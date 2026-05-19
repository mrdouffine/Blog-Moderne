<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Les attributs assignables en masse.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'provider_name',
        'provider_id',
    ];

    /**
     * Les attributs masqués dans les sérialisations.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs et leurs castings.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Un utilisateur peut rédiger plusieurs articles.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Un utilisateur peut poster plusieurs commentaires.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filtre les utilisateurs ayant le rôle "admin".
     *
     * @param Builder<User> $query
     * @return Builder<User>
     */
    public function scopeIsAdmin(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    /**
     * Filtre les utilisateurs ayant le rôle "author".
     *
     * @param Builder<User> $query
     * @return Builder<User>
     */
    public function scopeIsAuthor(Builder $query): Builder
    {
        return $query->where('role', 'author');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est auteur.
     */
    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    /**
     * Vérifie si l'utilisateur peut rédiger des articles (admin ou author).
     */
    public function canWrite(): bool
    {
        return in_array($this->role, ['admin', 'author'], strict: true);
    }
}
