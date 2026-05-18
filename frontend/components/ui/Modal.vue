<script setup lang="ts">
// ============================================================
// Modal — dialogue avec overlay, téléporté dans <body>
// ============================================================
interface Props {
  modelValue: boolean
  title?: string
  size?: 'sm' | 'md' | 'lg'
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
}>()

const sizeClasses: Record<string, string> = {
  sm: 'max-w-sm',
  md: 'max-w-lg',
  lg: 'max-w-3xl',
}

const close = () => emit('update:modelValue', false)

// Fermeture au clic sur l'overlay
const onOverlayClick = (e: MouseEvent) => {
  if (e.target === e.currentTarget) close()
}

// Fermeture via Escape
const onKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') close()
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))

// Bloque le scroll du body quand la modal est ouverte
watch(
  () => props.modelValue,
  (val) => {
    document.body.style.overflow = val ? 'hidden' : ''
  },
)
</script>

<template>
  <!-- Téléporté directement dans <body> pour éviter les problèmes de z-index -->
  <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        @click="onOverlayClick"
      >
        <!-- Overlay semi-transparent -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" />

        <!-- Panneau de la modal -->
        <div
          :class="[
            'relative w-full bg-white rounded-2xl shadow-2xl',
            'flex flex-col max-h-[90vh]',
            sizeClasses[size],
          ]"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="title ? 'modal-title' : undefined"
        >
          <!-- Header -->
          <div v-if="title" class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 id="modal-title" class="text-lg font-semibold text-gray-900">
              {{ title }}
            </h2>
            <button
              @click="close"
              class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
              aria-label="Fermer"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Corps (slot default) -->
          <div class="flex-1 overflow-y-auto px-6 py-4">
            <slot />
          </div>

          <!-- Footer (slot optionnel) -->
          <div
            v-if="$slots.footer"
            class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3"
          >
            <slot name="footer" />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.25s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
  transition: transform 0.25s ease;
}
.modal-fade-enter-from .relative,
.modal-fade-leave-to .relative {
  transform: scale(0.95) translateY(-8px);
}
</style>
