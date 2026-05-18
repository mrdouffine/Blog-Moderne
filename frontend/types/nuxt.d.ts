// =============================================================================
// blog/frontend/types/nuxt.d.ts
// Augmentation des types globaux de Nuxt
// - Déclare $api dans le contexte NuxtApp pour avoir l'autocomplétion
// - Déclaration des modules utilisés
// =============================================================================

import type { $Fetch } from 'ofetch'

// Augmenter le type NuxtApp pour inclure $api exposé par le plugin
declare module '#app' {
  interface NuxtApp {
    /**
     * Instance $fetch préconfigurée exposée par ~/plugins/api.ts.
     * Inclut automatiquement le Bearer token et gère les erreurs 401/403.
     */
    $api: $Fetch
  }
}

// Augmenter le contexte Vue pour les options API (si utilisé)
declare module 'vue' {
  interface ComponentCustomProperties {
    $api: $Fetch
  }
}

export {}
