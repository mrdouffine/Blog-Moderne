<script setup lang="ts">
// ============================================================
// Spinner — indicateur de chargement animé
// ============================================================
interface Props {
  size?: 'sm' | 'md' | 'lg'
  color?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  color: 'text-indigo-600',
})

const sizeClasses: Record<string, string> = {
  sm: 'w-4 h-4',
  md: 'w-6 h-6',
  lg: 'w-10 h-10',
}

// Si la couleur passée est une valeur CSS directe (ex: currentColor),
// on l'applique via style ; sinon on suppose une classe Tailwind
const isColorClass = computed(() => !props.color.includes('#') && !props.color.includes('rgb') && props.color !== 'currentColor')
</script>

<template>
  <svg
    :class="[
      'animate-spin',
      sizeClasses[size],
      isColorClass ? color : '',
    ]"
    :style="!isColorClass ? { color: color } : {}"
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
    aria-label="Chargement..."
    role="status"
  >
    <circle
      class="opacity-25"
      cx="12"
      cy="12"
      r="10"
      stroke="currentColor"
      stroke-width="4"
    />
    <path
      class="opacity-75"
      fill="currentColor"
      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
    />
  </svg>
</template>
