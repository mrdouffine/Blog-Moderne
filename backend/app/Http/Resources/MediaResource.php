<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int            $id
 * @property int            $user_id
 * @property int|null       $article_id
 * @property string         $filename
 * @property string         $path
 * @property string         $url
 * @property string         $mime_type
 * @property int            $size
 * @property string         $disk
 * @property \Carbon\Carbon $created_at
 */
class MediaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'article_id' => $this->article_id,
            'filename'   => $this->filename,
            'url'        => $this->url,
            'mime_type'  => $this->mime_type,
            'size'       => $this->size,       // Taille en octets
            'size_human' => $this->formatSize($this->size),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Formate la taille du fichier en unité lisible (KB, MB, GB).
     *
     * @param int $bytes
     * @return string
     */
    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return "{$bytes} B";
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }

        if ($bytes < 1073741824) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        return round($bytes / 1073741824, 1) . ' GB';
    }
}
