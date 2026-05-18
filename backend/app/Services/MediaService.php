<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    /**
     * Uploade un fichier et crée l'entrée correspondante en base de données.
     *
     * Le fichier est stocké dans storage/app/public/media/{year}/{month}/.
     *
     * @param UploadedFile $file      Fichier à uploader
     * @param User         $user      Propriétaire du média
     * @param int|null     $articleId ID de l'article associé (optionnel)
     * @return Media
     */
    public function upload(UploadedFile $file, User $user, ?int $articleId = null): Media
    {
        $year  = now()->format('Y');
        $month = now()->format('m');

        // Stockage dans le dossier organisé par année/mois
        $path = $file->store("media/{$year}/{$month}", 'public');

        // Récupération de l'URL publique accessible
        $url = Storage::disk('public')->url($path);

        return Media::create([
            'user_id'    => $user->id,
            'article_id' => $articleId,
            'filename'   => $file->getClientOriginalName(),
            'path'       => $path,
            'url'        => $url,
            'mime_type'  => $file->getMimeType(),
            'size'       => $file->getSize(),
            'disk'       => 'public',
        ]);
    }

    /**
     * Supprime un média : fichier physique sur le disque et entrée en base de données.
     *
     * @param Media $media
     * @return bool
     */
    public function delete(Media $media): bool
    {
        // Suppression du fichier physique s'il existe
        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        return $media->delete();
    }

    /**
     * Récupère tous les médias associés à un article donné.
     *
     * @param int $articleId
     * @return Collection<int, Media>
     */
    public function getForArticle(int $articleId): Collection
    {
        return Media::query()
            ->where('article_id', $articleId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
