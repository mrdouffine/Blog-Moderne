<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })
useSeoMeta({ title: 'Commentaires — Admin BlogModerne' })

const { $api } = useNuxtApp()
const uiStore = useUiStore()

interface Comment {
  id: number
  content: string
  is_approved: boolean
  created_at: string
  user: { id: number; name: string } | null
  article: { id: number; title: string; slug: string } | null
}

const comments = ref<Comment[]>([])
const loading = ref(true)
const deleteModal = ref(false)
const toDelete = ref<Comment | null>(null)
const deleteLoading = ref(false)

const formattedDate = (d: string) =>
  new Date(d).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })

async function loadComments() {
  loading.value = true
  try {
    // Single fast request — no N+1
    const res = await $api<any>('/admin/comments', { query: { per_page: 50 } })
    comments.value = res?.data ?? []
  } catch {
    uiStore.error('Impossible de charger les commentaires.')
  } finally {
    loading.value = false
  }
}

async function deleteComment() {
  if (!toDelete.value) return
  deleteLoading.value = true
  try {
    await $api(`/comments/${toDelete.value.id}`, { method: 'DELETE' })
    comments.value = comments.value.filter(c => c.id !== toDelete.value!.id)
    uiStore.success('Commentaire supprimé.')
    deleteModal.value = false
    toDelete.value = null
  } catch {
    uiStore.error('Impossible de supprimer le commentaire.')
  } finally {
    deleteLoading.value = false
  }
}

async function approveComment(comment: Comment) {
  try {
    await $api(`/comments/${comment.id}/approve`, { method: 'PATCH' })
    comment.is_approved = true
    uiStore.success('Commentaire approuvé avec succès.')
  } catch {
    uiStore.error('Impossible d\'approuver le commentaire.')
  }
}

function confirmDelete(comment: Comment) {
  toDelete.value = comment
  deleteModal.value = true
}

await loadComments()
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Commentaires</h1>
        <p class="text-sm text-gray-500 mt-1">
          {{ loading ? '...' : `${comments.length} commentaire(s) au total` }}
        </p>
      </div>
    </div>

    <!-- Skeleton loading -->
    <div v-if="loading" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-pulse">
      <div v-for="i in 5" :key="i" class="flex items-start gap-4 p-5 border-b border-gray-50">
        <div class="w-9 h-9 bg-gray-200 rounded-full flex-shrink-0" />
        <div class="flex-1 space-y-2">
          <div class="h-3 bg-gray-200 rounded w-1/4" />
          <div class="h-3 bg-gray-200 rounded w-3/4" />
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="comments.length === 0"
      class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm"
    >
      <div class="w-16 h-16 bg-violet-100 text-violet-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-gray-900 mb-2">Aucun commentaire</h2>
      <p class="text-gray-500">Il n'y a encore aucun commentaire sur vos articles.</p>
    </div>

    <!-- Comment list -->
    <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="flex items-start gap-4 p-5 hover:bg-gray-50 transition-colors"
      >
        <!-- Avatar -->
        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold flex-shrink-0 uppercase">
          {{ comment.user?.name?.charAt(0) ?? '?' }}
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 flex-wrap mb-1">
            <span class="text-sm font-semibold text-gray-900">{{ comment.user?.name ?? 'Anonyme' }}</span>
            <span class="text-xs text-gray-400">{{ formattedDate(comment.created_at) }}</span>
            <span v-if="!comment.is_approved" class="px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-700 rounded-full">En attente</span>
            <span v-else class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">Approuvé</span>
          </div>
          <p class="text-sm text-gray-700 mb-1">{{ comment.content }}</p>
          <NuxtLink
            v-if="comment.article"
            :to="`/articles/${comment.article.slug}`"
            target="_blank"
            class="text-xs text-indigo-500 hover:underline"
          >
            → {{ comment.article.title }}
          </NuxtLink>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1 flex-shrink-0">
          <!-- Approuver -->
          <button
            v-if="!comment.is_approved"
            @click="approveComment(comment)"
            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
            title="Approuver le commentaire"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </button>

          <!-- Supprimer -->
          <button
            @click="confirmDelete(comment)"
            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
            title="Supprimer le commentaire"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Delete modal -->
    <UiModal v-model="deleteModal" title="Supprimer le commentaire" size="sm">
      <p class="text-sm text-gray-600">
        Supprimer le commentaire de <strong>{{ toDelete?.user?.name }}</strong> ? Cette action est irréversible.
      </p>
      <template #footer>
        <button @click="deleteModal = false" class="px-4 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">Annuler</button>
        <button @click="deleteComment" :disabled="deleteLoading" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors disabled:opacity-60">Supprimer</button>
      </template>
    </UiModal>
  </div>
</template>
