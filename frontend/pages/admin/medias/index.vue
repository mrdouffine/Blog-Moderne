<script setup lang="ts">
definePageMeta({ middleware: 'auth', layout: 'admin' })
useSeoMeta({ title: 'Médias — Admin BlogModerne' })

const { $api } = useNuxtApp()
const uiStore = useUiStore()

interface Media {
  id: number
  title: string
  cover_image: string
  slug: string
}

const medias = ref<Media[]>([])
const loading = ref(true)
const selectedMedia = ref<string | null>(null)

async function loadMedias() {
  loading.value = true
  try {
    // Single fast query — images with cover only
    const res = await $api<any>('/admin/medias')
    medias.value = res?.data ?? []
  } catch {
    uiStore.error('Impossible de charger les médias.')
  } finally {
    loading.value = false
  }
}

await loadMedias()
</script>

<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Médiathèque</h1>
        <p class="text-sm text-gray-500 mt-1">
          {{ loading ? '...' : `${medias.length} image(s) de couverture` }}
        </p>
      </div>
    </div>

    <!-- Skeleton loading -->
    <div v-if="loading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 animate-pulse">
      <div v-for="i in 8" :key="i" class="aspect-video bg-gray-200 rounded-xl" />
    </div>

    <!-- Empty state -->
    <div v-else-if="medias.length === 0"
      class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm"
    >
      <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-gray-900 mb-2">Aucune image</h2>
      <p class="text-gray-500">Aucun article n'a encore d'image de couverture.</p>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="media in medias"
        :key="media.id"
        class="group relative bg-gray-100 rounded-xl overflow-hidden aspect-video cursor-pointer shadow-sm hover:shadow-md transition-shadow"
        @click="selectedMedia = media.cover_image"
      >
        <img
          :src="media.cover_image"
          :alt="media.title"
          loading="lazy"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        />
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-end">
          <div class="p-3 translate-y-full group-hover:translate-y-0 transition-transform w-full">
            <p class="text-white text-xs font-semibold truncate">{{ media.title }}</p>
            <NuxtLink :to="`/admin/articles/${media.id}/edit`" class="text-white/80 text-xs hover:text-white mt-0.5 block" @click.stop>
              Modifier l'article →
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <div v-if="selectedMedia" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-6" @click="selectedMedia = null">
        <button class="absolute top-4 right-4 text-white text-3xl leading-none hover:text-gray-300 transition-colors" @click="selectedMedia = null">✕</button>
        <img :src="selectedMedia" class="max-w-full max-h-full rounded-xl shadow-2xl" @click.stop />
      </div>
    </Teleport>
  </div>
</template>
