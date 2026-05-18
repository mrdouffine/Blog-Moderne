<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
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
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * Les attributs et leurs castings.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    /**
     * Un média appartient optionnellement à un article.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Un média appartient à un utilisateur (uploader).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Accesseurs
    // -------------------------------------------------------------------------

    /**
     * Retourne l'URL publique complète du fichier via le disk Storage configuré.
     *
     * @return string URL publique du fichier
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Retourne la taille formatée en unité lisible (B, KB, MB, GB).
     *
     * @param int $precision Nombre de décimales
     */
    public function formattedSize(int $precision = 2): string
    {
        $bytes = $this->size;

        return match (true) {
            $bytes >= 1_073_741_824 => round($bytes / 1_073_741_824, $precision) . ' GB',
            $bytes >= 1_048_576     => round($bytes / 1_048_576, $precision) . ' MB',
            $bytes >= 1_024         => round($bytes / 1_024, $precision) . ' KB',
            default                 => "{$bytes} B",
        };
    }

    /**
     * Vérifie si le fichier est une image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Supprime le fichier physique du storage puis le modèle.
     */
    public function deleteWithFile(): bool
    {
        Storage::delete($this->path);

        return $this->delete();
    }
}
