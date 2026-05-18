<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'token',
        'subscribed_at',
        'unsubscribed_at',
    ];

    /**
     * Les attributs et leurs castings.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscribed_at'   => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Boot — génération automatique du token UUID
    // -------------------------------------------------------------------------

    /**
     * Génère automatiquement un token UUID unique et définit subscribed_at.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Subscriber $subscriber): void {
            if (empty($subscriber->token)) {
                $subscriber->token = (string) Str::uuid();
            }

            if (empty($subscriber->subscribed_at)) {
                $subscriber->subscribed_at = now();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Retourne uniquement les abonnés actifs (non désinscrits).
     *
     * @param Builder<Subscriber> $query
     * @return Builder<Subscriber>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('unsubscribed_at');
    }

    /**
     * Retourne uniquement les abonnés désinscrits.
     *
     * @param Builder<Subscriber> $query
     * @return Builder<Subscriber>
     */
    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->whereNotNull('unsubscribed_at');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Vérifie si l'abonné est actuellement actif.
     */
    public function isActive(): bool
    {
        return $this->unsubscribed_at === null;
    }

    /**
     * Désinscrit l'abonné en enregistrant la date de désinscription.
     */
    public function unsubscribe(): bool
    {
        return $this->update(['unsubscribed_at' => now()]);
    }

    /**
     * Réinscrit un abonné désinscrit.
     */
    public function resubscribe(): bool
    {
        return $this->update([
            'subscribed_at'   => now(),
            'unsubscribed_at' => null,
            'token'           => (string) Str::uuid(),
        ]);
    }
}
