<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService
    ) {}

    /**
     * Liste paginée des articles avec filtres optionnels.
     *
     * Query params acceptés : search, status, per_page, page
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'per_page', 'category']);

        $paginator = $this->articleService->getAllPaginated($filters);

        return response()->json([
            'success' => true,
            'data'    => ArticleResource::collection($paginator->items()),
            'message' => 'Articles récupérés.',
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ], 200);
    }

    /**
     * Retourne les articles ayant une image de couverture (admin — médiathèque).
     */
    public function mediasIndex(): JsonResponse
    {
        $articles = Article::whereNotNull('cover_image')
            ->select(['id', 'title', 'slug', 'cover_image'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $articles,
            'message' => 'Médias récupérés.',
        ], 200);
    }


    /**
     * Retourne le détail d'un article par son slug et incrémente le compteur de vues.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $article = $this->articleService->getBySlug($slug);

        return response()->json([
            'success' => true,
            'data'    => new ArticleResource($article),
            'message' => 'Article récupéré.',
        ], 200);
    }

    /**
     * Crée un nouvel article pour l'utilisateur authentifié.
     *
     * @param StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'data'    => new ArticleResource($article),
            'message' => 'Article créé avec succès.',
        ], 201);
    }

    /**
     * Met à jour un article existant. Vérifie que l'utilisateur est le propriétaire.
     *
     * @param UpdateArticleRequest $request
     * @param Article              $article
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        // Vérification de la propriété : seul l'auteur ou un admin peut modifier l'article
        if ($request->user()->id !== $article->user_id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $updated = $this->articleService->update($article, $request->validated());

        return response()->json([
            'success' => true,
            'data'    => new ArticleResource($updated),
            'message' => 'Article mis à jour.',
        ], 200);
    }

    /**
     * Supprime un article et ses médias associés. Vérifie la propriété.
     *
     * @param Request $request
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(Request $request, Article $article): JsonResponse
    {
        // Autorisé pour le propriétaire ou un administrateur
        if ($request->user()->id !== $article->user_id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $this->articleService->delete($article);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Article supprimé.',
        ], 200);
    }

    /**
     * Publie un article (status → published, published_at = maintenant).
     *
     * @param Request $request
     * @param Article $article
     * @return JsonResponse
     */
    public function publish(Request $request, Article $article): JsonResponse
    {
        // Autorisé pour le propriétaire ou un administrateur
        if ($request->user()->id !== $article->user_id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $published = $this->articleService->publish($article);

        return response()->json([
            'success' => true,
            'data'    => new ArticleResource($published),
            'message' => 'Article publié.',
        ], 200);
    }

    /**
     * Repasse un article en brouillon (status → draft, published_at = null).
     *
     * @param Request $request
     * @param Article $article
     * @return JsonResponse
     */
    public function draft(Request $request, Article $article): JsonResponse
    {
        // Autorisé pour le propriétaire ou un administrateur
        if ($request->user()->id !== $article->user_id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Action non autorisée.',
            ], 403);
        }

        $drafted = $this->articleService->draft($article);

        return response()->json([
            'success' => true,
            'data'    => new ArticleResource($drafted),
            'message' => 'Article repassé en brouillon.',
        ], 200);
    }
}
