<script setup lang="ts">
// ============================================================
// Layout Admin — sidebar fixe + contenu scrollable
// ============================================================
const mobileNavOpen = ref(false)
const route = useRoute()

// Fermer la nav mobile à chaque changement de route
watch(() => route.path, () => { mobileNavOpen.value = false })
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar desktop (fixe) -->
    <div class="hidden lg:flex lg:flex-shrink-0">
      <LayoutAdminNav />
    </div>

    <!-- Overlay mobile -->
    <Transition name="fade">
      <div
        v-if="mobileNavOpen"
        class="fixed inset-0 z-30 bg-black/50 lg:hidden"
        @click="mobileNavOpen = false"
      />
    </Transition>

    <!-- Sidebar mobile (drawer) -->
    <Transition name="sidebar-slide">
      <div
        v-if="mobileNavOpen"
        class="fixed inset-y-0 left-0 z-40 flex lg:hidden"
      >
        <LayoutAdminNav />
      </div>
    </Transition>

    <!-- Contenu principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Topbar mobile -->
      <div class="lg:hidden flex items-center justify-between h-14 px-4 bg-white border-b border-gray-100">
        <button
          @click="mobileNavOpen = true"
          class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
          aria-label="Ouvrir le menu"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <span class="text-sm font-bold text-gray-900">Administration</span>
        <div class="w-9" /> <!-- Spacer -->
      </div>

      <!-- Zone scrollable -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>
    </div>

    <!-- Toasts globaux -->
    <UiToast />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.sidebar-slide-enter-active,
.sidebar-slide-leave-active {
  transition: transform 0.25s ease;
}
.sidebar-slide-enter-from,
.sidebar-slide-leave-to {
  transform: translateX(-100%);
}
</style>
