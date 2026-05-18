<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Article;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {}

    /**
     * Uploade un fichier média et crée l'entrée en base de données.
     *
     * @param UploadMediaRequest $request
     * @return JsonResponse
     */
    public function upload(UploadMediaRequest $request): JsonResponse
    {
        $articleId = $request->input('article_id');

        $media = $this->mediaService->upload(
            $request->file('file'),
            $request->user(),
            $articleId ? (int) $articleId : null
        );

        return response()->json([
            'success' => true,
            'data'    => new MediaResource($media),
            'message' => 'Fichier uploadé avec succès.',
        ], 201);
    }

    /**
     * Supprime un média (fichier physique + entrée DB). Vérifie la propriété.
     *
     * @param Request $request
     * @param Media   $media
     * @return JsonResponse
     */
    public function destroy(Request $request, Media $media): JsonResponse
    {
        $user = $request->user();

        // Seul le propriétaire ou un admin peut supprimer le média
        if ($user->id !== $media->user_id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $this->mediaService->delete($media);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Média supprimé.',
        ], 200);
    }

    /**
     * Retourne tous les médias associés à un article.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function getForArticle(Article $article): JsonResponse
    {
        $media = $this->mediaService->getForArticle($article->id);

        return response()->json([
            'success' => true,
            'data'    => MediaResource::collection($media),
            'message' => 'Médias récupérés.',
        ], 200);
    }
}
