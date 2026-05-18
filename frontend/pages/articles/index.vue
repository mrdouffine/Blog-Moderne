<script setup lang="ts">
// ============================================================
// Liste publique des articles avec recherche et pagination
// ============================================================
useSeoMeta({
  title: 'Articles — MonBlog',
  description: 'Parcourez tous nos articles.',
})

const { articles, loading, pagination, fetchArticles } = useArticles()

// Paramètres de filtrage et pagination
const search   = ref('')
const page     = ref(1)
const perPage  = ref(9)

const perPageOptions = [6, 9, 12, 18]

// Charge les articles (only published côté public)
const load = async () => {
  await fetchArticles({
    status:   'published',
    search:   search.value || undefined,
    page:     page.value,
    per_page: perPage.value,
  })
}

// Debounce de la recherche : attend 400ms après la frappe
let searchTimer: ReturnType<typeof setTimeout>
const onSearchInput = () => {
  clearTimeout(searchTimer)
  page.value = 1
  searchTimer = setTimeout(load, 400)
}

const onPageChange = (p: number) => {
  page.value = p
  window.scrollTo({ top: 0, behavior: 'smooth' })
  load()
}

const onPerPageChange = () => {
  page.value = 1
  load()
}

// Charge initialement
await load()
</script>

<template>
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <!-- En-tête de la page -->
    <div class="mb-10">
      <p class="text-sm font-medium text-indigo-600 mb-1">Bibliothèque</p>
      <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">Tous les articles</h1>
      <p class="mt-2 text-gray-500">Explorez l'ensemble de nos publications.</p>
    </div>

    <!-- Barre de filtres -->
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
      <!-- Recherche -->
      <div class="relative flex-1">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="search"
          @input="onSearchInput"
          type="search"
          placeholder="Rechercher un article..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
        />
      </div>

      <!-- Sélecteur par page -->
      <select
        v-model="perPage"
        @change="onPerPageChange"
        class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white cursor-pointer"
      >
        <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} par page</option>
      </select>
    </div>

    <!-- Liste d'articles -->
    <ArticleList :articles="articles" :loading="loading" />

    <!-- Pagination -->
    <div
      v-if="!loading && pagination && pagination.last_page > 1"
      class="mt-12 flex items-center justify-center gap-2"
    >
      <!-- Précédent -->
      <button
        @click="onPageChange(page - 1)"
        :disabled="page <= 1"
        class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        aria-label="Page précédente"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Numéros de pages -->
      <button
        v-for="p in pagination.last_page"
        :key="p"
        @click="onPageChange(p)"
        :class="[
          'w-9 h-9 rounded-lg text-sm font-medium transition-colors',
          p === page
            ? 'bg-indigo-600 text-white'
            : 'border border-gray-200 text-gray-600 hover:bg-gray-50',
        ]"
      >
        {{ p }}
      </button>

      <!-- Suivant -->
      <button
        @click="onPageChange(page + 1)"
        :disabled="page >= pagination.last_page"
        class="p-2 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors"
        aria-label="Page suivante"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>

    <!-- Infos pagination -->
    <p
      v-if="!loading && pagination"
      class="text-center text-xs text-gray-400 mt-4"
    >
      {{ pagination.total }} article(s) — page {{ page }}/{{ pagination.last_page }}
    </p>
  </div>
</template>
