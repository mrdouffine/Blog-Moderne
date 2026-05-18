<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Retourne la liste des commentaires approuvés d'un article donné.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function index(Article $article): JsonResponse
    {
        $comments = $article->comments()
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => CommentResource::collection($comments),
            'message' => 'Commentaires récupérés.',
        ], 200);
    }

    /**
     * Crée un nouveau commentaire associé à un article et à l'utilisateur authentifié.
     *
     * @param StoreCommentRequest $request
     * @param Article             $article
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, Article $article): JsonResponse
    {
        $content = $request->input('content') ?? $request->input('body');
        $user = $request->user();

        // Auto-approbation si l'utilisateur est admin ou auteur de l'article/plateforme
        $isApproved = in_array($user->role, ['admin', 'author']);

        $comment = $article->comments()->create([
            'user_id'     => $user->id,
            'content'     => $content,
            'is_approved' => $isApproved,
        ]);

        $comment->load('user');

        $message = $isApproved 
            ? 'Commentaire publié avec succès.' 
            : 'Commentaire soumis, en attente de modération.';

        return response()->json([
            'success' => true,
            'data'    => new CommentResource($comment),
            'message' => $message,
        ], 201);
    }

    /**
     * Met à jour un commentaire. Seul l'auteur peut modifier son propre commentaire.
     *
     * @param UpdateCommentRequest $request
     * @param Comment              $comment
     * @return JsonResponse
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        // Vérification de la propriété
        if ($user->id !== $comment->user_id) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $content = $request->input('content') ?? $request->input('body');

        // Repasser en attente de modération après modification, sauf si c'est un admin ou un auteur
        $isApproved = in_array($user->role, ['admin', 'author']);

        $comment->update([
            'content'     => $content,
            'is_approved' => $isApproved,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'data'    => new CommentResource($comment),
            'message' => 'Commentaire mis à jour.',
        ], 200);
    }

    /**
     * Supprime un commentaire. Autorisé pour l'auteur du commentaire ou un administrateur.
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $user = $request->user();

        // L'auteur peut supprimer son commentaire, l'admin peut supprimer n'importe lequel
        if ($user->id !== $comment->user_id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Commentaire supprimé.',
        ], 200);
    }

    /**
     * Approuve un commentaire (Administrateur uniquement).
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function approve(Request $request, Comment $comment): JsonResponse
    {
        // Seul l'administrateur peut approuver des commentaires
        if ($request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Accès réservé aux administrateurs.',
            ], 403);
        }

        $comment->update(['is_approved' => true]);
        $comment->load('user');

        return response()->json([
            'success' => true,
            'data'    => new CommentResource($comment),
            'message' => 'Commentaire approuvé avec succès.',
        ], 200);
    }
}
