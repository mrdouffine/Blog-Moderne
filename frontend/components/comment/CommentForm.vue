<script setup lang="ts">
// ============================================================
// CommentForm — formulaire d'ajout/édition de commentaire
// ============================================================
interface Props {
  articleId: number
  loading?: boolean
  initialValue?: string   // Pour le mode édition
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
  initialValue: '',
})

const emit = defineEmits<{ submitted: [] }>()

const MAX_CHARS = 1000

const content = ref(props.initialValue)
const error   = ref('')

const charCount    = computed(() => content.value.length)
const isOverLimit  = computed(() => charCount.value > MAX_CHARS)
const isNearLimit  = computed(() => charCount.value > MAX_CHARS * 0.85)
const remaining    = computed(() => MAX_CHARS - charCount.value)

const { addComment } = useComments()

const onSubmit = async () => {
  error.value = ''
  if (!content.value.trim()) {
    error.value = 'Le commentaire ne peut pas être vide.'
    return
  }
  if (isOverLimit.value) {
    error.value = `Le commentaire dépasse la limite de ${MAX_CHARS} caractères.`
    return
  }

  try {
    await addComment({ article_id: props.articleId, content: content.value.trim() })
    content.value = ''
    emit('submitted')
  } catch (e: any) {
    error.value = e?.message ?? 'Une erreur est survenue.'
  }
}
</script>

<template>
  <form @submit.prevent="onSubmit" class="space-y-3">
    <!-- Textarea -->
    <div class="relative">
      <textarea
        v-model="content"
        rows="4"
        placeholder="Partagez votre avis ou posez une question..."
        :class="[
          'w-full rounded-xl border px-4 py-3 text-sm text-gray-900 placeholder-gray-400',
          'resize-none focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors',
          error
            ? 'border-red-400 focus:ring-red-400'
            : 'border-gray-200 focus:ring-indigo-500 focus:border-indigo-500',
        ]"
      />

      <!-- Compteur de caractères positionné en bas à droite du textarea -->
      <span
        :class="[
          'absolute bottom-3 right-3 text-xs pointer-events-none transition-colors',
          isOverLimit ? 'text-red-500 font-semibold' : isNearLimit ? 'text-amber-500' : 'text-gray-300',
        ]"
      >
        {{ charCount }}/{{ MAX_CHARS }}
      </span>
    </div>

    <!-- Erreur -->
    <p v-if="error" class="text-xs text-red-600">{{ error }}</p>

    <!-- Actions -->
    <div class="flex justify-end">
      <UiButton
        type="submit"
        size="sm"
        :loading="loading"
        :disabled="isOverLimit || !content.trim()"
      >
        Publier le commentaire
      </UiButton>
    </div>
  </form>
</template>
