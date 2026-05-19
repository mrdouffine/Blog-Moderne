// =============================================================================
// blog/frontend/plugins/api.ts
// Plugin Nuxt qui expose une instance $fetch préconfigurée ($api)
// - Base URL depuis runtimeConfig
// - Injection automatique du Bearer token (cookie auth_token)
// - Gestion centralisée des erreurs 401 / 403
// =============================================================================

import { defineNuxtPlugin, useRuntimeConfig, useCookie, navigateTo } from '#app'
import { FetchError } from 'ofetch'

export default defineNuxtPlugin((nuxtApp) => {
  const config = useRuntimeConfig()

  /**
   * Retourne le token stocké dans le cookie httpOnly `auth_token`.
   * Fonctionne côté serveur (SSR) et côté client.
   */
  const getToken = (): string | null => {
    const cookie = useCookie<string | null>('auth_token')
    return cookie.value ?? null
  }

  /**
   * Instance $fetch configurée avec :
   *  - baseURL pointant vers l'API Laravel
   *  - headers Accept / Content-Type JSON
   *  - Intercepteur `onRequest`  → attache le Bearer token
   *  - Intercepteur `onResponseError` → gère 401 et 403
   */
  const api = $fetch.create({
    baseURL: import.meta.server ? config.apiUrl : (config.public.apiUrl as string),

    // --- Intercepteur requête -----------------------------------------------
    onRequest({ options }) {
      const token = getToken()

      // Initialiser les headers si absents
      const headers = new Headers(options.headers as HeadersInit | undefined)

      headers.set('Accept', 'application/json')

      // Ne pas écraser Content-Type si c'est un FormData (multipart)
      if (!(options.body instanceof FormData)) {
        headers.set('Content-Type', 'application/json')
      }

      // Attacher le token d'authentification
      if (token) {
        headers.set('Authorization', `Bearer ${token}`)
      }

      options.headers = headers
    },

    // --- Intercepteur réponse (erreurs) -------------------------------------
    async onResponseError({ response }) {
      const status = response.status

      if (status === 401) {
        // Token expiré ou invalide → vider le cookie et rediriger vers login
        const authCookie = useCookie('auth_token')
        authCookie.value = null

        await navigateTo('/auth/login')
        return
      }

      if (status === 403) {
        // Accès interdit → rediriger vers l'accueil
        await navigateTo('/')
        return
      }
    },
  })

  // Exposer $api dans toute l'application (composables, pages, composants)
  return {
    provide: {
      api,
    },
  }
})
