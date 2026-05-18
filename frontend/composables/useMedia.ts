// =============================================================================
// blog/frontend/composables/useMedia.ts
// Composable pour la gestion des médias (upload, suppression, listing)
// - Upload multipart/form-data
// - Liaison optionnelle à un article
// =============================================================================

import { useNuxtApp } from '#app'
import { useUiStore } from '~/stores/ui'
import type { Media, ApiResponse } from '~/types'

/** Taille maximum autorisée pour un upload (10 Mo) */
const MAX_FILE_SIZE = 10 * 1024 * 1024

/** Types MIME autorisés pour les images */
const ALLOWED_IMAGE_TYPES = [
  'image/jpeg',
  'image/png',
  'image/webp',
  'image/gif',
  'image/svg+xml',
]

/**
 * Composable de gestion des médias.
 *
 * Usage :
 * ```ts
 * const { media, uploading, uploadMedia, deleteMedia } = useMedia()
 * const uploaded = await uploadMedia(file, articleId)
 * ```
 */
export const useMedia = () => {
  const { $api } = useNuxtApp()
  const api = $api as typeof $fetch
  const ui = useUiStore()

  // ---------------------------------------------------------------------------
  // State local
  // ---------------------------------------------------------------------------
  const media = ref<Media[]>([])
  const uploading = ref<boolean>(false)
  const error = ref<string | null>(null)

  // Progress de l'upload (0-100)
  const uploadProgress = ref<number>(0)

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

  /**
   * Valide un fichier avant upload.
   * @param file    - Fichier à valider
   * @param options - Options de validation (types, taille max)
   * @returns Message d'erreur ou null si valide
   */
  function validateFile(
    file: File,
    options: {
      allowedTypes?: string[]
      maxSize?: number
    } = {}
  ): string | null {
    const {
      allowedTypes = ALLOWED_IMAGE_TYPES,
      maxSize = MAX_FILE_SIZE,
    } = options

    if (!allowedTypes.includes(file.type)) {
      return `Type de fichier non supporté. Types acceptés : ${allowedTypes.join(', ')}`
    }

    if (file.size > maxSize) {
      const maxMb = (maxSize / 1024 / 1024).toFixed(0)
      return `Fichier trop volumineux. Taille maximum : ${maxMb} Mo`
    }

    return null
  }

  // ---------------------------------------------------------------------------
  // Actions
  // ---------------------------------------------------------------------------

  /**
   * Uploade un fichier média.
   * Envoie une requête multipart/form-data vers POST /media/upload.
   *
   * @param file      - Fichier à uploader
   * @param articleId - Identifiant de l'article à associer (optionnel)
   * @returns Le média créé, ou undefined en cas d'erreur
   */
  async function uploadMedia(
    file: File,
    articleId?: number
  ): Promise<Media | undefined> {
    // Validation côté client avant envoi
    const validationError = validateFile(file)
    if (validationError) {
      error.value = validationError
      ui.error(validationError)
      return undefined
    }

    uploading.value = true
    uploadProgress.value = 0
    error.value = null

    try {
      // Construire le FormData pour l'upload multipart
      const formData = new FormData()
      formData.append('file', file)
      formData.append('filename', file.name)

      if (articleId !== undefined) {
        formData.append('article_id', String(articleId))
      }

      const response = await api<ApiResponse<Media>>(
        '/media/upload',
        {
          method: 'POST',
          body: formData,
          // Ne pas définir Content-Type : le navigateur le fait automatiquement
          // avec la boundary correcte pour multipart/form-data
        }
      )

      // Ajouter le nouveau média à la liste locale
      media.value.push(response.data)
      uploadProgress.value = 100
      ui.success('Fichier uploadé avec succès !')

      return response.data
    } catch (err: unknown) {
      setError(err, "Erreur lors de l'upload")
      ui.error("Impossible d'uploader le fichier.")
      return undefined
    } finally {
      uploading.value = false
    }
  }

  /**
   * Supprime un média par son identifiant.
   *
   * @param mediaId - Identifiant du média à supprimer
   * @returns true si supprimé, false sinon
   */
  async function deleteMedia(mediaId: number): Promise<boolean> {
    error.value = null

    try {
      await api(`/media/${mediaId}`, { method: 'DELETE' })

      // Retirer de la liste locale
      media.value = media.value.filter((m) => m.id !== mediaId)
      ui.success('Média supprimé.')
      return true
    } catch (err: unknown) {
      setError(err, 'Impossible de supprimer le média')
      ui.error('Impossible de supprimer le média.')
      return false
    }
  }

  /**
   * Récupère tous les médias associés à un article.
   *
   * @param articleId - Identifiant de l'article
   */
  async function fetchArticleMedia(articleId: number): Promise<void> {
    error.value = null

    try {
      const response = await api<ApiResponse<Media[]>>(
        `/articles/${articleId}/media`
      )
      media.value = response.data
    } catch (err: unknown) {
      setError(err, 'Impossible de charger les médias')
      ui.error('Impossible de charger les médias de cet article.')
    }
  }

  /**
   * Uploade plusieurs fichiers en parallèle.
   * Retourne les médias créés avec succès.
   *
   * @param files     - Tableau de fichiers
   * @param articleId - Identifiant de l'article (optionnel)
   * @returns Tableau des médias créés
   */
  async function uploadMultiple(
    files: File[],
    articleId?: number
  ): Promise<Media[]> {
    uploading.value = true
    error.value = null

    try {
      const results = await Promise.allSettled(
        files.map((f) => uploadMedia(f, articleId))
      )

      const created: Media[] = []
      for (const result of results) {
        if (result.status === 'fulfilled' && result.value) {
          created.push(result.value)
        }
      }

      if (created.length < files.length) {
        ui.warning(
          `${created.length}/${files.length} fichiers uploadés avec succès.`
        )
      }

      return created
    } finally {
      uploading.value = false
    }
  }

  /** Vide la liste locale des médias */
  function resetMedia(): void {
    media.value = []
    error.value = null
    uploadProgress.value = 0
  }

  return {
    // State
    media,
    uploading,
    error,
    uploadProgress,

    // Actions
    uploadMedia,
    deleteMedia,
    fetchArticleMedia,
    uploadMultiple,
    resetMedia,
    validateFile,
  }
}
