<script setup lang="ts">
// ============================================================
// Button — composant bouton réutilisable
// ============================================================
import Spinner from './Spinner.vue'

interface Props {
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost'
  size?: 'sm' | 'md' | 'lg'
  loading?: boolean
  disabled?: boolean
  type?: 'button' | 'submit' | 'reset'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  loading: false,
  disabled: false,
  type: 'button',
})

// Mappe chaque variant sur ses classes Tailwind
const variantClasses: Record<string, string> = {
  primary:
    'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 border border-transparent',
  secondary:
    'bg-white text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500 border border-indigo-600',
  danger:
    'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 border border-transparent',
  ghost:
    'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-400 border border-gray-300',
}

const sizeClasses: Record<string, string> = {
  sm: 'px-3 py-1.5 text-sm gap-1.5',
  md: 'px-4 py-2 text-sm gap-2',
  lg: 'px-6 py-3 text-base gap-2',
}

const spinnerSize: Record<string, 'sm' | 'md' | 'lg'> = {
  sm: 'sm',
  md: 'sm',
  lg: 'md',
}

const isDisabled = computed(() => props.disabled || props.loading)
</script>

<template>
  <button
    :type="type"
    :disabled="isDisabled"
    :class="[
      'inline-flex items-center justify-center font-medium rounded-lg',
      'transition-colors duration-200',
      'focus:outline-none focus:ring-2 focus:ring-offset-2',
      variantClasses[variant],
      sizeClasses[size],
      isDisabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
    ]"
  >
    <!-- Spinner affiché pendant le chargement -->
    <Spinner v-if="loading" :size="spinnerSize[size]" color="currentColor" />
    <slot />
  </button>
</template>
