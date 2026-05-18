// =============================================================================
// blog/frontend/stores/articles.ts
// Store Pinia pour la gestion des articles de blog
// - CRUD complet + publish / draft
// - Pagination et filtres
// =============================================================================

import { defineStore } from 'pinia'
import { useNuxtApp } from '#app'
import type {
  Article,
  ArticlesState,
  ArticleFilters,
  ArticlePayload,
  ApiResponse,
  PaginatedResponse,
  PaginationMeta,
} from '~/types'

export const useArticlesStore = defineStore('articles', {
  // ---------------------------------------------------------------------------
  // State
  // ---------------------------------------------------------------------------
  state: (): ArticlesState => ({
    articles: [],
    currentArticle: null,
    loading: false,
    error: null,
    pagination: null,
    filters: {
      search: '',
      status: '',
      per_page: 12,
      page: 1,
      sort_by: 'created_at',
      sort_direction: 'desc',
    },
  }),

  // ---------------------------------------------------------------------------
  // Getters
  // ---------------------------------------------------------------------------
  getters: {
    /** Articles filtrés localement par statut (utile si liste déjà chargée) */
    publishedArticles: (state): Article[] =>
      state.articles.filter((a) => a.status === 'published'),

    /** Indique s'il existe une page suivante */
    hasNextPage: (state): boolean =>
      !!state.pagination &&
      state.pagination.current_page < state.pagination.last_page,

    /** Indique s'il existe une page précédente */
    hasPrevPage: (state): boolean =>
      !!state.pagination && state.pagination.current_page > 1,
  },

  // ---------------------------------------------------------------------------
  // Actions
  // ---------------------------------------------------------------------------
  actions: {
    /** Récupère l'instance $api du plugin */
    _api() {
      const { $api } = useNuxtApp()
      return $api as typeof $fetch
    },

    // -------------------------------------------------------------------------
    // Filtres
    // -------------------------------------------------------------------------

    /**
     * Met à jour un filtre et réinitialise la pagination à la page 1.
     * @param key   - Clé du filtre à modifier
     * @param value - Nouvelle valeur
     */
    setFilter<K extends keyof ArticleFilters>(
      key: K,
      value: ArticleFilters[K]
    ): void {
      this.filters[key] = value
      // Remettre à la page 1 dès qu'un filtre change (sauf si c'est la page)
      if (key !== 'page') {
        this.filters.page = 1
      }
    },

    /** Réinitialise tous les filtres à leurs valeurs par défaut */
    resetFilters(): void {
      this.filters = {
        search: '',
        status: '',
        per_page: 12,
        page: 1,
        sort_by: 'created_at',
        sort_direction: 'desc',
      }
    },

    // -------------------------------------------------------------------------
    // Lecture
    // -------------------------------------------------------------------------

    /**
     * Récupère la liste paginée des articles depuis l'API.
     * Fusionne les filtres du store avec ceux passés en paramètre.
     * @param filters - Filtres optionnels (surchargent ceux du store)
     */
    async fetchArticles(filters?: Partial<ArticleFilters>): Promise<void> {
      this.loading = true
      this.error = null

      // Construire les query params (retirer les valeurs vides)
      const query: Record<string, string | number> = {}
      const merged: ArticleFilters = { ...this.filters, ...filters }

      for (const [k, v] of Object.entries(merged)) {
        if (v !== '' && v !== undefined && v !== null) {
          query[k] = v as string | number
        }
      }

      try {
        const response = await this._api()<PaginatedResponse<Article>>(
          '/articles',
          { query }
        )
        this.articles = response.data
        this.pagination = response.meta
      } catch (err: unknown) {
        this.error = _extractMessage(err, 'Impossible de charger les articles')
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Récupère un article par son slug.
     * @param slug - Slug unique de l'article
     */
    async fetchArticle(slug: string): Promise<void> {
      this.loading = true
      this.error = null
      this.currentArticle = null

      try {
        const response = await this._api()<ApiResponse<Article>>(
          `/articles/${slug}`
        )
        this.currentArticle = response.data
      } catch (err: unknown) {
        this.error = _extractMessage(err, 'Article introuvable')
        throw err
      } finally {
        this.loading = false
      }
    },

    // -------------------------------------------------------------------------
    // Écriture
    // -------------------------------------------------------------------------

    /**
     * Crée un nouvel article.
     * Accepte un objet plain ou FormData (si upload de cover image).
     * @param data - Données de l'article
     * @returns L'article créé
     */
    async createArticle(
      data: ArticlePayload | FormData
    ): Promise<Article> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<Article>>(
          '/articles',
          {
            method: 'POST',
            body: data,
          }
        )

        // Ajouter l'article en tête de liste
        this.articles.unshift(response.data)
        return response.data
      } catch (err: unknown) {
        this.error = _extractMessage(err, "Impossible de créer l'article")
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Met à jour un article existant.
     * @param id   - Identifiant numérique de l'article
     * @param data - Données partielles ou FormData
     * @returns L'article mis à jour
     */
    async updateArticle(
      id: number,
      data: Partial<ArticlePayload> | FormData
    ): Promise<Article> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<Article>>(
          `/articles/${id}`,
          {
            method: 'PUT',
            body: data,
          }
        )

        // Mettre à jour dans la liste locale
        const idx = this.articles.findIndex((a) => a.id === id)
        if (idx !== -1) {
          this.articles[idx] = response.data
        }

        if (this.currentArticle?.id === id) {
          this.currentArticle = response.data
        }

        return response.data
      } catch (err: unknown) {
        this.error = _extractMessage(err, "Impossible de mettre à jour l'article")
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Supprime un article.
     * @param id - Identifiant numérique de l'article
     */
    async deleteArticle(id: number): Promise<void> {
      this.loading = true
      this.error = null

      try {
        await this._api()(`/articles/${id}`, { method: 'DELETE' })

        // Retirer de la liste locale
        this.articles = this.articles.filter((a) => a.id !== id)

        if (this.currentArticle?.id === id) {
          this.currentArticle = null
        }
      } catch (err: unknown) {
        this.error = _extractMessage(err, "Impossible de supprimer l'article")
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Publie un article (passe son statut à 'published').
     * @param id - Identifiant numérique de l'article
     * @returns L'article publié
     */
    async publishArticle(id: number): Promise<Article> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<Article>>(
          `/articles/${id}/publish`,
          { method: 'PATCH' }
        )

        _updateInList(this.articles, response.data)

        if (this.currentArticle?.id === id) {
          this.currentArticle = response.data
        }

        return response.data
      } catch (err: unknown) {
        this.error = _extractMessage(err, "Impossible de publier l'article")
        throw err
      } finally {
        this.loading = false
      }
    },

    /**
     * Repasse un article en brouillon.
     * @param id - Identifiant numérique de l'article
     * @returns L'article en brouillon
     */
    async draftArticle(id: number): Promise<Article> {
      this.loading = true
      this.error = null

      try {
        const response = await this._api()<ApiResponse<Article>>(
          `/articles/${id}/draft`,
          { method: 'PATCH' }
        )

        _updateInList(this.articles, response.data)

        if (this.currentArticle?.id === id) {
          this.currentArticle = response.data
        }

        return response.data
      } catch (err: unknown) {
        this.error = _extractMessage(err, "Impossible de repasser l'article en brouillon")
        throw err
      } finally {
        this.loading = false
      }
    },
  },
})

// =============================================================================
// Helpers module-level
// =============================================================================

/** Remplace un article dans un tableau par son id */
function _updateInList(list: Article[], updated: Article): void {
  const idx = list.findIndex((a) => a.id === updated.id)
  if (idx !== -1) {
    list[idx] = updated
  }
}

/** Extrait un message d'erreur lisible */
function _extractMessage(err: unknown, fallback: string): string {
  if (err && typeof err === 'object') {
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
