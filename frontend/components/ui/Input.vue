<script setup lang="ts">
// ============================================================
// Input — champ de formulaire avec label et gestion d'erreur
// ============================================================
interface Props {
  modelValue?: string | number
  label?: string
  type?: string
  placeholder?: string
  error?: string
  required?: boolean
  disabled?: boolean
  id?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  required: false,
  disabled: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

// Génère un id unique si non fourni (pour lier label/input)
const uid = computed(() => props.id || `input-${Math.random().toString(36).slice(2, 9)}`)

const onInput = (e: Event) => {
  emit('update:modelValue', (e.target as HTMLInputElement).value)
}
</script>

<template>
  <div class="w-full">
    <!-- Label -->
    <label
      v-if="label"
      :for="uid"
      class="block text-sm font-medium text-gray-700 mb-1.5"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <!-- Input field -->
    <input
      :id="uid"
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      @input="onInput"
      :class="[
        'w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900',
        'placeholder-gray-400 bg-white',
        'transition-colors duration-200',
        'focus:outline-none focus:ring-2 focus:ring-offset-0',
        error
          ? 'border-red-400 focus:ring-red-400 focus:border-red-400'
          : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500',
        disabled ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '',
      ]"
    />

    <!-- Message d'erreur animé -->
    <Transition name="slide-down">
      <p v-if="error" class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ error }}
      </p>
    </Transition>
  </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.2s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
