// =============================================================================
// blog/frontend/composables/useComments.ts
// Composable pour la gestion des commentaires d'un article
// - State local (pas de store dédié)
// - CRUD : fetch, add, update, delete
// =============================================================================

import { useNuxtApp } from '#app'
import { useUiStore } from '~/stores/ui'
import type { Comment, ApiResponse, PaginatedResponse, PaginationMeta } from '~/types'

/**
 * Composable de gestion des commentaires.
 *
 * Usage dans un composant :
 * ```ts
 * const { comments, loading, fetchComments, addComment } = useComments()
 * await fetchComments(articleId)
 * ```
 */
export const useComments = () => {
  const { $api } = useNuxtApp()
  const api = $api as typeof $fetch
  const ui = useUiStore()

  // ---------------------------------------------------------------------------
  // State local
  // ---------------------------------------------------------------------------
  const comments = ref<Comment[]>([])
  const loading = ref<boolean>(false)
  const error = ref<string | null>(null)
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

  // ---------------------------------------------------------------------------
  // Actions
  // ---------------------------------------------------------------------------

  /**
   * Récupère les commentaires d'un article (paginés).
   *
   * @param articleId - Identifiant de l'article
   * @param page      - Numéro de page (défaut : 1)
   */
  async function fetchComments(
    articleId: number,
    page: number = 1
  ): Promise<void> {
    loading.value = true
    error.value = null

    try {
      const response = await api<PaginatedResponse<Comment>>(
        `/articles/${articleId}/comments`,
        { query: { page } }
      )
      comments.value = response.data
      pagination.value = response.meta
    } catch (err: unknown) {
      setError(err, 'Impossible de charger les commentaires')
      ui.error('Impossible de charger les commentaires.')
    } finally {
      loading.value = false
    }
  }

  /**
   * Ajoute un nouveau commentaire sur un article.
   * Insère le commentaire en tête de liste en cas de succès.
   *
   * @param articleId - Identifiant de l'article cible
   * @param content   - Contenu textuel du commentaire
   * @returns Le commentaire créé, ou undefined en cas d'erreur
   */
  async function addComment(
    articleId: number,
    content: string
  ): Promise<Comment | undefined> {
    loading.value = true
    error.value = null

    try {
      const response = await api<ApiResponse<Comment>>(
        `/articles/${articleId}/comments`,
        {
          method: 'POST',
          body: { content },
        }
      )

      // Insérer en tête de liste pour un affichage immédiat
      comments.value.unshift(response.data)
      ui.success('Commentaire ajouté. Il sera visible après modération.')
      return response.data
    } catch (err: unknown) {
      setError(err, "Impossible d'ajouter le commentaire")
      ui.error("Impossible d'ajouter le commentaire.")
      return undefined
    } finally {
      loading.value = false
    }
  }

  /**
   * Met à jour le contenu d'un commentaire existant.
   *
   * @param commentId - Identifiant du commentaire
   * @param content   - Nouveau contenu
   * @returns Le commentaire mis à jour, ou undefined en cas d'erreur
   */
  async function updateComment(
    commentId: number,
    content: string
  ): Promise<Comment | undefined> {
    loading.value = true
    error.value = null

    try {
      const response = await api<ApiResponse<Comment>>(
        `/comments/${commentId}`,
        {
          method: 'PUT',
          body: { content },
        }
      )

      // Mettre à jour dans la liste locale
      const idx = comments.value.findIndex((c) => c.id === commentId)
      if (idx !== -1) {
        comments.value[idx] = response.data
      }

      ui.success('Commentaire mis à jour.')
      return response.data
    } catch (err: unknown) {
      setError(err, 'Impossible de modifier le commentaire')
      ui.error('Impossible de modifier le commentaire.')
      return undefined
    } finally {
      loading.value = false
    }
  }

  /**
   * Supprime un commentaire.
   *
   * @param commentId - Identifiant du commentaire
   * @returns true si supprimé, false sinon
   */
  async function deleteComment(commentId: number): Promise<boolean> {
    loading.value = true
    error.value = null

    try {
      await api(`/comments/${commentId}`, { method: 'DELETE' })

      // Retirer de la liste locale
      comments.value = comments.value.filter((c) => c.id !== commentId)
      ui.success('Commentaire supprimé.')
      return true
    } catch (err: unknown) {
      setError(err, 'Impossible de supprimer le commentaire')
      ui.error('Impossible de supprimer le commentaire.')
      return false
    } finally {
      loading.value = false
    }
  }

  /**
   * Approuve un commentaire (admin).
   *
   * @param commentId - Identifiant du commentaire
   * @returns Le commentaire approuvé, ou undefined en cas d'erreur
   */
  async function approveComment(
    commentId: number
  ): Promise<Comment | undefined> {
    loading.value = true
    error.value = null

    try {
      const response = await api<ApiResponse<Comment>>(
        `/comments/${commentId}/approve`,
        { method: 'PATCH' }
      )

      const idx = comments.value.findIndex((c) => c.id === commentId)
      if (idx !== -1) {
        comments.value[idx] = response.data
      }

      ui.success('Commentaire approuvé.')
      return response.data
    } catch (err: unknown) {
      setError(err, "Impossible d'approuver le commentaire")
      ui.error("Impossible d'approuver le commentaire.")
      return undefined
    } finally {
      loading.value = false
    }
  }

  /** Vide la liste des commentaires (ex : au démontage du composant) */
  function resetComments(): void {
    comments.value = []
    pagination.value = null
    error.value = null
  }

  return {
    // State
    comments,
    loading,
    error,
    pagination,

    // Actions
    fetchComments,
    addComment,
    updateComment,
    deleteComment,
    approveComment,
    resetComments,
  }
}
