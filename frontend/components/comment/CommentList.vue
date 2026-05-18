<script setup lang="ts">
// ============================================================
// CommentList — liste complète des commentaires d'un article
// ============================================================
import type { Comment } from '~/types'

interface Props {
  articleId: number
}

const props = defineProps<Props>()

// Composables
const { comments, loading, fetchComments, deleteComment, updateComment } = useComments()
const authStore = useAuthStore()

const isAuthenticated = computed(() => !!authStore.user)
const isAdmin         = computed(() => authStore.user?.role === 'admin')

// Commentaire en cours d'édition
const editingComment = ref<Comment | null>(null)
const editLoading    = ref(false)
const deleteLoading  = ref<number | null>(null)

// Peut-on éditer un commentaire donné ?
const canEdit = (comment: Comment) =>
  isAdmin.value || comment.author?.id === authStore.user?.id

onMounted(() => fetchComments(props.articleId))

// ---- Handlers ----
const onDelete = async (id: number) => {
  if (!confirm('Supprimer ce commentaire ?')) return
  deleteLoading.value = id
  try {
    await deleteComment(id)
  } finally {
    deleteLoading.value = null
  }
}

const onEdit = (comment: Comment) => {
  editingComment.value = comment
}

const onEditSubmit = async (content: string) => {
  if (!editingComment.value) return
  editLoading.value = true
  try {
    await updateComment(editingComment.value.id, { content })
    editingComment.value = null
  } finally {
    editLoading.value = false
  }
}
</script>

<template>
  <section class="space-y-6">
    <!-- En-tête -->
    <div class="flex items-center gap-3">
      <h2 class="text-xl font-bold text-gray-900">Commentaires</h2>
      <span
        v-if="!loading"
        class="text-sm font-medium text-gray-400 bg-gray-100 px-2.5 py-0.5 rounded-full"
      >
        {{ comments.length }}
      </span>
    </div>

    <!-- Formulaire d'ajout (utilisateur connecté uniquement) -->
    <div v-if="isAuthenticated" class="bg-gray-50 rounded-xl p-4">
      <p class="text-sm font-medium text-gray-700 mb-3">Laisser un commentaire</p>
      <CommentForm :article-id="articleId" @submitted="fetchComments(articleId)" />
    </div>
    <div v-else class="bg-gray-50 rounded-xl p-4 text-center">
      <p class="text-sm text-gray-500">
        <NuxtLink to="/auth/login" class="text-indigo-600 font-medium hover:underline">Connectez-vous</NuxtLink>
        pour laisser un commentaire.
      </p>
    </div>

    <!-- Skeleton loading -->
    <div v-if="loading" class="space-y-5">
      <div v-for="i in 3" :key="i" class="flex gap-4 animate-pulse">
        <div class="w-9 h-9 rounded-full bg-gray-200 flex-shrink-0" />
        <div class="flex-1 space-y-2">
          <div class="h-3 bg-gray-200 rounded w-32" />
          <div class="h-3 bg-gray-200 rounded w-full" />
          <div class="h-3 bg-gray-200 rounded w-3/4" />
        </div>
      </div>
    </div>

    <!-- Liste des commentaires -->
    <div v-else-if="comments.length > 0" class="divide-y divide-gray-100 space-y-1">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="py-5 first:pt-0"
      >
        <!-- Mode édition inline -->
        <div v-if="editingComment?.id === comment.id" class="ml-13">
          <p class="text-xs font-medium text-gray-500 mb-2">Modifier le commentaire</p>
          <CommentForm
            :article-id="articleId"
            :initial-value="comment.content"
            :loading="editLoading"
            @submitted="onEditSubmit(comment.content)"
          />
          <button
            @click="editingComment = null"
            class="mt-2 text-xs text-gray-400 hover:text-gray-600"
          >
            Annuler
          </button>
        </div>

        <!-- Affichage normal -->
        <CommentItem
          v-else
          :comment="comment"
          :can-edit="canEdit(comment)"
          @edit="onEdit"
          @delete="onDelete"
        />
      </div>
    </div>

    <!-- Aucun commentaire -->
    <div v-else class="text-center py-8 text-gray-400">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
      </svg>
      <p class="text-sm">Aucun commentaire pour l'instant. Soyez le premier !</p>
    </div>
  </section>
</template>
