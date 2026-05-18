<script setup lang="ts">
// ============================================================
// Alert — message contextuel dismissible
// ============================================================
interface Props {
  type?: 'success' | 'error' | 'warning' | 'info'
  message: string
  dismissible?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  dismissible: true,
})

const emit = defineEmits<{ close: [] }>()

const visible = ref(true)
const close = () => {
  visible.value = false
  emit('close')
}

// Config par type : couleurs + icône SVG path
const config: Record<string, { classes: string; iconPath: string }> = {
  success: {
    classes: 'bg-emerald-50 border-emerald-300 text-emerald-800',
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  error: {
    classes: 'bg-red-50 border-red-300 text-red-800',
    iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
  },
  warning: {
    classes: 'bg-amber-50 border-amber-300 text-amber-800',
    iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
  },
  info: {
    classes: 'bg-blue-50 border-blue-300 text-blue-800',
    iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  },
}

const current = computed(() => config[props.type])
</script>

<template>
  <Transition name="alert-fade">
    <div
      v-if="visible"
      :class="['flex items-start gap-3 px-4 py-3 rounded-lg border text-sm', current.classes]"
      role="alert"
    >
      <!-- Icône -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5 flex-shrink-0 mt-0.5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="current.iconPath" />
      </svg>

      <!-- Message -->
      <p class="flex-1">{{ message }}</p>

      <!-- Bouton fermer -->
      <button
        v-if="dismissible"
        @click="close"
        class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity ml-auto"
        aria-label="Fermer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
  </Transition>
</template>

<style scoped>
.alert-fade-enter-active,
.alert-fade-leave-active {
  transition: all 0.2s ease;
}
.alert-fade-enter-from,
.alert-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
