<script setup lang="ts">
// ============================================================
// ArticleList — grille d'articles avec skeleton loader
// ============================================================
import type { Article } from '~/types'

interface Props {
  articles: Article[]
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

// Nombre de skeletons à afficher pendant le chargement
const SKELETON_COUNT = 6
</script>

<template>
  <div>
    <!-- État vide -->
    <div
      v-if="!loading && articles.length === 0"
      class="flex flex-col items-center justify-center py-20 text-center"
    >
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <p class="text-gray-500 font-medium">Aucun article trouvé</p>
      <p class="text-sm text-gray-400 mt-1">Revenez bientôt pour de nouveaux contenus.</p>
    </div>

    <!-- Grille responsive : 1 col mobile / 2 col md / 3 col lg -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Skeleton loaders -->
      <template v-if="loading">
        <div
          v-for="i in SKELETON_COUNT"
          :key="`skeleton-${i}`"
          class="bg-white rounded-2xl overflow-hidden border border-gray-100 animate-pulse"
        >
          <!-- Image skeleton -->
          <div class="aspect-video bg-gray-200" />
          <!-- Contenu skeleton -->
          <div class="p-5 flex flex-col gap-3">
            <div class="h-4 bg-gray-200 rounded w-3/4" />
            <div class="h-3 bg-gray-200 rounded w-full" />
            <div class="h-3 bg-gray-200 rounded w-5/6" />
            <div class="h-3 bg-gray-200 rounded w-4/6" />
            <div class="flex items-center justify-between pt-2">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200" />
                <div class="h-3 bg-gray-200 rounded w-20" />
              </div>
              <div class="h-3 bg-gray-200 rounded w-16" />
            </div>
          </div>
        </div>
      </template>

      <!-- Articles réels -->
      <ArticleCard
        v-else
        v-for="article in articles"
        :key="article.id"
        :article="article"
      />
    </div>
  </div>
</template>
