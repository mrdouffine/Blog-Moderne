<script setup lang="ts">
// =============================================================================
// blog/frontend/pages/articles/[slug].vue
// Page détail d'un article — reproduction pixel-perfect de la maquette
// =============================================================================

definePageMeta({ layout: "default" });

const route = useRoute();
const slug = route.params.slug as string;

// ── Articles ─────────────────────────────────────────────────────────────────
const { currentArticle: article, loading, fetchArticle } = useArticles();
const { articles: relatedArticles, fetchArticles } = useArticles();

// ── Commentaires ─────────────────────────────────────────────────────────────
const {
    comments,
    loading: commentsLoading,
    fetchComments,
    addComment,
} = useComments();

// ── Auth ──────────────────────────────────────────────────────────────────────
const authStore = useAuthStore();

// ── Fetch initial ─────────────────────────────────────────────────────────────
await fetchArticle(slug);
if (!article.value) {
    throw createError({ statusCode: 404, message: "Article introuvable" });
}

await Promise.all([
    fetchComments(article.value.id),
    fetchArticles({ status: "published", per_page: 3 }),
]);

// ── SEO ───────────────────────────────────────────────────────────────────────
useSeoMeta({
    title: computed(() => `${article.value?.title ?? ""} — BlogModerne`),
    description: computed(() => article.value?.excerpt ?? ""),
    ogImage: computed(() => article.value?.cover_image ?? undefined),
});

// ── Table of contents ─────────────────────────────────────────────────────────
const activeSection = ref("introduction");

const tocLinks = [
    { id: "introduction", label: "Introduction" },
    { id: "setup", label: "Configuration de Nuxt 3" },
    { id: "integration", label: "Intégration TailwindCSS" },
    { id: "optimisation", label: "Optimisation & Performance" },
    { id: "conclusion", label: "Conclusion" },
];

// IntersectionObserver côté client
onMounted(() => {
    if (typeof IntersectionObserver === "undefined") return;

    const observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    activeSection.value = entry.target.id;
                }
            }
        },
        { rootMargin: "-20% 0px -70% 0px" },
    );

    tocLinks.forEach(({ id }) => {
        const el = document.getElementById(id);
        if (el) observer.observe(el);
    });

    onUnmounted(() => observer.disconnect());
});

// ── Commentaire form ─────────────────────────────────────────────────────────
const commentText = ref("");
const commentLoading = ref(false);

const onSubmitComment = async () => {
    if (!commentText.value.trim() || !article.value) return;
    commentLoading.value = true;
    await addComment(article.value.id, commentText.value.trim());
    commentText.value = "";
    commentLoading.value = false;
};

// ── Helpers formatage ────────────────────────────────────────────────────────
function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
}

function relativeDate(dateStr: string): string {
    const diff = Date.now() - new Date(dateStr).getTime();
    const mins = Math.floor(diff / 60_000);
    const hours = Math.floor(diff / 3_600_000);
    const days = Math.floor(diff / 86_400_000);

    if (mins < 1) return "À l'instant";
    if (mins < 60) return `Il y a ${mins} min`;
    if (hours < 24) return `Il y a ${hours} h`;
    if (days < 7) return `Il y a ${days} j`;
    return formatDate(dateStr);
}

function authorInitial(name: string): string {
    return name?.charAt(0)?.toUpperCase() ?? "?";
}
</script>

