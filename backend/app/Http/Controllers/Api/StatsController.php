<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    /**
     * Retourne les statistiques globales pour le dashboard admin.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'articles_count'    => Article::count(),
                'comments_count'    => Comment::count(),
                'subscribers_count' => Subscriber::count(),
                'total_views'       => Article::sum('views_count'),
            ],
            'message' => 'Statistiques récupérées avec succès.',
        ], 200);
    }
}
