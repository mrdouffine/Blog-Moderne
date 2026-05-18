<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Le type de resource utilisé pour chaque élément de la collection.
     *
     * @var class-string<\Illuminate\Http\Resources\Json\JsonResource>
     */
    public $collects = ArticleResource::class;

    /**
     * Transforme la collection d'articles paginée en tableau pour la réponse API.
     * Inclut les meta-données de pagination dans la réponse standardisée.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data'    => $this->collection,
            'message' => 'Liste des articles récupérée avec succès.',
            'meta'    => [
                'current_page' => $this->resource->currentPage(),
                'last_page'    => $this->resource->lastPage(),
                'per_page'     => $this->resource->perPage(),
                'total'        => $this->resource->total(),
                'from'         => $this->resource->firstItem(),
                'to'           => $this->resource->lastItem(),
                'path'         => $this->resource->path(),
            ],
            'links'   => [
                'first' => $this->resource->url(1),
                'last'  => $this->resource->url($this->resource->lastPage()),
                'prev'  => $this->resource->previousPageUrl(),
                'next'  => $this->resource->nextPageUrl(),
            ],
        ];
    }
}
