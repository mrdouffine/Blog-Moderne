// =============================================================================
// blog/frontend/stores/ui.ts
// Store Pinia pour l'interface utilisateur
// - Gestion des toasts / notifications
// - État de chargement global
// - Sidebar (menu latéral)
// =============================================================================

import { defineStore } from 'pinia'
import type { Toast, ToastType, UiState } from '~/types'

/** Durée par défaut d'un toast en millisecondes */
const DEFAULT_TOAST_DURATION = 4000

export const useUiStore = defineStore('ui', {
  // ---------------------------------------------------------------------------
  // State
  // ---------------------------------------------------------------------------
  state: (): UiState => ({
    toasts: [],
    isLoading: false,
    sidebarOpen: false,
  }),

  // ---------------------------------------------------------------------------
  // Getters
  // ---------------------------------------------------------------------------
  getters: {
    /** Dernier toast ajouté (le plus récent) */
    latestToast: (state): Toast | undefined =>
      state.toasts[state.toasts.length - 1],

    /** Nombre de toasts actifs */
    toastCount: (state): number => state.toasts.length,
  },

  // ---------------------------------------------------------------------------
  // Actions
  // ---------------------------------------------------------------------------
  actions: {
    // -------------------------------------------------------------------------
    // Toasts
    // -------------------------------------------------------------------------

    /**
     * Ajoute une notification toast et planifie sa suppression automatique.
     *
     * @param message  - Texte du message à afficher
     * @param type     - Variante visuelle ('success' | 'error' | 'warning' | 'info')
     * @param duration - Durée d'affichage en ms (0 = persistant, défaut 4000 ms)
     * @returns L'identifiant unique du toast créé
     */
    addToast(
      message: string,
      type: ToastType = 'info',
      duration: number = DEFAULT_TOAST_DURATION
    ): string {
      const id = _generateId()

      const toast: Toast = {
        id,
        message,
        type,
        duration,
      }

      this.toasts.push(toast)

      // Auto-suppression après `duration` ms (sauf si durée nulle)
      if (duration > 0) {
        if (process.client) {
          setTimeout(() => {
            this.removeToast(id)
          }, duration)
        }
      }

      return id
    },

    /**
     * Supprime un toast par son identifiant.
     * @param id - Identifiant du toast à supprimer
     */
    removeToast(id: string): void {
      const idx = this.toasts.findIndex((t) => t.id === id)
      if (idx !== -1) {
        this.toasts.splice(idx, 1)
      }
    },

    /** Supprime tous les toasts en cours */
    clearToasts(): void {
      this.toasts = []
    },

    // -------------------------------------------------------------------------
    // Raccourcis sémantiques pour les toasts
    // -------------------------------------------------------------------------

    /**
     * Affiche un toast de succès.
     * @param message  - Message à afficher
     * @param duration - Durée optionnelle en ms
     */
    success(message: string, duration?: number): string {
      return this.addToast(message, 'success', duration)
    },

    /**
     * Affiche un toast d'erreur (persistant par défaut : duration = 0).
     * @param message  - Message à afficher
     * @param duration - Durée optionnelle en ms (défaut : 6000 ms pour les erreurs)
     */
    error(message: string, duration: number = 6000): string {
      return this.addToast(message, 'error', duration)
    },

    /**
     * Affiche un toast d'avertissement.
     * @param message  - Message à afficher
     * @param duration - Durée optionnelle en ms
     */
    warning(message: string, duration?: number): string {
      return this.addToast(message, 'warning', duration)
    },

    /**
     * Affiche un toast informatif.
     * @param message  - Message à afficher
     * @param duration - Durée optionnelle en ms
     */
    info(message: string, duration?: number): string {
      return this.addToast(message, 'info', duration)
    },

    // -------------------------------------------------------------------------
    // Sidebar
    // -------------------------------------------------------------------------

    /** Bascule l'état ouvert/fermé de la sidebar */
    toggleSidebar(): void {
      this.sidebarOpen = !this.sidebarOpen
    },

    /**
     * Ouvre ou ferme explicitement la sidebar.
     * @param open - true pour ouvrir, false pour fermer
     */
    setSidebar(open: boolean): void {
      this.sidebarOpen = open
    },

    // -------------------------------------------------------------------------
    // Chargement global
    // -------------------------------------------------------------------------

    /**
     * Active ou désactive l'indicateur de chargement global.
     * Utile pour les transitions de page ou les opérations longues.
     * @param value - true pour activer, false pour désactiver
     */
    setLoading(value: boolean): void {
      this.isLoading = value
    },
  },
})

// =============================================================================
// Helpers
// =============================================================================

/**
 * Génère un identifiant unique pour un toast.
 * Utilise un préfixe lisible + timestamp + entier aléatoire.
 */
function _generateId(): string {
  return `toast-${Date.now()}-${Math.floor(Math.random() * 10_000)}`
}
