<script setup lang="ts">
// ============================================================
// MediaGallery — galerie des médias liés à un article
// ============================================================
interface Props {
  articleId: number
}

const props = defineProps<Props>()

const { medias, loading, fetchMedias, deleteMedia } = useMedia()

const deletingId = ref<number | null>(null)

onMounted(() => fetchMedias(props.articleId))

const onDelete = async (id: number) => {
  if (!confirm('Supprimer ce fichier ?')) return
  deletingId.value = id
  try {
    await deleteMedia(id)
  } finally {
    deletingId.value = null
  }
}

// Formate la taille en Ko/Mo
const formatSize = (bytes: number): string => {
  if (bytes < 1024) return `${bytes} o`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} Ko`
  return `${(bytes / 1024 / 1024).toFixed(1)} Mo`
}
</script>

<template>
  <div>
    <!-- En-tête -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-sm font-semibold text-gray-700">Galerie de l'article</h3>
      <span v-if="!loading" class="text-xs text-gray-400">{{ medias.length }} fichier(s)</span>
    </div>

    <!-- Skeleton -->
    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
      <div
        v-for="i in 8"
        :key="i"
        class="aspect-square rounded-xl bg-gray-200 animate-pulse"
      />
    </div>

    <!-- Grille médias -->
    <div v-else-if="medias.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
      <div
        v-for="media in medias"
        :key="media.id"
        class="group relative rounded-xl overflow-hidden bg-gray-100 aspect-square shadow-sm"
      >
        <!-- Image -->
        <img
          :src="media.url"
          :alt="media.filename"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
          loading="lazy"
        />

        <!-- Overlay au hover -->
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-200 flex flex-col items-center justify-center gap-2">
          <!-- Nom du fichier -->
          <p class="text-xs text-white font-medium px-2 text-center opacity-0 group-hover:opacity-100 transition-opacity line-clamp-2">
            {{ media.filename }}
          </p>
          <p class="text-xs text-white/70 opacity-0 group-hover:opacity-100 transition-opacity">
            {{ formatSize(media.size) }}
          </p>

          <!-- Bouton supprimer -->
          <button
            @click="onDelete(media.id)"
            :disabled="deletingId === media.id"
            class="opacity-0 group-hover:opacity-100 transition-opacity p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg disabled:opacity-50"
            title="Supprimer"
          >
            <UiSpinner v-if="deletingId === media.id" size="sm" color="currentColor" />
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Vide -->
    <div v-else class="flex flex-col items-center justify-center py-12 text-gray-400 bg-gray-50 rounded-xl">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      <p class="text-sm">Aucun média pour cet article</p>
    </div>
  </div>
</template>
