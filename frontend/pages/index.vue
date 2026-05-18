<script setup lang="ts">
// ============================================================
// Page d'accueil — reproduit pixel-perfect la maquette
// Hero · Featured · Grid 8col + Sidebar 4col · Footer
// ============================================================
useSeoMeta({
    title: "BlogModerne — Partagez vos idées avec le monde",
    description:
        "La plateforme de blogging moderne conçue pour les écrivains, les créateurs et les passionnés.",
});

const { articles, loading, fetchArticles } = useArticles();
const searchQuery = ref("");

// Charge les 4 articles publiés les plus récents pour la grille
await fetchArticles({ status: "published", per_page: 4 });

// Articles de la grille principale (4 premiers)
const gridArticles = computed(() => articles.value.slice(0, 4));
// Article featured (le premier)
const featuredArticle = computed(() => articles.value[0] ?? null);

// Navigation vers la recherche
const handleSearch = () => {
    if (searchQuery.value.trim()) {
        navigateTo(
            `/articles?search=${encodeURIComponent(searchQuery.value.trim())}`,
        );
    }
};

// Tags populaires
const popularTags = ["#Technologie", "#Design", "#Productivité", "#IA"];

// Articles récents sidebar (simulés ou tirés du store)
const recentSidebarArticles = computed(() => articles.value.slice(0, 3));
</script>

