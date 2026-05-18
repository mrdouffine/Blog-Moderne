<script setup lang="ts">
// ============================================================
// AdminNav — sidebar de navigation pour l'espace admin
// ============================================================
const authStore = useAuthStore()
const { logout } = useAuth()
const route = useRoute()

const navItems = [
  {
    label: 'Dashboard',
    to: '/admin',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    exact: true,
  },
  {
    label: 'Articles',
    to: '/admin/articles',
    icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    exact: false,
  },
  {
    label: 'Commentaires',
    to: '/admin/comments',
    icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    exact: false,
  },
  {
    label: 'Médias',
    to: '/admin/medias',
    icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    exact: false,
  },
  {
    label: 'Newsletter',
    to: '/admin/newsletter',
    icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    exact: false,
  },
]

// Vérifie si un lien est actif
const isActive = (item: typeof navItems[0]) => {
  if (item.exact) return route.path === item.to
  return route.path.startsWith(item.to)
}

const handleLogout = async () => {
  await logout()
  navigateTo('/')
}
</script>

<template>
  <aside class="flex flex-col h-full w-64 bg-gray-900 text-white">
    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-5 border-b border-gray-800">
      <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-lg flex items-center justify-center flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
        </svg>
      </div>
      <div class="min-w-0">
        <p class="text-sm font-bold text-white truncate">MonBlog</p>
        <p class="text-xs text-gray-400 truncate">Administration</p>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
          isActive(item)
            ? 'bg-indigo-600 text-white shadow-sm'
            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
        ]"
      >
        <!-- Icône -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-4 h-4 flex-shrink-0"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
        </svg>
        {{ item.label }}
      </NuxtLink>
    </nav>

    <!-- Footer : infos utilisateur + lien public -->
    <div class="border-t border-gray-800 px-3 py-4 space-y-1">
      <!-- Lien vers le blog public -->
      <NuxtLink
        to="/"
        class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Voir le blog
      </NuxtLink>

      <!-- Utilisateur connecté -->
      <div class="flex items-center gap-3 px-3 py-2.5 mt-1">
        <div class="w-8 h-8 bg-indigo-900 text-indigo-300 rounded-full flex items-center justify-center text-sm font-bold uppercase flex-shrink-0">
          {{ authStore.user?.name?.charAt(0) }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-semibold text-white truncate">{{ authStore.user?.name }}</p>
          <p class="text-xs text-gray-500 truncate">{{ authStore.user?.email }}</p>
        </div>
        <!-- Déconnexion -->
        <button
          @click="handleLogout"
          class="p-1.5 text-gray-500 hover:text-red-400 transition-colors rounded-lg hover:bg-gray-800"
          title="Se déconnecter"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </aside>
</template>
