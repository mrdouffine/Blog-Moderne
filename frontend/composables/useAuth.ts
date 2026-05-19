// =============================================================================
// blog/frontend/composables/useAuth.ts
// Composable qui encapsule le store auth avec navigation automatique
// Retourne : user, isAuthenticated, isAdmin, isAuthor, loading, error
// + méthodes : login, register, logout, fetchMe
// =============================================================================

import { storeToRefs } from 'pinia'
import { navigateTo } from '#app'
import { useAuthStore } from '~/stores/auth'
import type { LoginCredentials, RegisterPayload } from '~/types'

/**
 * Composable d'authentification.
 *
 * Usage dans un composant :
 * ```ts
 * const { user, isAuthenticated, login, logout } = useAuth()
 * ```
 */
export const useAuth = () => {
  const store = useAuthStore()

  // Déstructuration réactive du store
  const { user, token, loading, error } = storeToRefs(store)

  // Getters réactifs (computed depuis le store)
  const isAuthenticated = computed(() => store.isAuthenticated)
  const isAdmin = computed(() => store.isAdmin)
  const isAuthor = computed(() => store.isAuthor)

  // ---------------------------------------------------------------------------
  // Actions avec navigation automatique
  // ---------------------------------------------------------------------------

  /**
   * Connecte l'utilisateur puis redirige vers la page d'accueil.
   * En cas d'erreur, celle-ci est propagée pour que le formulaire la gère.
   *
   * @param credentials - Email et mot de passe
   * @param redirectTo  - Route de redirection après succès (défaut : '/')
   */
  async function login(
    credentials: LoginCredentials,
    redirectTo?: string
  ): Promise<void> {
    await store.login(credentials)
    // Admins → /admin, autres → redirectTo ou accueil
    const dest = redirectTo || (store.isAdmin ? '/admin' : '/')
    await navigateTo(dest)
  }

  /**
   * Inscrit un nouvel utilisateur puis redirige.
   *
   * @param data       - Données d'inscription
   * @param redirectTo - Route de redirection après succès (défaut : '/')
   */
  async function register(
    data: RegisterPayload,
    redirectTo: string = '/'
  ): Promise<void> {
    await store.register(data)
    await navigateTo(redirectTo)
  }

  /**
   * Déconnecte l'utilisateur et redirige vers la page de login.
   *
   * @param redirectTo - Route de redirection après déconnexion (défaut : '/auth/login')
   */
  async function logout(redirectTo: string = '/auth/login'): Promise<void> {
    await store.logout()
    await navigateTo(redirectTo)
  }

  /**
   * Récupère le profil de l'utilisateur connecté.
   * Wrapper direct de l'action du store.
   */
  async function fetchMe(): Promise<void> {
    await store.fetchMe()
  }

  /**
   * Initialise l'état d'auth (lecture cookie + fetchMe).
   * À appeler au démarrage si on ne l'a pas fait via un plugin.
   */
  async function initAuth(): Promise<void> {
    await store.initAuth()
  }

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  /**
   * Vide l'erreur courante du store.
   * Utile après qu'un formulaire ait affiché le message.
   */
  function clearError(): void {
    store.error = null
  }

  return {
    // State (réactif)
    user,
    token,
    loading,
    error,

    // Getters (computed)
    isAuthenticated,
    isAdmin,
    isAuthor,

    // Actions
    login,
    register,
    logout,
    fetchMe,
    initAuth,
    clearError,
  }
}
