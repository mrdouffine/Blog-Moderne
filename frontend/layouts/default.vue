<script setup lang="ts">
// ============================================================
// Layout public par défaut — header + footer + toasts
// ============================================================
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <!-- Header sticky -->
    <LayoutHeader />

    <!-- Contenu de la page -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-100 bg-gray-50 py-10 mt-auto">
      <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
          <!-- Logo -->
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-md flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
            </div>
            <span class="text-sm font-semibold text-gray-700">MonBlog</span>
          </div>

          <!-- Liens -->
          <nav class="flex items-center gap-5 text-xs text-gray-400">
            <NuxtLink to="/" class="hover:text-gray-600 transition-colors">Accueil</NuxtLink>
            <NuxtLink to="/articles" class="hover:text-gray-600 transition-colors">Articles</NuxtLink>
          </nav>

          <!-- Copyright -->
          <p class="text-xs text-gray-400">
            &copy; {{ new Date().getFullYear() }} MonBlog. Tous droits réservés.
          </p>
        </div>
      </div>
    </footer>

    <!-- Toasts globaux -->
    <UiToast />
  </div>
</template>
