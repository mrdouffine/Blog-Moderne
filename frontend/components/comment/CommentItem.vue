<script setup lang="ts">
// ============================================================
// CommentItem — affichage d'un commentaire individuel
// ============================================================
import type { Comment } from '~/types'

interface Props {
  comment: Comment
  canEdit?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  canEdit: false,
})

const emit = defineEmits<{
  edit:   [comment: Comment]
  delete: [id: number]
}>()

const formattedDate = computed(() =>
  new Date(props.comment.created_at).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }),
)

// Initiales pour l'avatar généré
const initials = computed(() =>
  props.comment.author?.name
    ?.split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase() ?? '?',
)

// Couleur déterministe basée sur le nom
const avatarColor = computed(() => {
  const colors = [
    'bg-indigo-100 text-indigo-700',
    'bg-violet-100 text-violet-700',
    'bg-blue-100 text-blue-700',
    'bg-emerald-100 text-emerald-700',
    'bg-amber-100 text-amber-700',
    'bg-rose-100 text-rose-700',
  ]
  const idx = (props.comment.author?.name?.charCodeAt(0) ?? 0) % colors.length
  return colors[idx]
})
</script>

<template>
  <div class="flex gap-4 group">
    <!-- Avatar -->
    <div
      :class="['w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0', avatarColor]"
    >
      {{ initials }}
    </div>

    <!-- Corps du commentaire -->
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="font-semibold text-sm text-gray-900">{{ comment.author?.name }}</span>
          <time :datetime="comment.created_at" class="text-xs text-gray-400">
            {{ formattedDate }}
          </time>
        </div>

        <!-- Actions (visibles au hover) -->
        <div
          v-if="canEdit"
          class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
        >
          <button
            @click="emit('edit', comment)"
            class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
            title="Modifier"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
          </button>
          <button
            @click="emit('delete', comment.id)"
            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            title="Supprimer"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Contenu -->
      <p class="mt-1.5 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
        {{ comment.content }}
      </p>
    </div>
  </div>
</template>
