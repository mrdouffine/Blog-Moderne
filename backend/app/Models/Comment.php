<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'article_id',
        'user_id',
        'content',
        'is_approved',
    ];

    /**
     * Les attributs et leurs castings.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Un commentaire appartient à un article.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Un commentaire appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Retourne uniquement les commentaires approuvés.
     *
     * @param Builder<Comment> $query
     * @return Builder<Comment>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    /**
     * Retourne uniquement les commentaires en attente de modération.
     *
     * @param Builder<Comment> $query
     * @return Builder<Comment>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Approuve le commentaire.
     */
    public function approve(): bool
    {
        return $this->update(['is_approved' => true]);
    }

    /**
     * Rejette (désapprouve) le commentaire.
     */
    public function reject(): bool
    {
        return $this->update(['is_approved' => false]);
    }
}
