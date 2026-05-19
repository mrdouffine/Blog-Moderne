<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'category',
        'status',
        'views_count',
        'published_at',
    ];

    /**
     * Les attributs et leurs castings.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views_count'  => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot — génération automatique du slug
    // -------------------------------------------------------------------------

    /**
     * Génère automatiquement un slug unique à partir du titre lors de la création.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Article $article): void {
            if (empty($article->slug)) {
                $article->slug = static::generateUniqueSlug($article->title);
            }
        });

        static::updating(function (Article $article): void {
            if ($article->isDirty('title') && ! $article->isDirty('slug')) {
                $article->slug = static::generateUniqueSlug($article->title, $article->id);
            }
        });
    }

    /**
     * Génère un slug unique en ajoutant un suffixe numérique si nécessaire.
     *
     * @param string   $title Titre source
     * @param int|null $exceptId ID à exclure lors de la vérification d'unicité (update)
     */
    protected static function generateUniqueSlug(string $title, ?int $exceptId = null): string
    {
        $slug      = Str::slug($title);
        $original  = $slug;
        $count     = 1;

        while (
            static::where('slug', $slug)
                ->when($exceptId, fn (Builder $q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * L'article appartient à un auteur (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un article possède plusieurs commentaires.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Un article possède plusieurs médias.
     */
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Retourne uniquement les articles publiés.
     *
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Retourne uniquement les articles en brouillon.
     *
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Trie par date de publication décroissante (plus récent en premier).
     *
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Trie par nombre de vues décroissant (plus populaire en premier).
     *
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * Recherche fulltext sur le titre, l'extrait et le contenu.
     *
     * @param Builder<Article> $query
     * @param string           $search Terme de recherche
     * @return Builder<Article>
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        $term = "%{$search}%";

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', $term)
              ->orWhere('excerpt', 'like', $term)
              ->orWhere('content', 'like', $term);
        });
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    /**
     * Calcule le temps de lecture estimé en minutes.
     * Basé sur une vitesse moyenne de 200 mots par minute.
     *
     * @return int Temps de lecture en minutes (minimum 1)
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount    = str_word_count(strip_tags($this->content));
        $wordsPerMin  = 200;
        $minutes      = (int) ceil($wordCount / $wordsPerMin);

        return max(1, $minutes);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Incrémente le compteur de vues de l'article.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Vérifie si l'article est publié.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }
}
