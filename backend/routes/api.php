<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Blog Platform
|--------------------------------------------------------------------------
|
| Toutes les routes sont préfixées par /api (configuré dans bootstrap/app.php
| ou RouteServiceProvider selon la version de Laravel).
|
*/

// =============================================================================
// AUTH
// =============================================================================
Route::prefix('auth')->group(function () {
    // Inscription d'un nouvel utilisateur
    Route::post('/register', [AuthController::class, 'register']);

    // Connexion et récupération du token
    Route::post('/login', [AuthController::class, 'login']);

    // Routes protégées par Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Déconnexion (révocation du token courant)
        Route::post('/logout', [AuthController::class, 'logout']);

        // Profil de l'utilisateur connecté
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// =============================================================================
// ARTICLES
// =============================================================================
Route::prefix('articles')->group(function () {
    // Routes publiques
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{slug}', [ArticleController::class, 'show']);

    // Routes protégées par authentification
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ArticleController::class, 'store']);
        Route::put('/{article}', [ArticleController::class, 'update']);
        Route::delete('/{article}', [ArticleController::class, 'destroy']);

        // Actions de workflow sur le statut
        Route::patch('/{article}/publish', [ArticleController::class, 'publish']);
        Route::patch('/{article}/draft', [ArticleController::class, 'draft']);
    });

    // Médias d'un article (protégé)
    Route::middleware('auth:sanctum')
        ->get('/{article}/media', [MediaController::class, 'getForArticle']);

    // Commentaires d'un article
    Route::get('/{article}/comments', [CommentController::class, 'index']);
    Route::middleware('auth:sanctum')
        ->post('/{article}/comments', [CommentController::class, 'store']);
});

// =============================================================================
// COMMENTS (actions indépendantes de l'article)
// =============================================================================
Route::prefix('comments')->middleware('auth:sanctum')->group(function () {
    Route::put('/{comment}', [CommentController::class, 'update']);
    Route::delete('/{comment}', [CommentController::class, 'destroy']);
    Route::patch('/{comment}/approve', [CommentController::class, 'approve']);
});

// =============================================================================
// MEDIA
// =============================================================================
Route::prefix('media')->middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [MediaController::class, 'upload']);
    Route::delete('/{media}', [MediaController::class, 'destroy']);
});

// =============================================================================
// NEWSLETTER
// =============================================================================
Route::prefix('newsletter')->group(function () {
    // Routes publiques : abonnement / désabonnement
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);

    // Route admin : liste des abonnés
    Route::middleware(['auth:sanctum', EnsureUserIsAdmin::class])
        ->get('/subscribers', [NewsletterController::class, 'subscribers']);
});
