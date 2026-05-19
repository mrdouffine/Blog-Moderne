<script setup lang="ts">
// ============================================================
// Layout public par défaut — header + footer + toasts
// ============================================================
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Header sticky -->
    <LayoutHeader />

    <!-- Contenu de la page -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <LayoutFooter />

    <!-- Toasts globaux -->
    <UiToast />
  </div>
</template>
