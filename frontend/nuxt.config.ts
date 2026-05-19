// =============================================================================
// blog/frontend/nuxt.config.ts
// Configuration principale de l'application Nuxt 3
// =============================================================================

export default defineNuxtConfig({
  // ---------------------------------------------------------------------------
  // Modules
  // ---------------------------------------------------------------------------
  modules: [
    "@pinia/nuxt", // State management
    "@nuxtjs/tailwindcss", // Styles utilitaires
    "@vueuse/nuxt", // Composables VueUse auto-importés
  ],

  // ---------------------------------------------------------------------------
  // Variables d'environnement accessibles côté client
  // ---------------------------------------------------------------------------
  runtimeConfig: {
    apiUrl: process.env.NUXT_API_URL ?? "http://localhost:8000/api",
    // Variables publiques (exposées au client via useRuntimeConfig().public)
    public: {
      /**
       * URL de base de l'API Laravel.
       * Remplacée par la variable d'environnement NUXT_PUBLIC_API_URL.
       * Défaut : http://localhost:8000/api
       */
      apiUrl: process.env.NUXT_PUBLIC_API_URL ?? "http://localhost:8000/api",

      /** Nom du site (utilisé dans les meta tags) */
      siteName: process.env.NUXT_PUBLIC_SITE_NAME ?? "Mon Blog",
    },
  },

  // ---------------------------------------------------------------------------
  // Plugins
  // ---------------------------------------------------------------------------
  plugins: ["~/plugins/api.ts"],

  // ---------------------------------------------------------------------------
  // Auto-imports
  // ---------------------------------------------------------------------------
  imports: {
    // Les composables dans ~/composables/ sont auto-importés par Nuxt
    // Ajout des répertoires de stores pour un accès direct si besoin
    dirs: ["stores", "composables"],
  },

  // ---------------------------------------------------------------------------
  // TypeScript
  // ---------------------------------------------------------------------------
  typescript: {
    strict: true,
    typeCheck: false, // Activer en CI : true (plus lent en dev)
    shim: false,
  },

  // ---------------------------------------------------------------------------
  // CSS global
  // ---------------------------------------------------------------------------
  css: ["~/assets/css/main.css"],

  // ---------------------------------------------------------------------------
  // App (meta, head)
  // ---------------------------------------------------------------------------
  app: {
    head: {
      charset: "utf-8",
      viewport: "width=device-width, initial-scale=1",
      title: "BlogModerne",
      meta: [
        {
          name: "description",
          content:
            "La plateforme de blogging moderne conçue pour les écrivains, les créateurs et les passionnés.",
        },
        { name: "theme-color", content: "#fcf8ff" },
      ],
      link: [
        { rel: "icon", type: "image/x-icon", href: "/favicon.ico" },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@700&family=JetBrains+Mono&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap",
        },
      ],
    },
    // Transition de page douce
    pageTransition: { name: "page", mode: "out-in" },
    layoutTransition: { name: "layout", mode: "out-in" },
  },

  // ---------------------------------------------------------------------------
  // Rendu / SSR
  // ---------------------------------------------------------------------------
  ssr: true,

  // ---------------------------------------------------------------------------
  // Nitro (serveur)
  // ---------------------------------------------------------------------------
  nitro: {
    // Compresser les réponses
    compressPublicAssets: true,
  },

  // ---------------------------------------------------------------------------
  // Vite (bundler)
  // ---------------------------------------------------------------------------
  vite: {
    // Optimisations de développement
    optimizeDeps: {
      include: ["pinia"],
    },
  },

  // ---------------------------------------------------------------------------
  // Dev tools (désactivé en production automatiquement)
  // ---------------------------------------------------------------------------
  devtools: {
    enabled: false,
  },
});
