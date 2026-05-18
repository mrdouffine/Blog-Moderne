// =============================================================================
// blog/frontend/composables/useNewsletter.ts
// Composable pour la gestion de la newsletter
// - subscribe / unsubscribe (public)
// - fetchSubscribers (admin)
// =============================================================================

import { useNuxtApp } from '#app'
import { useUiStore } from '~/stores/ui'
import type {
  Subscriber,
  ApiResponse,
  PaginatedResponse,
  PaginationMeta,
  SubscriberFilters,
} from '~/types'

/**
 * Composable newsletter.
 *
 * Usage (widget d'abonnement) :
 * ```ts
 * const { loading, success, subscribe } = useNewsletter()
 * await subscribe('user@example.com')
 * ```
 *
 * Usage (admin) :
 * ```ts
 * const { subscribers, fetchSubscribers } = useNewsletter()
 * await fetchSubscribers({ is_active: true, per_page: 20 })
 * ```
 */
export const useNewsletter = () => {
  const { $api } = useNuxtApp()
  const api = $api as typeof $fetch
  const ui = useUiStore()

  // ---------------------------------------------------------------------------
  // State local
  // ---------------------------------------------------------------------------
  const loading = ref<boolean>(false)
  const error = ref<string | null>(null)
  const success = ref<boolean>(false)

  /** Liste des abonnés (admin seulement) */
  const subscribers = ref<Subscriber[]>([])
  const pagination = ref<PaginationMeta | null>(null)

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  function setError(err: unknown, fallback: string): void {
    if (err && typeof err === 'object' && 'data' in err) {
      const data = (err as { data?: { message?: string } }).data
      error.value = data?.message ?? fallback
    } else if (err instanceof Error) {
      error.value = err.message
    } else {
      error.value = fallback
    }
  }

  /** Valide le format d'une adresse email */
  function isValidEmail(email: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
  }

  /** Réinitialise les indicateurs de résultat */
  function reset(): void {
    error.value = null
    success.value = false
  }

  // ---------------------------------------------------------------------------
  // Actions publiques
  // ---------------------------------------------------------------------------

  /**
   * Abonne une adresse email à la newsletter.
   *
   * @param email - Adresse email à abonner
   * @returns true si l'abonnement a réussi, false sinon
   */
  async function subscribe(email: string): Promise<boolean> {
    reset()

    if (!isValidEmail(email)) {
      error.value = 'Adresse email invalide.'
      ui.error('Veuillez entrer une adresse email valide.')
      return false
    }

    loading.value = true

    try {
      await api<ApiResponse<{ message: string }>>(
        '/newsletter/subscribe',
        {
          method: 'POST',
          body: { email },
        }
      )

      success.value = true
      ui.success('Inscription réussie ! Merci de votre intérêt.')
      return true
    } catch (err: unknown) {
      // Gérer le cas "déjà abonné" (ex: 409 Conflict de Laravel)
      if (
        err &&
        typeof err === 'object' &&
        'status' in err &&
        (err as { status: number }).status === 409
      ) {
        error.value = 'Cette adresse email est déjà abonnée.'
        ui.warning('Cette adresse email est déjà abonnée à la newsletter.')
      } else {
        setError(err, "Impossible de s'abonner à la newsletter")
        ui.error("Impossible de s'abonner. Veuillez réessayer.")
      }
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Désabonne une adresse email de la newsletter.
   *
   * @param email - Adresse email à désabonner
   * @returns true si le désabonnement a réussi, false sinon
   */
  async function unsubscribe(email: string): Promise<boolean> {
    reset()

    if (!isValidEmail(email)) {
      error.value = 'Adresse email invalide.'
      ui.error('Veuillez entrer une adresse email valide.')
      return false
    }

    loading.value = true

    try {
      await api<ApiResponse<{ message: string }>>(
        '/newsletter/unsubscribe',
        {
          method: 'POST',
          body: { email },
        }
      )

      success.value = true
      ui.success('Vous avez été désabonné de la newsletter.')
      return true
    } catch (err: unknown) {
      setError(err, 'Impossible de se désabonner')
      ui.error('Impossible de se désabonner. Veuillez réessayer.')
      return false
    } finally {
      loading.value = false
    }
  }

  // ---------------------------------------------------------------------------
  // Actions admin
  // ---------------------------------------------------------------------------

  /**
   * Récupère la liste paginée des abonnés (réservé aux admins).
   *
   * @param filters - Filtres optionnels (is_active, search, page, per_page)
   */
  async function fetchSubscribers(
    filters?: SubscriberFilters
  ): Promise<void> {
    loading.value = true
    error.value = null

    // Construire la query en retirant les valeurs nulles/vides
    const query: Record<string, string | number | boolean> = {}
    if (filters) {
      for (const [k, v] of Object.entries(filters)) {
        if (v !== undefined && v !== null && v !== '') {
          query[k] = v as string | number | boolean
        }
      }
    }

    try {
      const response = await api<PaginatedResponse<Subscriber>>(
        '/newsletter/subscribers',
        { query }
      )
      subscribers.value = response.data
      pagination.value = response.meta
    } catch (err: unknown) {
      setError(err, 'Impossible de charger les abonnés')
      ui.error('Impossible de charger la liste des abonnés.')
    } finally {
      loading.value = false
    }
  }

  /**
   * Exporte la liste des abonnés actifs (admin).
   * Déclenche le téléchargement d'un CSV.
   */
  async function exportSubscribers(): Promise<void> {
    loading.value = true
    error.value = null

    try {
      // L'API retourne un blob CSV
      const blob = await api<Blob>('/newsletter/export', {
        responseType: 'blob',
      })

      // Créer un lien de téléchargement dynamique
      if (process.client) {
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `subscribers-${new Date().toISOString().slice(0, 10)}.csv`
        link.click()
        URL.revokeObjectURL(url)
      }

      ui.success('Export téléchargé avec succès.')
    } catch (err: unknown) {
      setError(err, "Impossible d'exporter les abonnés")
      ui.error("Impossible d'exporter les abonnés.")
    } finally {
      loading.value = false
    }
  }

  return {
    // State
    loading,
    error,
    success,
    subscribers,
    pagination,

    // Actions publiques
    subscribe,
    unsubscribe,
    reset,

    // Actions admin
    fetchSubscribers,
    exportSubscribers,
  }
}
