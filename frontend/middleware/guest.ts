// =============================================================================
// blog/frontend/middleware/guest.ts
// Middleware pour les routes réservées aux visiteurs non connectés
// - Si l'utilisateur est déjà authentifié, redirige vers /
// - Utilisé sur les pages login et register
//
// Usage dans une page :
//   definePageMeta({ middleware: 'guest' })
// =============================================================================

import { defineNuxtRouteMiddleware, navigateTo, useCookie } from '#app'
import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  // 1. Si le store signale déjà une session active → rediriger
  if (authStore.isAuthenticated) {
    return navigateTo('/')
  }

  // 2. Vérifier le cookie pour les cas SSR / rechargement
  const tokenCookie = useCookie<string | null>('auth_token')

  if (!tokenCookie.value) {
    // Pas de token → l'utilisateur peut accéder à la page guest
    return
  }

  // 3. Token présent mais store non initialisé → tenter de récupérer l'user
  try {
    await authStore.initAuth()
  } catch {
    // Erreur d'hydratation → laisser l'accès (token probablement invalide)
    return
  }

  // 4. Si après initAuth l'utilisateur est authentifié → rediriger vers /
  if (authStore.isAuthenticated) {
    return navigateTo('/')
  }
})
