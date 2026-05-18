// =============================================================================
// blog/frontend/middleware/auth.ts
// Middleware de protection des routes authentifiées
// - Vérifie la présence d'un token et d'un user dans le store
// - Initialise l'auth si nécessaire (token cookie présent mais store vide)
// - Redirige vers /auth/login si non authentifié
//
// Usage dans une page :
//   definePageMeta({ middleware: 'auth' })
// =============================================================================

import { defineNuxtRouteMiddleware, navigateTo, useCookie } from '#app'
import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()

  // 1. Si le store indique déjà que l'utilisateur est authentifié → OK
  if (authStore.isAuthenticated) {
    return
  }

  // 2. Vérifier si un token existe dans le cookie
  //    (cas SSR ou rechargement de page où le store est vide)
  const tokenCookie = useCookie<string | null>('auth_token')

  if (!tokenCookie.value) {
    // Aucun token → rediriger vers login en conservant l'URL de destination
    return navigateTo({
      path: '/auth/login',
      query: {
        redirect: to.fullPath !== '/auth/login' ? to.fullPath : undefined,
      },
    })
  }

  // 3. Un token existe mais le store est vide → hydrater via fetchMe
  //    (typique lors du premier rendu SSR ou d'un rechargement navigateur)
  try {
    await authStore.initAuth()
  } catch {
    // initAuth a déjà nettoyé le cookie et le state en cas d'erreur 401
  }

  // 4. Re-vérifier après initAuth
  if (!authStore.isAuthenticated) {
    return navigateTo({
      path: '/auth/login',
      query: {
        redirect: to.fullPath !== '/auth/login' ? to.fullPath : undefined,
      },
    })
  }
})
