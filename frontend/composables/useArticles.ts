// =============================================================================
// blog/frontend/composables/useArticles.ts
// Composable pour la gestion des articles
// - Wrapper du store articles
// - Gestion des erreurs via le store UI (toasts)
// =============================================================================

import { storeToRefs } from 'pinia'
import { useArticlesStore } from '~/stores/articles'
import { useUiStore } from '~/stores/ui'
import type { ArticleFilters, ArticlePayload } from '~/types'

/**
 * Composable articles.
 *
 * Usage :
 * ```ts
 * const { articles, loading, fetchArticles, createArticle } = useArticles()
 * ```
 */
export const useArticles = () => {
  const store = useArticlesStore()
  const ui = useUiStore()

  // Déstructuration réactive
  const { articles, currentArticle, loading, error, pagination, filters } =
    storeToRefs(store)

  // Getters calculés
  const hasNextPage = computed(() => store.hasNextPage)
  const hasPrevPage = computed(() => store.hasPrevPage)
  const publishedArticles = computed(() => store.publishedArticles)

  // ---------------------------------------------------------------------------
  // Wrappers avec gestion des erreurs
  // ---------------------------------------------------------------------------

  /**
   * Charge la liste paginée des articles.
   * Affiche un toast d'erreur en cas d'échec.
   *
   * @param filters - Filtres optionnels (surchargent ceux du store)
   */
  async function fetchArticles(
    filters?: Partial<ArticleFilters>
  ): Promise<void> {
    try {
      await store.fetchArticles(filters)
    } catch {
      ui.error('Impossible de charger les articles. Veuillez réessayer.')
    }
  }

  /**
   * Charge un article par son slug.
   * Affiche un toast d'erreur en cas d'échec.
   *
   * @param slug - Slug de l'article
   */
  async function fetchArticle(slug: string): Promise<void> {
    try {
      await store.fetchArticle(slug)
    } catch {
      ui.error('Article introuvable.')
    }
  }

  /**
   * Crée un nouvel article.
   * Affiche un toast de succès ou d'erreur selon le résultat.
   *
   * @param data - Données de l'article (objet ou FormData)
   * @returns L'article créé, ou undefined en cas d'erreur
   */
  async function createArticle(
    data: ArticlePayload | FormData
  ) {
    try {
      const article = await store.createArticle(data)
      ui.success('Article créé avec succès !')
      return article
    } catch {
      ui.error("Impossible de créer l'article.")
      return undefined
    }
  }

  /**
   * Met à jour un article existant.
   * Affiche un toast de succès ou d'erreur.
   *
   * @param id   - Identifiant de l'article
   * @param data - Données partielles ou FormData
   * @returns L'article mis à jour, ou undefined en cas d'erreur
   */
  async function updateArticle(
    id: number,
    data: Partial<ArticlePayload> | FormData
  ) {
    try {
      const article = await store.updateArticle(id, data)
      ui.success('Article mis à jour avec succès !')
      return article
    } catch {
      ui.error("Impossible de mettre à jour l'article.")
      return undefined
    }
  }

  /**
   * Supprime un article.
   * Affiche un toast de succès ou d'erreur.
   *
   * @param id - Identifiant de l'article
   * @returns true si la suppression a réussi, false sinon
   */
  async function deleteArticle(id: number): Promise<boolean> {
    try {
      await store.deleteArticle(id)
      ui.success('Article supprimé avec succès !')
      return true
    } catch {
      ui.error("Impossible de supprimer l'article.")
      return false
    }
  }

  /**
   * Publie un article (statut → 'published').
   * Affiche un toast de succès ou d'erreur.
   *
   * @param id - Identifiant de l'article
   * @returns L'article publié, ou undefined en cas d'erreur
   */
  async function publishArticle(id: number) {
    try {
      const article = await store.publishArticle(id)
      ui.success('Article publié !')
      return article
    } catch {
      ui.error("Impossible de publier l'article.")
      return undefined
    }
  }

  /**
   * Repasse un article en brouillon (statut → 'draft').
   * Affiche un toast de succès ou d'erreur.
   *
   * @param id - Identifiant de l'article
   * @returns L'article en brouillon, ou undefined en cas d'erreur
   */
  async function draftArticle(id: number) {
    try {
      const article = await store.draftArticle(id)
      ui.success('Article repassé en brouillon.')
      return article
    } catch {
      ui.error("Impossible de repasser l'article en brouillon.")
      return undefined
    }
  }

  /**
   * Met à jour un filtre de recherche.
   * Délègue directement au store.
   *
   * @param key   - Clé du filtre
   * @param value - Valeur à appliquer
   */
  function setFilter<K extends keyof ArticleFilters>(
    key: K,
    value: ArticleFilters[K]
  ): void {
    store.setFilter(key, value)
  }

  /** Réinitialise tous les filtres */
  function resetFilters(): void {
    store.resetFilters()
  }

  return {
    // State (réactif)
    articles,
    currentArticle,
    loading,
    error,
    pagination,
    filters,

    // Getters calculés
    hasNextPage,
    hasPrevPage,
    publishedArticles,

    // Actions
    fetchArticles,
    fetchArticle,
    createArticle,
    updateArticle,
    deleteArticle,
    publishArticle,
    draftArticle,
    setFilter,
    resetFilters,
  }
}
