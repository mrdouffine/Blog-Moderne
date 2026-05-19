<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int              $id
 * @property string           $title
 * @property string           $slug
 * @property string           $content
 * @property string|null      $excerpt
 * @property string           $status
 * @property string|null      $cover_image
 * @property int              $views_count
 * @property int              $comments_count
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon   $created_at
 * @property \Carbon\Carbon   $updated_at
 * @property \App\Models\User $user
 */
class ArticleResource extends JsonResource
{
    /**
     * Transforme le modèle Article en tableau JSON.
     * Les relations sont incluses seulement si elles ont été chargées (whenLoaded).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'excerpt'        => $this->excerpt,
            'content'        => $this->when(
                // Le contenu complet est inclus uniquement sur les requêtes de détail
                $request->routeIs('*.show') || $this->relationLoaded('comments'),
                $this->content ? (string) \Illuminate\Support\Str::markdown($this->content) : ''
            ),
            'status'         => $this->status,
            'category'       => $this->category ?? 'Général',
            'cover_image'    => $this->cover_image
                ? (filter_var($this->cover_image, FILTER_VALIDATE_URL) ? $this->cover_image : \Illuminate\Support\Facades\Storage::disk('public')->url($this->cover_image))
                : null,
            'views_count'    => $this->views_count,
            'comments_count' => $this->whenCounted('comments'),
            'published_at'   => $this->published_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),

            // Relations chargées à la demande
            'author'         => new UserResource($this->whenLoaded('user')),
            'comments'       => CommentResource::collection($this->whenLoaded('comments')),
            'media'          => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
