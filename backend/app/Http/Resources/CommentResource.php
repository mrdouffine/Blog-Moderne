<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int            $id
 * @property int            $article_id
 * @property int            $user_id
 * @property string         $body
 * @property string         $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'article_id'  => $this->article_id,
            'content'     => $this->content,
            'body'        => $this->content, // Fallback pour la compatibilité avec body
            'is_approved' => (bool) $this->is_approved,
            'status'      => $this->is_approved ? 'approved' : 'pending', // Fallback pour la compatibilité avec status
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),

            // Auteur chargé à la demande
            'author'      => new UserResource($this->whenLoaded('user')),
        ];
    }
}
