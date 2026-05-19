<script setup lang="ts">
// ============================================================
// Dashboard admin — vue d'ensemble
// ============================================================
definePageMeta({ middleware: 'auth', layout: 'admin' })

useSeoMeta({ title: 'Dashboard — Admin' })

const loading = ref(true)
const stats = ref({
  articles_count: 0,
  comments_count: 0,
  subscribers_count: 0,
  total_views: 0
})

const { $api } = useNuxtApp()

try {
  const res = await $api('/admin/stats')
  if (res && res.data) {
    stats.value = res.data
  }
} catch (e) {
  console.error("Failed to load stats", e)
} finally {
  loading.value = false
}

// Cartes de statistiques
const statCards = computed(() => [
  {
    label: 'Articles',
    value: stats.value?.articles_count ?? 0,
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    color: 'text-indigo-600',
    bg:    'bg-indigo-50',
    to:    '/admin/articles',
  },
  {
    label: 'Commentaires',
    value: stats.value?.comments_count ?? 0,
    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    color: 'text-violet-600',
    bg:    'bg-violet-50',
    to:    '/admin/comments',
  },
  {
    label: 'Abonnés newsletter',
    value: stats.value?.subscribers_count ?? 0,
    icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    color: 'text-emerald-600',
    bg:    'bg-emerald-50',
    to:    '/admin/newsletter',
  },
  {
    label: 'Vues totales',
    value: stats.value?.total_views ?? 0,
    icon: 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
    color: 'text-amber-600',
    bg:    'bg-amber-50',
    to:    '/articles',
  },
])

// Accès rapides
const quickActions = [
  { label: 'Nouvel article',    to: '/admin/articles/create', icon: 'M12 4v16m8-8H4',    primary: true },
  { label: 'Gérer les articles', to: '/admin/articles',       icon: 'M4 6h16M4 10h16M4 14h16M4 18h16', primary: false },
  { label: 'Newsletter',        to: '/admin/newsletter',      icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', primary: false },
]

// Formate les grands nombres
const formatNumber = (n: number) =>
  n >= 1000 ? `${(n / 1000).toFixed(1)}k` : String(n)
</script>

<template>
  <div class="p-6 space-y-8">
    <!-- Titre -->
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
      <p class="text-sm text-gray-500 mt-1">Vue d'ensemble de votre blog</p>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
      <div
        v-for="card in statCards"
        :key="card.label"
        class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Skeleton -->
        <div v-if="loading" class="animate-pulse space-y-3">
          <div class="w-10 h-10 bg-gray-200 rounded-xl" />
          <div class="h-7 bg-gray-200 rounded w-16" />
          <div class="h-3 bg-gray-200 rounded w-24" />
        </div>

        <template v-else>
          <div class="flex items-start justify-between">
            <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', card.bg]">
              <svg xmlns="http://www.w3.org/2000/svg" :class="['w-5 h-5', card.color]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon" />
              </svg>
            </div>
            <NuxtLink :to="card.to" class="text-xs text-gray-400 hover:text-indigo-600 transition-colors">
              Voir →
            </NuxtLink>
          </div>
          <p class="mt-3 text-3xl font-extrabold text-gray-900">
            {{ formatNumber(card.value) }}
          </p>
          <p class="text-sm text-gray-500 mt-0.5">{{ card.label }}</p>
        </template>
      </div>
    </div>

    <!-- Accès rapides -->
    <div>
      <h2 class="text-base font-semibold text-gray-700 mb-4">Accès rapides</h2>
      <div class="flex flex-wrap gap-3">
        <NuxtLink
          v-for="action in quickActions"
          :key="action.to"
          :to="action.to"
        >
          <UiButton :variant="action.primary ? 'primary' : 'secondary'" size="sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="action.icon" />
            </svg>
            {{ action.label }}
          </UiButton>
        </NuxtLink>
      </div>
    </div>

    <!-- Récapitulatif visuel -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
      <h2 class="text-base font-semibold text-gray-700 mb-5">Activité récente</h2>

      <div v-if="loading" class="space-y-3 animate-pulse">
        <div v-for="i in 4" :key="i" class="flex items-center gap-4">
          <div class="w-8 h-8 bg-gray-200 rounded-full" />
          <div class="flex-1 h-3 bg-gray-200 rounded" />
          <div class="w-16 h-3 bg-gray-200 rounded" />
        </div>
      </div>

      <!-- Mini chart indicatif (barres proportionnelles) -->
      <div v-else class="space-y-4">
        <div
          v-for="card in statCards"
          :key="card.label"
          class="flex items-center gap-4"
        >
          <div :class="['w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0', card.bg]">
            <svg xmlns="http://www.w3.org/2000/svg" :class="['w-4 h-4', card.color]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="card.icon" />
            </svg>
          </div>
          <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-medium text-gray-600">{{ card.label }}</span>
              <span class="text-xs font-semibold text-gray-900">{{ formatNumber(card.value) }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div
                :class="['h-full rounded-full', card.bg.replace('bg-', 'bg-').replace('-50', '-400')]"
                :style="{
                  width: card.value > 0
                    ? `${Math.min(100, (card.value / (stats?.total_views || 1)) * 100)}%`
                    : '2%'
                }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
