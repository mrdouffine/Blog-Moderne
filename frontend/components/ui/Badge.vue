<script setup lang="ts">
// ============================================================
// Badge — étiquette colorée pour statuts et catégories
// ============================================================
interface Props {
  variant?: 'success' | 'warning' | 'danger' | 'info' | 'default'
  label: string
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
})

const variantClasses: Record<string, string> = {
  success: 'bg-emerald-100 text-emerald-700 ring-emerald-200',
  warning: 'bg-amber-100 text-amber-700 ring-amber-200',
  danger:  'bg-red-100 text-red-700 ring-red-200',
  info:    'bg-blue-100 text-blue-700 ring-blue-200',
  default: 'bg-gray-100 text-gray-600 ring-gray-200',
}

const dotClasses: Record<string, string> = {
  success: 'bg-emerald-500',
  warning: 'bg-amber-500',
  danger:  'bg-red-500',
  info:    'bg-blue-500',
  default: 'bg-gray-400',
}
</script>

<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-2.5 py-0.5',
      'text-xs font-medium rounded-full ring-1',
      variantClasses[variant],
    ]"
  >
    <!-- Indicateur coloré -->
    <span :class="['w-1.5 h-1.5 rounded-full flex-shrink-0', dotClasses[variant]]" />
    {{ label }}
  </span>
</template>
