<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Vérifie que l'utilisateur authentifié possède le rôle 'admin'.
     *
     * Ce middleware doit être utilisé après le middleware 'auth:sanctum'
     * pour s'assurer que $request->user() est bien disponible.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérification que l'utilisateur est authentifié (sécurité supplémentaire)
        if (!$user) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Non authentifié.',
            ], 401);
        }

        // Vérification du rôle admin
        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'data'    => null,
                'message' => 'Accès réservé aux administrateurs.',
            ], 403);
        }

        return $next($request);
    }
}
