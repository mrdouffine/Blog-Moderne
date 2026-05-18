<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * Récupère la liste paginée des articles avec filtres optionnels.
     *
     * @param array $filters Filtres disponibles : status, search, per_page
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(array $filters): LengthAwarePaginator
    {
        $query = Article::query()
            ->with('user')
            ->withCount('comments');

        // Filtre par statut (published, draft, archived)
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Recherche full-text sur le titre et le contenu
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Tri par date de publication décroissante par défaut
        $query->latest('published_at');

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
    }

    /**
     * Récupère un article par son slug et incrémente le compteur de vues.
     *
     * @param string $slug
     * @return Article
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getBySlug(string $slug): Article
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->with(['user', 'comments.user', 'media'])
            ->firstOrFail();

        // Incrémenter le compteur de vues de manière atomique
        $article->increment('views_count');

        return $article;
    }

    /**
     * Crée un nouvel article et gère l'upload de l'image de couverture.
     *
     * @param array $data Données de l'article
     * @param User  $user Auteur de l'article
     * @return Article
     */
    public function create(array $data, User $user): Article
    {
        // Génération d'un slug unique à partir du titre
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['user_id'] = $user->id;
        $data['status'] = $data['status'] ?? 'draft';

        // Gestion de l'upload de l'image de couverture
        if (!empty($data['cover_image']) && $data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['cover_image'] = $this->uploadCoverImage($data['cover_image']);
        }

        // Définir published_at si le statut est published
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return Article::create($data);
    }

    /**
     * Met à jour un article existant et gère le remplacement de l'image de couverture.
     *
     * @param Article $article Article à mettre à jour
     * @param array   $data    Nouvelles données
     * @return Article
     */
    public function update(Article $article, array $data): Article
    {
        // Mise à jour du slug si le titre change
        if (!empty($data['title']) && $data['title'] !== $article->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
        }

        // Remplacement de l'image de couverture existante
        if (!empty($data['cover_image']) && $data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
            // Supprimer l'ancienne image si elle existe
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $data['cover_image'] = $this->uploadCoverImage($data['cover_image']);
        }

        // Gestion automatique de published_at lors d'un passage en published
        if (
            isset($data['status']) &&
            $data['status'] === 'published' &&
            $article->status !== 'published'
        ) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $article->update($data);

        return $article->fresh(['user', 'comments', 'media']);
    }

    /**
     * Supprime un article et tous ses médias associés (fichiers + entrées DB).
     *
     * @param Article $article
     * @return bool
     */
    public function delete(Article $article): bool
    {
        // Supprimer les fichiers physiques des médias liés
        foreach ($article->media as $media) {
            Storage::disk('public')->delete($media->path);
        }

        // Supprimer l'image de couverture si elle existe
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }

        return $article->delete();
    }

    /**
     * Publie un article en changeant son statut et en définissant published_at.
     *
     * @param Article $article
     * @return Article
     */
    public function publish(Article $article): Article
    {
        $article->update([
            'status'       => 'published',
            'published_at' => $article->published_at ?? now(),
        ]);

        return $article->fresh();
    }

    /**
     * Repasse un article en brouillon et efface sa date de publication.
     *
     * @param Article $article
     * @return Article
     */
    public function draft(Article $article): Article
    {
        $article->update([
            'status'       => 'draft',
            'published_at' => null,
        ]);

        return $article->fresh();
    }

    // -------------------------------------------------------------------------
    // Méthodes privées utilitaires
    // -------------------------------------------------------------------------

    /**
     * Génère un slug unique à partir d'un titre en vérifiant les collisions en BDD.
     *
     * @param string   $title     Titre source
     * @param int|null $excludeId ID de l'article à exclure (utile pour les updates)
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (true) {
            $query = Article::where('slug', $slug);

            if ($excludeId !== null) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Stocke l'image de couverture sur le disque public et retourne son chemin.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Chemin relatif du fichier stocké
     */
    private function uploadCoverImage(\Illuminate\Http\UploadedFile $file): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');

        return $file->store("covers/{$year}/{$month}", 'public');
    }
}
