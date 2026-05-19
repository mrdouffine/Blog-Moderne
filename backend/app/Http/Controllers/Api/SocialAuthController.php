<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Obtenir l'URL de redirection OAuth pour le provider donné.
     *
     * @param string $provider (google, github)
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'github'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Provider non supporté.',
            ], 400);
        }

        try {
            $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            return response()->json([
                'success' => true,
                'url'     => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer l\'URL de redirection : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gérer le callback renvoyé par le provider OAuth.
     * Authentifie l'utilisateur et redirige vers le frontend Nuxt avec le token.
     *
     * @param string $provider (google, github)
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');

        if (!in_array($provider, ['google', 'github'], true)) {
            return redirect()->away($frontendUrl . '/auth/login?error=provider_unsupported');
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->away($frontendUrl . '/auth/login?error=oauth_failed');
        }

        // Trouver ou créer l'utilisateur
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Associer le provider s'il ne l'est pas encore
            if (!$user->provider_name) {
                $user->update([
                    'provider_name' => $provider,
                    'provider_id'   => $socialUser->getId(),
                    'avatar'        => $user->avatar ?? $socialUser->getAvatar(),
                ]);
            }
        } else {
            // Créer un nouvel utilisateur avec mot de passe aléatoire
            $user = User::create([
                'name'          => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Utilisateur ' . Str::random(5),
                'email'         => $socialUser->getEmail(),
                'password'      => bcrypt(Str::random(24)),
                'role'          => 'reader', // Rôle par défaut
                'avatar'        => $socialUser->getAvatar(),
                'provider_name' => $provider,
                'provider_id'   => $socialUser->getId(),
                'email_verified_at' => now(), // OAuth valide déjà l'email
            ]);
        }

        // Créer un token Sanctum pour l'API
        $token = $user->createToken('social-auth-token')->plainTextToken;

        // Rediriger vers le frontend avec le token
        return redirect()->away($frontendUrl . '/auth/callback?token=' . urlencode($token));
    }
}
