<script setup lang="ts">
// ============================================================
// Toast — notifications temporaires (lit le store ui)
// ============================================================
import type { Toast } from '~/types'

// Store UI — attendu : { toasts: Toast[], removeToast(id: string): void }
const uiStore = useUiStore()

// Config visuelle par type
const toastConfig: Record<string, { bg: string; border: string; text: string; iconPath: string }> = {
  success: {
    bg: 'bg-white',
    border: 'border-l-4 border-l-emerald-500',
    text: 'text-emerald-600',
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  error: {
    bg: 'bg-white',
    border: 'border-l-4 border-l-red-500',
    text: 'text-red-600',
    iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  warning: {
    bg: 'bg-white',
    border: 'border-l-4 border-l-amber-500',
    text: 'text-amber-600',
    iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
  },
  info: {
    bg: 'bg-white',
    border: 'border-l-4 border-l-blue-500',
    text: 'text-blue-600',
    iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  },
}
</script>

<template>
  <!-- Zone fixe en bas à droite -->
  <Teleport to="body">
    <div
      class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 w-80 pointer-events-none"
      aria-live="polite"
      aria-label="Notifications"
    >
      <TransitionGroup name="toast">
        <div
          v-for="toast in uiStore.toasts"
          :key="toast.id"
          :class="[
            'pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-xl shadow-lg',
            'border border-gray-100',
            toastConfig[toast.type].bg,
            toastConfig[toast.type].border,
          ]"
          role="alert"
        >
          <!-- Icône -->
          <svg
            xmlns="http://www.w3.org/2000/svg"
            :class="['w-5 h-5 flex-shrink-0 mt-0.5', toastConfig[toast.type].text]"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="toastConfig[toast.type].iconPath" />
          </svg>

          <!-- Message -->
          <p class="flex-1 text-sm text-gray-800 leading-relaxed">{{ toast.message }}</p>

          <!-- Fermer manuellement -->
          <button
            @click="uiStore.removeToast(toast.id)"
            class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
            aria-label="Fermer la notification"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<style scoped>
/* Entrée depuis la droite, sortie vers la droite */
.toast-enter-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-leave-active {
  transition: all 0.25s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
.toast-move {
  transition: transform 0.3s ease;
}
</style>