<template>
    <div class="bg-surface min-h-screen font-body-md text-on-surface">
        <!-- ══════════════════════════════════════════════════════════════════════
         1. TOP NAV BAR
         ══════════════════════════════════════════════════════════════════════ -->
        <header
            class="sticky top-0 z-50 h-16 bg-surface/80 backdrop-blur-md border-b border-outline-variant"
        >
            <div
                class="max-w-container-max mx-auto px-sm md:px-lg h-full flex items-center justify-between gap-md"
            >
                <!-- Logo -->
                <NuxtLink
                    to="/"
                    class="font-display-lg text-display-lg-mobile text-on-surface tracking-tight shrink-0"
                >
                    BlogModerne
                </NuxtLink>

                <!-- Nav desktop -->
                <nav class="hidden md:flex items-center gap-lg">
                    <NuxtLink
                        to="/"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Accueil
                    </NuxtLink>
                    <NuxtLink
                        to="/articles"
                        class="font-label-sm text-label-sm text-primary border-b-2 border-primary pb-px"
                    >
                        Articles
                    </NuxtLink>
                    <NuxtLink
                        to="/a-propos"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        À propos
                    </NuxtLink>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-sm">
                    <NuxtLink
                        v-if="authStore.isAuthor || authStore.isAdmin"
                        to="/admin/articles/create"
                        class="hidden sm:inline-flex items-center gap-xs bg-primary text-on-primary rounded-full px-md py-xs font-label-sm text-label-sm hover:opacity-90 transition-opacity"
                    >
                        Écrire
                    </NuxtLink>
                    <!-- Avatar -->
                    <button
                        v-if="authStore.isAuthenticated"
                        class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm text-label-sm shrink-0 overflow-hidden"
                    >
                        <img
                            v-if="authStore.user?.avatar"
                            :src="authStore.user.avatar"
                            :alt="authStore.user.name"
                            class="w-full h-full object-cover"
                        />
                        <span v-else>{{
                            authorInitial(authStore.user?.name ?? "")
                        }}</span>
                    </button>
                    <NuxtLink
                        v-else
                        to="/auth/login"
                        class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center hover:bg-surface-container-high transition-colors"
                        aria-label="Se connecter"
                    >
                        <span
                            class="material-symbols-outlined text-[20px] text-on-surface-variant"
                            >person</span
                        >
                    </NuxtLink>
                </div>
            </div>
        </header>

        <!-- ══════════════════════════════════════════════════════════════════════
         2. MAIN CONTENT
         ══════════════════════════════════════════════════════════════════════ -->
        <main class="max-w-container-max mx-auto px-sm md:px-lg py-md">
            <!-- ── Breadcrumb ──────────────────────────────────────────────────── -->
            <nav
                aria-label="Fil d'Ariane"
                class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant mb-md flex-wrap"
            >
                <NuxtLink to="/" class="hover:text-primary transition-colors"
                    >Accueil</NuxtLink
                >
                <span class="material-symbols-outlined text-[16px] text-outline"
                    >chevron_right</span
                >
                <NuxtLink
                    to="/articles"
                    class="hover:text-primary transition-colors"
                    >Articles</NuxtLink
                >
                <span class="material-symbols-outlined text-[16px] text-outline"
                    >chevron_right</span
                >
                <span class="text-primary font-bold">Développement</span>
            </nav>

            <!-- ── Layout grid 12 colonnes ─────────────────────────────────────── -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-lg items-start">
                <!-- ================================================================
             LEFT SIDEBAR — TOC + Partager
             ================================================================ -->
                <aside class="hidden lg:block lg:col-span-3 sticky top-24">
                    <!-- Titre TOC -->
                    <p
                        class="font-label-sm text-label-sm uppercase tracking-wider text-outline mb-sm"
                    >
                        Sur cette page
                    </p>

                    <!-- Liste TOC -->
                    <ul class="flex flex-col gap-1 mb-md">
                        <li v-for="link in tocLinks" :key="link.id">
                            <a
                                :href="`#${link.id}`"
                                :class="[
                                    'block border-l-2 pl-sm py-1 text-label-sm font-label-sm transition-colors',
                                    activeSection === link.id
                                        ? 'border-primary text-primary font-bold'
                                        : 'border-transparent text-on-surface-variant hover:text-primary hover:border-primary/40',
                                ]"
                                @click.prevent="
                                    activeSection = link.id;
                                    document
                                        .getElementById(link.id)
                                        ?.scrollIntoView({
                                            behavior: 'smooth',
                                        });
                                "
                            >
                                {{ link.label }}
                            </a>
                        </li>
                    </ul>

                    <!-- Séparateur -->
                    <hr class="border-t border-outline-variant mb-md" />

                    <!-- Partager -->
                    <p
                        class="font-label-sm text-label-sm text-on-surface-variant mb-sm"
                    >
                        Partager l'article
                    </p>
                    <div class="flex items-center gap-sm">
                        <button
                            class="w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors flex items-center justify-center"
                            aria-label="Partager"
                            @click="
                                navigator.share?.({
                                    title: article?.title,
                                    url: $route.fullPath,
                                })
                            "
                        >
                            <span
                                class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                >share</span
                            >
                        </button>
                        <button
                            class="w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors flex items-center justify-center"
                            aria-label="Sauvegarder"
                        >
                            <span
                                class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                >bookmark</span
                            >
                        </button>
                    </div>
                </aside>

                <!-- ================================================================
             ARTICLE PRINCIPAL
             ================================================================ -->
                <div class="col-span-1 lg:col-span-9">
                    <article class="max-w-content-reading-width mx-auto w-full">
                        <!-- ── Skeleton chargement ──────────────────────────────────── -->
                        <template v-if="loading">
                            <div class="animate-pulse space-y-md">
                                <div
                                    class="h-6 w-32 bg-surface-container rounded-full"
                                />
                                <div
                                    class="h-10 w-full bg-surface-container rounded-lg"
                                />
                                <div
                                    class="h-10 w-4/5 bg-surface-container rounded-lg"
                                />
                                <div
                                    class="h-5 w-2/3 bg-surface-container rounded"
                                />
                                <div
                                    class="flex items-center gap-sm py-md border-y border-outline-variant"
                                >
                                    <div
                                        class="w-12 h-12 rounded-full bg-surface-container shrink-0"
                                    />
                                    <div class="flex flex-col gap-xs flex-1">
                                        <div
                                            class="h-3 w-32 bg-surface-container rounded"
                                        />
                                        <div
                                            class="h-3 w-24 bg-surface-container rounded"
                                        />
                                    </div>
                                </div>
                                <div
                                    class="w-full aspect-video bg-surface-container rounded-xl"
                                />
                                <div class="space-y-3 pt-md">
                                    <div
                                        v-for="i in 10"
                                        :key="i"
                                        :class="[
                                            'h-3 bg-surface-container rounded',
                                            i % 4 === 0 ? 'w-3/4' : 'w-full',
                                        ]"
                                    />
                                </div>
                            </div>
                        </template>

                        <!-- ── Contenu article ─────────────────────────────────────── -->
                        <template v-else-if="article">
                            <!-- Header article -->
                            <header class="mb-lg">
                                <!-- Badge catégorie -->
                                <span
                                    class="inline-block px-sm py-1 bg-primary-container text-on-primary-container rounded-full font-label-sm text-label-sm mb-md"
                                >
                                    Développement
                                </span>

                                <!-- Titre h1 -->
                                <h1
                                    class="font-display-lg text-display-lg-mobile md:text-display-lg text-on-surface mb-sm leading-tight"
                                >
                                    {{ article.title }}
                                </h1>

                                <!-- Sous-titre / excerpt -->
                                <p
                                    v-if="article.excerpt"
                                    class="font-body-lg text-body-lg text-on-surface-variant mb-lg"
                                >
                                    {{ article.excerpt }}
                                </p>

                                <!-- Barre auteur -->
                                <div
                                    class="flex items-center justify-between py-md border-y border-outline-variant gap-md flex-wrap"
                                >
                                    <!-- Gauche : avatar + nom + date + lecture -->
                                    <div class="flex items-center gap-sm">
                                        <div
                                            class="w-12 h-12 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm text-label-sm shrink-0 overflow-hidden"
                                        >
                                            <img
                                                v-if="article.author?.avatar"
                                                :src="article.author.avatar"
                                                :alt="article.author.name"
                                                class="w-full h-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-[18px] font-bold"
                                            >
                                                {{
                                                    authorInitial(
                                                        article.author?.name ??
                                                            "",
                                                    )
                                                }}
                                            </span>
                                        </div>
                                        <div class="flex flex-col gap-0.5">
                                            <span
                                                class="font-label-sm text-label-sm text-on-surface"
                                            >
                                                {{ article.author?.name }}
                                            </span>
                                            <span
                                                class="font-label-sm text-label-sm text-on-surface-variant"
                                            >
                                                {{
                                                    article.published_at
                                                        ? formatDate(
                                                              article.published_at,
                                                          )
                                                        : formatDate(
                                                              article.created_at,
                                                          )
                                                }}
                                                <span class="mx-1">•</span>
                                                {{
                                                    article.reading_time ?? 8
                                                }}
                                                min de lecture
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Droite : boutons action -->
                                    <div class="flex items-center gap-xs">
                                        <button
                                            class="p-xs rounded-full hover:bg-surface-container transition-colors"
                                            aria-label="Copier le lien"
                                        >
                                            <span
                                                class="material-symbols-outlined text-[22px] text-on-surface-variant hover:text-primary transition-colors"
                                                >link</span
                                            >
                                        </button>
                                        <button
                                            class="p-xs rounded-full hover:bg-surface-container transition-colors"
                                            aria-label="Mentionner"
                                        >
                                            <span
                                                class="material-symbols-outlined text-[22px] text-on-surface-variant hover:text-primary transition-colors"
                                                >alternate_email</span
                                            >
                                        </button>
                                    </div>
                                </div>
                            </header>

                            <!-- Image de couverture -->
                            <img
                                v-if="article.cover_image"
                                :src="article.cover_image"
                                :alt="article.title"
                                class="w-full h-auto rounded-xl shadow-md mb-xl object-cover"
                                loading="lazy"
                            />
                            <div
                                v-else
                                class="w-full aspect-video rounded-xl bg-surface-container mb-xl flex items-center justify-center"
                            >
                                <span
                                    class="material-symbols-outlined text-[64px] text-outline"
                                    >image</span
                                >
                            </div>

                            <!-- Corps prose -->
                            <div
                                class="font-body-md text-body-md text-on-surface-variant article-prose"
                                v-html="article.content"
                            />

                            <!-- ── Section commentaires ─────────────────────────────── -->
                            <section
                                class="mt-xl pt-xl border-t border-outline-variant"
                            >
                                <!-- Titre commentaires -->
                                <h2
                                    class="font-headline-md text-headline-md text-on-surface mb-lg"
                                >
                                    Commentaires ({{ comments.length }})
                                </h2>

                                <!-- Formulaire de commentaire -->
                                <form
                                    class="bg-surface-container-low p-md rounded-xl mb-xl"
                                    @submit.prevent="onSubmitComment"
                                >
                                    <label
                                        for="comment-input"
                                        class="block font-label-sm text-label-sm text-on-surface mb-sm"
                                    >
                                        Laissez un commentaire
                                    </label>
                                    <textarea
                                        id="comment-input"
                                        v-model="commentText"
                                        placeholder="Votre commentaire…"
                                        class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-sm mb-sm focus:outline-none focus:ring-2 focus:ring-primary min-h-[120px] resize-y font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/60 transition-shadow"
                                    />
                                    <div class="flex justify-end">
                                        <button
                                            type="submit"
                                            :disabled="
                                                !commentText.trim() ||
                                                commentLoading
                                            "
                                            class="bg-primary text-on-primary px-lg py-xs rounded-full font-label-sm text-label-sm hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span v-if="commentLoading"
                                                >Publication…</span
                                            >
                                            <span v-else>Publier</span>
                                        </button>
                                    </div>
                                </form>

                                <!-- Liste des commentaires -->
                                <div
                                    v-if="commentsLoading"
                                    class="space-y-lg animate-pulse"
                                >
                                    <div
                                        v-for="i in 2"
                                        :key="i"
                                        class="flex gap-sm"
                                    >
                                        <div
                                            class="w-10 h-10 rounded-full bg-surface-container shrink-0"
                                        />
                                        <div
                                            class="flex-1 bg-surface-container rounded-xl p-sm space-y-xs"
                                        >
                                            <div
                                                class="h-3 w-28 bg-surface-container-high rounded"
                                            />
                                            <div
                                                class="h-3 w-full bg-surface-container-high rounded"
                                            />
                                            <div
                                                class="h-3 w-3/4 bg-surface-container-high rounded"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="space-y-lg">
                                    <!-- Commentaire vide -->
                                    <p
                                        v-if="comments.length === 0"
                                        class="font-body-md text-body-md text-on-surface-variant text-center py-lg"
                                    >
                                        Soyez le premier à commenter cet article
                                        !
                                    </p>

                                    <!-- Commentaires réels -->
                                    <template
                                        v-for="comment in comments"
                                        :key="comment.id"
                                    >
                                        <div class="flex gap-sm">
                                            <!-- Avatar -->
                                            <div
                                                class="w-10 h-10 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm text-label-sm shrink-0 overflow-hidden"
                                            >
                                                <img
                                                    v-if="
                                                        comment.author?.avatar
                                                    "
                                                    :src="comment.author.avatar"
                                                    :alt="comment.author.name"
                                                    class="w-full h-full object-cover"
                                                />
                                                <span v-else>{{
                                                    authorInitial(
                                                        comment.author?.name ??
                                                            "",
                                                    )
                                                }}</span>
                                            </div>

                                            <!-- Bulle commentaire -->
                                            <div
                                                class="flex-1 bg-surface-container rounded-xl p-sm"
                                            >
                                                <div
                                                    class="flex items-center justify-between gap-sm mb-xs flex-wrap"
                                                >
                                                    <span
                                                        class="font-label-sm text-label-sm text-on-surface"
                                                    >
                                                        {{
                                                            comment.author?.name
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-[12px] text-on-surface-variant"
                                                    >
                                                        {{
                                                            relativeDate(
                                                                comment.created_at,
                                                            )
                                                        }}
                                                    </span>
                                                </div>
                                                <p
                                                    class="font-body-md text-body-md text-on-surface-variant leading-relaxed"
                                                >
                                                    {{ comment.content }}
                                                </p>
                                                <button
                                                    class="mt-sm font-label-sm text-label-sm text-primary hover:underline ml-sm"
                                                    type="button"
                                                >
                                                    Répondre
                                                </button>

                                                <!-- Réponse imbriquée (exemple statique — extensible) -->
                                                <div
                                                    v-if="
                                                        article?.author?.id ===
                                                        comment.author?.id
                                                    "
                                                    class="mt-md ml-lg border-l-2 border-outline-variant pl-md flex gap-sm"
                                                >
                                                    <div
                                                        class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm text-label-sm shrink-0 overflow-hidden"
                                                    >
                                                        <img
                                                            v-if="
                                                                article.author
                                                                    ?.avatar
                                                            "
                                                            :src="
                                                                article.author
                                                                    .avatar
                                                            "
                                                            :alt="
                                                                article.author
                                                                    .name
                                                            "
                                                            class="w-full h-full object-cover"
                                                        />
                                                        <span v-else>{{
                                                            authorInitial(
                                                                article.author
                                                                    ?.name ??
                                                                    "",
                                                            )
                                                        }}</span>
                                                    </div>
                                                    <div
                                                        class="flex-1 bg-surface-container-high rounded-xl p-sm"
                                                    >
                                                        <div
                                                            class="flex items-center gap-xs mb-xs flex-wrap"
                                                        >
                                                            <span
                                                                class="font-label-sm text-label-sm text-on-surface"
                                                            >
                                                                {{
                                                                    article
                                                                        .author
                                                                        ?.name
                                                                }}
                                                            </span>
                                                            <span
                                                                class="text-[12px] text-primary ml-xs font-normal"
                                                                >(Auteur)</span
                                                            >
                                                        </div>
                                                        <p
                                                            class="font-body-md text-body-md text-on-surface-variant"
                                                        >
                                                            Merci pour votre
                                                            commentaire !
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </section>
                        </template>
                    </article>
                </div>
            </div>
        </main>

        <!-- ══════════════════════════════════════════════════════════════════════
         3. ARTICLES SIMILAIRES
         ══════════════════════════════════════════════════════════════════════ -->
        <section class="bg-surface-container-low py-xl mt-xl">
            <div class="max-w-container-max mx-auto px-sm md:px-lg">
                <h2
                    class="font-headline-md text-headline-md text-on-surface mb-lg"
                >
                    Articles similaires
                </h2>

                <!-- Skeleton -->
                <div
                    v-if="loading"
                    class="grid grid-cols-1 md:grid-cols-3 gap-lg animate-pulse"
                >
                    <div
                        v-for="i in 3"
                        :key="i"
                        class="bg-surface-container-lowest rounded-xl overflow-hidden"
                    >
                        <div class="aspect-video bg-surface-container" />
                        <div class="p-md space-y-sm">
                            <div
                                class="h-3 w-20 bg-surface-container rounded-full"
                            />
                            <div
                                class="h-5 w-full bg-surface-container rounded"
                            />
                            <div
                                class="h-3 w-2/3 bg-surface-container rounded"
                            />
                        </div>
                    </div>
                </div>

                <!-- Cards -->
                <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-lg">
                    <NuxtLink
                        v-for="related in relatedArticles.slice(0, 3)"
                        :key="related.id"
                        :to="`/articles/${related.slug}`"
                        class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow"
                    >
                        <!-- Image -->
                        <div
                            class="aspect-video overflow-hidden bg-surface-container"
                        >
                            <img
                                v-if="related.cover_image"
                                :src="related.cover_image"
                                :alt="related.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                loading="lazy"
                            />
                            <div
                                v-else
                                class="w-full h-full flex items-center justify-center"
                            >
                                <span
                                    class="material-symbols-outlined text-[48px] text-outline"
                                    >image</span
                                >
                            </div>
                        </div>

                        <!-- Contenu card -->
                        <div class="p-md">
                            <span
                                class="block font-label-sm text-label-sm text-primary mb-xs"
                            >
                                Développement
                            </span>
                            <h3
                                class="text-[20px] font-bold leading-snug text-on-surface group-hover:text-primary transition-colors mb-xs line-clamp-2"
                            >
                                {{ related.title }}
                            </h3>
                            <p
                                class="font-label-sm text-label-sm text-on-surface-variant"
                            >
                                {{ related.reading_time ?? 5 }} min
                                <span class="mx-1">•</span>
                                {{
                                    related.published_at
                                        ? formatDate(related.published_at)
                                        : formatDate(related.created_at)
                                }}
                            </p>
                        </div>
                    </NuxtLink>
                </div>
            </div>
        </section>

        <!-- ══════════════════════════════════════════════════════════════════════
         4. FOOTER
         ══════════════════════════════════════════════════════════════════════ -->
        <footer
            class="bg-surface-container-lowest border-t border-outline-variant py-xl"
        >
            <div
                class="max-w-container-max mx-auto px-sm md:px-lg flex flex-col items-center gap-md text-center"
            >
                <!-- Logo -->
                <NuxtLink
                    to="/"
                    class="font-display-lg text-display-lg-mobile text-on-surface"
                >
                    BlogModerne
                </NuxtLink>

                <!-- Liens légaux -->
                <nav class="flex flex-wrap items-center justify-center gap-md">
                    <NuxtLink
                        to="/mentions-legales"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Mentions légales
                    </NuxtLink>
                    <NuxtLink
                        to="/confidentialite"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Confidentialité
                    </NuxtLink>
                    <NuxtLink
                        to="/contact"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Contact
                    </NuxtLink>
                    <NuxtLink
                        to="/articles"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Articles
                    </NuxtLink>
                </nav>

                <!-- Copyright -->
                <p class="font-label-sm text-label-sm text-on-surface-variant">
                    © {{ new Date().getFullYear() }} BlogModerne. Tous droits
                    réservés.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* ── Prose styles — rendu du contenu HTML de l'article ───────────────────── */
.article-prose :deep(h2) {
    font-family: Inter, sans-serif;
    font-size: 30px;
    font-weight: 700;
    line-height: 1.25;
    color: #1b1b23; /* on-surface */
    margin-top: 3rem; /* mt-12 */
    margin-bottom: 1.5rem; /* mb-6 */
}

.article-prose :deep(h3) {
    font-family: Inter, sans-serif;
    font-size: 22px;
    font-weight: 700;
    line-height: 1.3;
    color: #1b1b23;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.article-prose :deep(p) {
    margin-bottom: 1.25rem;
    line-height: 1.75;
}

.article-prose :deep(a) {
    color: #4648d4; /* primary */
    text-decoration: underline;
    text-underline-offset: 3px;
}

.article-prose :deep(a:hover) {
    opacity: 0.8;
}

.article-prose :deep(ul),
.article-prose :deep(ol) {
    padding-left: 1.5rem;
    margin-bottom: 1.25rem;
}

.article-prose :deep(ul) {
    list-style-type: disc;
}

.article-prose :deep(ol) {
    list-style-type: decimal;
}

.article-prose :deep(li) {
    margin-bottom: 0.5rem;
    line-height: 1.7;
}

.article-prose :deep(blockquote) {
    border-left: 4px solid #4648d4; /* border-primary */
    padding-left: 1.5rem; /* pl-6 */
    font-style: italic;
    color: #464554; /* on-surface-variant */
    margin: 1.5rem 0;
    background: #f5f2fe; /* surface-container-low */
    padding-top: 1rem;
    padding-bottom: 1rem;
    border-radius: 0 0.5rem 0.5rem 0;
}

.article-prose :deep(pre) {
    background: #303038; /* inverse-surface */
    color: #f2effb; /* inverse-on-surface */
    padding: 1.5rem; /* p-6 */
    border-radius: 1rem; /* rounded-xl */
    overflow-x: auto;
    margin: 1.5rem 0;
    font-family: "JetBrains Mono", monospace;
    font-size: 14px;
    line-height: 1.5;
}

.article-prose :deep(code) {
    font-family: "JetBrains Mono", monospace;
    font-size: 13px;
    background: #efecf8; /* surface-container */
    color: #4648d4; /* primary */
    padding: 0.1em 0.4em;
    border-radius: 0.25rem;
    white-space: pre-wrap;
}

.article-prose :deep(pre code) {
    background: transparent;
    color: inherit;
    padding: 0;
    font-size: 14px;
}

.article-prose :deep(hr) {
    border-color: #c7c4d7; /* outline-variant */
    margin: 2rem 0;
}

.article-prose :deep(img) {
    border-radius: 0.75rem;
    max-width: 100%;
    height: auto;
    margin: 1.5rem auto;
    display: block;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.article-prose :deep(table) {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
    font-size: 15px;
}

.article-prose :deep(th) {
    background: #efecf8;
    color: #1b1b23;
    font-weight: 600;
    text-align: left;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #c7c4d7;
}

.article-prose :deep(td) {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #c7c4d7;
    color: #464554;
}

.article-prose :deep(tr:last-child td) {
    border-bottom: none;
}
</style>
