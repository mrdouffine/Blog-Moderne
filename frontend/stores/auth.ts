// =============================================================================
// blog/frontend/stores/auth.ts
// Store Pinia pour l'authentification utilisateur
// - Gestion du token via cookie 'auth_token'
// - Actions : register, login, logout, fetchMe, initAuth
// - Getters : isAuthenticated, isAdmin, isAuthor
// =============================================================================

import { defineStore } from 'pinia'
import { useCookie, useNuxtApp } from '#app'
import type {
  User,
  AuthState,
  LoginCredentials,
  RegisterPayload,
  ApiResponse,
} from '~/types'

/** Durée de vie du cookie d'auth (7 jours en secondes) */
const COOKIE_MAX_AGE = 60 * 60 * 24 * 7

export const useAuthStore = defineStore('auth', {
  // ---------------------------------------------------------------------------
  // State
  // ---------------------------------------------------------------------------
  state: (): AuthState => ({
    user: null,
    token: null,
    loading: false,
    error: null,
  }),

  // ---------------------------------------------------------------------------
  // Getters
  // ---------------------------------------------------------------------------
  getters: {
    /** Vrai si un utilisateur est connecté et qu'un token est présent */
    isAuthenticated: (state): boolean => !!state.user && !!state.token,

    /** Vrai si l'utilisateur connecté a le rôle 'admin' */
    isAdmin: (state): boolean => state.user?.role === 'admin',

    /** Vrai si l'utilisateur connecté a le rôle 'author' ou supérieur */
    isAuthor: (state): boolean =>
      state.user?.role === 'author' || state.user?.role === 'admin',
  },

  // ---------------------------------------------------------------------------
  // Actions
  // ---------------------------------------------------------------------------
  actions: {
    // -------------------------------------------------------------------------
    // Helpers privés
    // -------------------------------------------------------------------------

    /**
     * Persiste le token dans le cookie httpOnly et dans le state.
     * @param token - Jeton Bearer reçu de l'API
     */
    _setToken(token: string): void {
      this.token = token
      const cookie = useCookie<string>('auth_token', {
        maxAge: COOKIE_MAX_AGE,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'lax',
        // httpOnly ne peut être positionné que côté serveur ;
        // ici on laisse JS y accéder pour pouvoir le lire dans le plugin
      })
      cookie.value = token
    },

    /**
     * Supprime le token du cookie et du state.
     */
    _clearToken(): void {
      this.token = null
      this.user = null
      const cookie = useCookie('auth_token')
      cookie.value = null
    },

    /**
     * Récupère l'instance $api exposée par le plugin.
     */
    _api() {
      const { $api } = useNuxtApp()
      return $api as typeof $fetch
    },

    // -------------------------------------------------------------------------
    // Actions publiques
    // -------------------------------------------------------------------------

    /**
     * Inscrit un nouvel utilisateur et stocke le token retourné.
     * @param data - Données d'inscription (name, email, password, confirmation)
     * @throws {Error} En cas d'échec (validation, email déjà pris, etc.)
     */
    async register(data: RegisterPayload): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<{ token: string; user: User }>>(
          '/auth/register',
          {
            method: 'POST',
            body: data,
          }
        )

        this._setToken(response.data.token)
        this.user = response.data.user
      } catch (err: unknown) {
        this.error = _extractErrorMessage(err, "Erreur lors de l'inscription")
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Connecte un utilisateur existant avec ses identifiants.
     * @param credentials - Email et mot de passe
     * @throws {Error} En cas d'identifiants invalides
     */
    async login(credentials: LoginCredentials): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<{ token: string; user: User }>>(
          '/auth/login',
          {
            method: 'POST',
            body: credentials,
          }
        )

        this._setToken(response.data.token)
        this.user = response.data.user
      } catch (err: unknown) {
        this.error = _extractErrorMessage(err, 'Identifiants invalides')
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Déconnecte l'utilisateur courant.
     * Appelle POST /auth/logout pour invalider le token côté serveur,
     * puis vide le state et le cookie.
     */
    async logout(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        // Tentative de révocation côté serveur (ignore les erreurs réseau)
        if (this.token) {
          await this._api()('/auth/logout', { method: 'POST' }).catch(() => {
            // Silencieux : on nettoie localement même si le serveur échoue
          })
        }
      } finally {
        this._clearToken()
        this.loading = false
      }
    },

    /**
     * Récupère les informations de l'utilisateur actuellement connecté.
     * Hydrate `state.user` depuis GET /auth/me.
     * @throws {Error} Si la requête échoue (token invalide, réseau, etc.)
     */
    async fetchMe(): Promise<void> {
      this.loading = true
      this.error = null

      try {
        const options: any = {}
        if (this.token) {
          options.headers = { Authorization: `Bearer ${this.token}` }
        }
        const response = await this._api()<ApiResponse<User>>('/auth/me', options)
        this.user = response.data
      } catch (err: unknown) {
        this.error = _extractErrorMessage(err, 'Impossible de récupérer le profil')
        // On nettoie si le token est invalide (401 déjà géré par le plugin)
        this._clearToken()
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Initialise l'authentification au démarrage de l'application.
     * Lit le token depuis le cookie et tente de récupérer le profil utilisateur.
     * Doit être appelé dans un plugin ou app.vue (onMounted / callOnce).
     */
    async initAuth(): Promise<void> {
      const cookie = useCookie<string | null>('auth_token')
      const storedToken = cookie.value

      if (!storedToken) {
        // Pas de token → pas de session
        return
      }

      // Restaurer le token en mémoire avant d'appeler l'API
      this.token = storedToken

      try {
        await this.fetchMe()
      } catch {
        // fetchMe a déjà nettoyé le state si 401
      }
    },
  },
})

// =============================================================================
// Helpers module-level (non exportés)
// =============================================================================

/**
 * Extrait un message d'erreur lisible depuis une exception inconnue.
 * Compatible avec les erreurs ofetch (FetchError) et les erreurs standard.
 */
function _extractErrorMessage(err: unknown, fallback: string): string {
  if (err && typeof err === 'object') {
    // Erreur ofetch avec corps JSON (ex: validation Laravel 422)
    if ('data' in err) {
      const data = (err as { data?: { message?: string } }).data
      if (data?.message) return data.message
    }
    if ('message' in err) {
      return (err as { message: string }).message
    }
  }
  return fallback
}
