<script setup lang="ts">
// ============================================================
// Page d'édition d'article — charge l'article par ID
// ============================================================
definePageMeta({ middleware: 'auth', layout: 'admin' })

const route = useRoute()
const id    = Number(route.params.id)

const { article, loading, fetchArticleById, updateArticle, deleteArticle } = useArticles()
const uiStore = useUiStore()

// Chargement de l'article
await fetchArticleById(id)

useSeoMeta({
  title: computed(() => `Éditer « ${article.value?.title ?? '...' } » — Admin`),
})

if (!article.value) {
  throw createError({ statusCode: 404, message: 'Article introuvable' })
}

// ---- Soumission de la mise à jour ----
const submitLoading = ref(false)

const onSubmit = async (formData: FormData) => {
  submitLoading.value = true
  try {
    await updateArticle(id, formData)
    uiStore.addToast({ type: 'success', message: 'Article mis à jour !' })
    await navigateTo('/admin/articles')
  } catch (e: any) {
    uiStore.addToast({
      type: 'error',
      message: e?.message ?? "Erreur lors de la mise à jour.",
    })
  } finally {
    submitLoading.value = false
  }
}

// ---- Suppression ----
const deleteModal   = ref(false)
const deleteLoading = ref(false)

const onDelete = async () => {
  deleteLoading.value = true
  try {
    await deleteArticle(id)
    uiStore.addToast({ type: 'success', message: 'Article supprimé.' })
    await navigateTo('/admin/articles')
  } catch (e: any) {
    uiStore.addToast({ type: 'error', message: e?.message ?? 'Erreur lors de la suppression.' })
  } finally {
    deleteLoading.value = false
    deleteModal.value   = false
  }
}
</script>

<template>
  <div class="p-6 max-w-4xl mx-auto space-y-8">
    <!-- En-tête -->
    <div class="flex items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <NuxtLink
          to="/admin/articles"
          class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </NuxtLink>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Éditer l'article</h1>
          <p class="text-sm text-gray-500 mt-0.5 line-clamp-1">{{ article?.title }}</p>
        </div>
      </div>

      <!-- Bouton suppression -->
      <UiButton variant="danger" size="sm" @click="deleteModal = true">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Supprimer
      </UiButton>
    </div>

    <!-- Skeleton global -->
    <div v-if="loading" class="bg-white rounded-2xl border border-gray-100 p-6 animate-pulse space-y-5">
      <div class="h-5 bg-gray-200 rounded w-1/4" />
      <div class="h-10 bg-gray-200 rounded" />
      <div class="h-24 bg-gray-200 rounded" />
      <div class="h-48 bg-gray-200 rounded" />
    </div>

    <!-- Formulaire principal -->
    <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <ArticleForm
        :article="article ?? undefined"
        :loading="submitLoading"
        @submit="onSubmit"
      />
    </div>

    <!-- Galerie médias -->
    <div v-if="!loading" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="text-base font-semibold text-gray-700 mb-5">Médias de l'article</h2>

      <MediaUploader
        :article-id="id"
        multiple
        @uploaded="() => {}"
      />

      <div class="mt-6">
        <MediaGallery :article-id="id" />
      </div>
    </div>

    <!-- Lien de prévisualisation -->
    <div v-if="article" class="flex justify-center">
      <NuxtLink
        :to="`/articles/${article.slug}`"
        target="_blank"
        class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-indigo-600 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Prévisualiser l'article
      </NuxtLink>
    </div>

    <!-- Modal confirmation suppression -->
    <UiModal v-model="deleteModal" title="Supprimer l'article" size="sm">
      <p class="text-sm text-gray-600">
        Êtes-vous sûr de vouloir supprimer
        <strong class="text-gray-900">« {{ article?.title }} »</strong> ?
        Tous les médias et commentaires associés seront également supprimés.
      </p>
      <template #footer>
        <UiButton variant="ghost" @click="deleteModal = false">Annuler</UiButton>
        <UiButton variant="danger" :loading="deleteLoading" @click="onDelete">Supprimer définitivement</UiButton>
      </template>
    </UiModal>
  </div>
</template>
