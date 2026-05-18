<script setup lang="ts">
// =============================================================================
// blog/frontend/app.vue
// Point d'entrée Nuxt — initialise l'authentification une seule fois (SSR-safe)
// =============================================================================

const authStore = useAuthStore()

/**
 * callOnce garantit que initAuth est exécuté une seule fois :
 *  - côté serveur lors du premier rendu SSR
 *  - côté client lors du premier montage (sans ré-exécution à la navigation)
 */
await callOnce(async () => {
  await authStore.initAuth()
})
</script>

<template>
  <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>

  <!-- Toasts globaux (fallback si le layout ne les inclut pas) -->
  <UiToast />
</template>