<template>
    <div class="bg-surface font-body-md text-on-surface">
        <!-- ════════════════════════════════════════════════════════
         HERO SECTION
         ════════════════════════════════════════════════════════ -->
        <section
            class="py-xl flex flex-col items-center text-center px-sm md:px-lg max-w-container-max mx-auto"
        >
            <!-- Titre principal — Playfair Display -->
            <h1
                class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-surface mb-md"
            >
                Partagez vos idées avec le monde
            </h1>

            <!-- Sous-titre -->
            <p
                class="font-body-lg text-body-lg text-on-surface-variant max-w-[600px] mb-lg"
            >
                La plateforme de blogging moderne conçue pour les écrivains, les
                créateurs et les passionnés. L'élégance du papier rencontre la
                puissance du web.
            </p>

            <!-- Barre de recherche -->
            <div
                class="w-full max-w-[600px] relative mb-md group transition-transform duration-200"
            >
                <span
                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline group-focus-within:text-primary transition-colors select-none"
                >
                    search
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Rechercher un sujet, un auteur..."
                    class="w-full pl-12 pr-4 py-4 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all font-body-md text-body-md text-on-surface placeholder:text-outline outline-none shadow-sm"
                    @keydown.enter="handleSearch"
                />
            </div>

            <!-- Tags populaires -->
            <div class="flex flex-wrap justify-center gap-xs">
                <NuxtLink
                    v-for="tag in popularTags"
                    :key="tag"
                    :to="`/articles?search=${encodeURIComponent(tag.replace('#', ''))}`"
                    class="bg-surface-container-high hover:bg-surface-container-highest px-4 py-1.5 rounded-full font-label-sm text-label-sm text-on-surface-variant cursor-pointer transition-colors"
                >
                    {{ tag }}
                </NuxtLink>
            </div>
        </section>

        <main class="max-w-container-max mx-auto px-sm md:px-lg">
            <!-- ════════════════════════════════════════════════════
           FEATURED SECTION — Article à la une
           ════════════════════════════════════════════════════ -->
            <section class="mb-xl">
                <!-- Skeleton featured -->
                <div
                    v-if="loading"
                    class="relative rounded-xl bg-surface-container-low border border-outline-variant/30 overflow-hidden"
                >
                    <div
                        class="flex flex-col md:flex-row gap-lg items-center p-sm md:p-lg"
                    >
                        <div
                            class="w-full md:w-3/5 aspect-video rounded-lg skeleton"
                        />
                        <div class="w-full md:w-2/5 flex flex-col gap-sm">
                            <div class="h-4 w-24 skeleton rounded-full" />
                            <div class="h-8 w-full skeleton rounded-lg" />
                            <div class="h-4 w-full skeleton rounded" />
                            <div class="h-4 w-4/5 skeleton rounded" />
                        </div>
                    </div>
                </div>

                <!-- Article featured réel -->
                <NuxtLink
                    v-else-if="featuredArticle"
                    :to="`/articles/${featuredArticle.slug}`"
                    class="relative group cursor-pointer overflow-hidden rounded-xl bg-surface-container-low flex flex-col md:flex-row gap-lg items-center p-sm md:p-lg border border-outline-variant/30 hover:border-primary/30 transition-all shadow-sm hover:shadow-md block"
                >
                    <!-- Image -->
                    <div
                        class="w-full md:w-3/5 aspect-video md:aspect-[16/9] rounded-lg overflow-hidden flex-shrink-0"
                    >
                        <img
                            :src="
                                featuredArticle.cover_image ??
                                'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=1200&q=80'
                            "
                            :alt="featuredArticle.title"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            loading="eager"
                        />
                    </div>

                    <!-- Contenu texte -->
                    <div class="w-full md:w-2/5 flex flex-col gap-sm">
                        <span
                            class="text-secondary font-label-sm text-label-sm uppercase tracking-wider"
                            >À la une</span
                        >

                        <h2
                            class="font-display-lg text-display-lg-mobile md:text-headline-md leading-tight text-on-surface"
                        >
                            {{ featuredArticle.title }}
                        </h2>

                        <p
                            class="font-body-md text-body-md text-on-surface-variant line-clamp-3"
                        >
                            {{ featuredArticle.excerpt }}
                        </p>

                        <!-- Auteur + date -->
                        <div class="flex items-center gap-sm mt-base">
                            <img
                                v-if="featuredArticle.author?.avatar"
                                :src="featuredArticle.author.avatar"
                                :alt="featuredArticle.author.name"
                                class="w-8 h-8 rounded-full border border-primary-container"
                            />
                            <div
                                v-else
                                class="w-8 h-8 rounded-full border border-primary-container bg-primary-container flex items-center justify-center font-label-sm text-on-primary-fixed-variant uppercase flex-shrink-0"
                            >
                                {{
                                    featuredArticle.author?.name?.charAt(0) ??
                                    "?"
                                }}
                            </div>
                            <div class="flex flex-col">
                                <span
                                    class="font-label-sm text-label-sm text-on-surface"
                                >
                                    {{
                                        featuredArticle.author?.name ?? "Auteur"
                                    }}
                                </span>
                                <span class="text-[12px] text-outline">
                                    {{
                                        new Date(
                                            featuredArticle.published_at ??
                                                featuredArticle.created_at,
                                        ).toLocaleDateString("fr-FR", {
                                            day: "numeric",
                                            month: "short",
                                            year: "numeric",
                                        })
                                    }}
                                    ·
                                    {{
                                        featuredArticle.reading_time ?? "?"
                                    }}
                                    min de lecture
                                </span>
                            </div>
                        </div>
                    </div>
                </NuxtLink>
            </section>

            <!-- ════════════════════════════════════════════════════
           GRILLE ARTICLES + SIDEBAR
           ════════════════════════════════════════════════════ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-xl mb-xl">
                <!-- ── Grille articles (8 colonnes) ─────────────────── -->
                <div class="lg:col-span-8">
                    <!-- Skeletons -->
                    <div
                        v-if="loading"
                        class="grid grid-cols-1 md:grid-cols-2 gap-md"
                    >
                        <div
                            v-for="i in 4"
                            :key="i"
                            class="rounded-xl overflow-hidden border border-outline-variant/30 bg-surface-container-lowest"
                        >
                            <div class="aspect-video skeleton" />
                            <div class="p-md flex flex-col gap-sm">
                                <div class="h-3 w-20 skeleton rounded-full" />
                                <div class="h-6 w-full skeleton rounded" />
                                <div class="h-4 w-full skeleton rounded" />
                                <div class="h-4 w-3/4 skeleton rounded" />
                            </div>
                        </div>
                    </div>

                    <!-- Articles réels -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <ArticleArticleCard
                            v-for="article in gridArticles"
                            :key="article.id"
                            :article="article"
                        />
                    </div>

                    <!-- Lien voir plus -->
                    <div class="mt-lg flex justify-center">
                        <NuxtLink
                            to="/articles"
                            class="flex items-center gap-sm px-6 py-3 rounded-full border border-outline-variant text-on-surface-variant font-label-sm text-label-sm hover:border-primary hover:text-primary transition-all"
                        >
                            Voir tous les articles
                            <span class="material-symbols-outlined text-[18px]"
                                >arrow_forward</span
                            >
                        </NuxtLink>
                    </div>
                </div>

                <!-- ── Sidebar (4 colonnes) ──────────────────────────── -->
                <aside class="lg:col-span-4 flex flex-col gap-md">
                    <!-- Newsletter widget -->
                    <div
                        class="bg-primary text-on-primary p-md rounded-xl shadow-md"
                    >
                        <h4 class="font-headline-md text-headline-md mb-xs">
                            Restez curieux
                        </h4>
                        <p class="font-body-md text-body-md opacity-90 mb-md">
                            Recevez une sélection hebdomadaire des meilleurs
                            articles directement dans votre boîte mail.
                        </p>
                        <NewsletterSubscribeForm :dark="true" />
                    </div>

                    <!-- Tags populaires -->
                    <div
                        class="bg-surface-container-low p-md rounded-xl border border-outline-variant/30"
                    >
                        <h4
                            class="font-label-sm text-label-sm text-outline uppercase tracking-widest mb-md"
                        >
                            Sujets populaires
                        </h4>
                        <div class="flex flex-wrap gap-xs">
                            <NuxtLink
                                v-for="tag in [
                                    'JavaScript',
                                    'Product Design',
                                    'Web3',
                                    'Python',
                                    'SaaS',
                                    'Management',
                                ]"
                                :key="tag"
                                :to="`/articles?search=${encodeURIComponent(tag)}`"
                                class="px-3 py-1 bg-surface-container-lowest border border-outline-variant/50 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:border-primary hover:text-primary transition-all"
                            >
                                {{ tag }}
                            </NuxtLink>
                        </div>
                    </div>

                    <!-- Articles récemment publiés -->
                    <div
                        class="bg-surface-container-low p-md rounded-xl border border-outline-variant/30"
                    >
                        <h4
                            class="font-label-sm text-label-sm text-outline uppercase tracking-widest mb-md"
                        >
                            Récemment publiés
                        </h4>

                        <!-- Skeletons -->
                        <div v-if="loading" class="flex flex-col gap-md">
                            <div
                                v-for="i in 3"
                                :key="i"
                                class="flex gap-sm items-center"
                            >
                                <div
                                    class="w-16 h-16 flex-shrink-0 rounded-lg skeleton"
                                />
                                <div class="flex flex-col gap-xs flex-1">
                                    <div class="h-3 w-full skeleton rounded" />
                                    <div class="h-3 w-3/4 skeleton rounded" />
                                </div>
                            </div>
                        </div>

                        <!-- Liste réelle -->
                        <div v-else class="flex flex-col gap-md">
                            <NuxtLink
                                v-for="article in recentSidebarArticles"
                                :key="article.id"
                                :to="`/articles/${article.slug}`"
                                class="group flex gap-sm items-center"
                            >
                                <div
                                    class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden"
                                >
                                    <img
                                        :src="
                                            article.cover_image ??
                                            'https://images.unsplash.com/photo-1516116216624-53e697fedbea?w=200&q=80'
                                        "
                                        :alt="article.title"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h5
                                        class="font-label-sm text-label-sm text-on-surface line-clamp-2 group-hover:text-primary transition-colors"
                                    >
                                        {{ article.title }}
                                    </h5>
                                    <span
                                        class="text-[12px] text-outline mt-0.5"
                                    >
                                        {{
                                            new Date(
                                                article.published_at ??
                                                    article.created_at,
                                            ).toLocaleDateString("fr-FR", {
                                                day: "numeric",
                                                month: "short",
                                            })
                                        }}
                                    </span>
                                </div>
                            </NuxtLink>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <!-- ════════════════════════════════════════════════════════
         FOOTER
         ════════════════════════════════════════════════════════ -->
        <footer
            class="bg-surface-container-lowest border-t border-outline-variant"
        >
            <div
                class="w-full py-xl px-sm md:px-lg max-w-container-max mx-auto flex flex-col md:flex-row justify-between gap-xl"
            >
                <!-- Bloc brand -->
                <div class="flex flex-col gap-md max-w-[300px]">
                    <div
                        class="font-display-lg text-display-lg-mobile text-on-surface"
                    >
                        BlogModerne
                    </div>
                    <p
                        class="font-body-md text-body-md text-on-surface-variant"
                    >
                        Une plateforme de pensée libre pour les esprits curieux.
                        Écrivez, lisez, apprenez.
                    </p>
                    <div class="flex gap-md">
                        <a
                            href="#"
                            class="text-outline hover:text-primary transition-colors"
                            aria-label="Email"
                        >
                            <span class="material-symbols-outlined"
                                >alternate_email</span
                            >
                        </a>
                        <a
                            href="#"
                            class="text-outline hover:text-primary transition-colors"
                            aria-label="RSS"
                        >
                            <span class="material-symbols-outlined"
                                >rss_feed</span
                            >
                        </a>
                        <a
                            href="#"
                            class="text-outline hover:text-primary transition-colors"
                            aria-label="Forum"
                        >
                            <span class="material-symbols-outlined">forum</span>
                        </a>
                    </div>
                </div>

                <!-- Colonnes de liens -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-xl">
                    <!-- Navigation -->
                    <div class="flex flex-col gap-md">
                        <span
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest"
                            >Navigation</span
                        >
                        <ul
                            class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant"
                        >
                            <li>
                                <NuxtLink
                                    to="/"
                                    class="hover:text-primary transition-colors"
                                    >Accueil</NuxtLink
                                >
                            </li>
                            <li>
                                <NuxtLink
                                    to="/articles"
                                    class="hover:text-primary transition-colors"
                                    >Articles</NuxtLink
                                >
                            </li>
                            <li>
                                <NuxtLink
                                    to="/newsletter"
                                    class="hover:text-primary transition-colors"
                                    >Newsletter</NuxtLink
                                >
                            </li>
                        </ul>
                    </div>
                    <!-- Légal -->
                    <div class="flex flex-col gap-md">
                        <span
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest"
                            >Légal</span
                        >
                        <ul
                            class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant"
                        >
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                    >Conditions d'utilisation</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                    >Confidentialité</a
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                    >Aide</a
                                >
                            </li>
                        </ul>
                    </div>
                    <!-- Admin -->
                    <div class="flex flex-col gap-md">
                        <span
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest"
                            >Admin</span
                        >
                        <ul
                            class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant"
                        >
                            <li>
                                <NuxtLink
                                    to="/admin"
                                    class="hover:text-primary transition-colors"
                                    >Dashboard</NuxtLink
                                >
                            </li>
                            <li>
                                <NuxtLink
                                    to="/auth/login"
                                    class="hover:text-primary transition-colors"
                                    >Profil</NuxtLink
                                >
                            </li>
                            <li>
                                <a
                                    href="#"
                                    class="hover:text-primary transition-colors"
                                    >Paramètres</a
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div
                class="w-full py-md px-sm border-t border-outline-variant/30 text-center"
            >
                <span class="font-label-sm text-label-sm text-outline">
                    © {{ new Date().getFullYear() }} BlogModerne. Tous droits
                    réservés.
                </span>
            </div>
        </footer>

        <!-- ════════════════════════════════════════════════════════
         FAB mobile — bouton Écrire (mobile seulement)
         ════════════════════════════════════════════════════════ -->
        <NuxtLink to="/admin/articles/create">
            <button
                class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center hover:scale-110 active:scale-95 transition-all z-40"
            >
                <span class="material-symbols-outlined">edit</span>
            </button>
        </NuxtLink>
    </div>
</template>

<script>
// Effet focus sur la search bar (scroll-scale)
if (process.client) {
    const searchInput = document.querySelector('input[type="text"]');
    if (searchInput) {
        searchInput.addEventListener("focus", () => {
            searchInput.parentElement?.classList.add("scale-[1.02]");
        });
        searchInput.addEventListener("blur", () => {
            searchInput.parentElement?.classList.remove("scale-[1.02]");
        });
    }
}
</script>
