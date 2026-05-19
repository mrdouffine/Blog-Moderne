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
const activeSection = ref("");

/**
 * Assainit le contenu HTML brut pour empêcher les attaques XSS.
 * Élimine les scripts, les gestionnaires d'événements et les URLs javascript:.
 */
const sanitizeHtml = (html: string): string => {
    if (!html) return "";
    return html
        .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, "")
        .replace(/\bon\w+\s*=\s*(['"])(.*?)\1/gi, "")
        .replace(/\bon\w+\s*=\s*([^>\s]+)/gi, "")
        .replace(/\bhref\s*=\s*(['"])javascript:(.*?)\1/gi, 'href="#"');
};

const parsedContent = computed(() => {
    if (!article.value || !article.value.content) return "";
    
    let content = sanitizeHtml(article.value.content);
    const headingRegex = /<h([23])([^>]*)>(.*?)<\/h\1>/gi;
    
    let index = 0;
    return content.replace(headingRegex, (match, level, attrs, text) => {
        if (attrs.includes("id=")) return match;
        
        const label = text.replace(/<\/?[^>]+(>|$)/g, "").trim();
        const id = label
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)/g, "");
        
        return `<h${level}${attrs} id="${id || `heading-${index++}`}">${text}</h${level}>`;
    });
});

const tocLinks = computed(() => {
    if (!article.value || !article.value.content) return [];
    
    const links: { id: string; label: string }[] = [];
    const content = article.value.content;
    const headingRegex = /<h([23])([^>]*)>(.*?)<\/h\1>/gi;
    
    let index = 0;
    let match;
    while ((match = headingRegex.exec(content)) !== null) {
        const text = match[3];
        const label = text.replace(/<\/?[^>]+(>|$)/g, "").trim();
        const id = label
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/(^-|-$)/g, "");
            
        links.push({
            id: id || `heading-${index++}`,
            label: label
        });
    }
    
    return links;
});

const scrollTo = (id: string) => {
    if (import.meta.client) {
        const el = document.getElementById(id);
        if (el) {
            el.scrollIntoView({ behavior: "smooth" });
        }
    }
};

const isBookmarked = ref(false);
const toastMessage = ref("");
const showToast = ref(false);

const triggerToast = (msg: string) => {
    toastMessage.value = msg;
    showToast.value = true;
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

const toggleBookmark = () => {
    if (!import.meta.client) return;
    const bookmarks = JSON.parse(localStorage.getItem("bookmarks") || "[]");
    if (bookmarks.includes(slug)) {
        const index = bookmarks.indexOf(slug);
        bookmarks.splice(index, 1);
        isBookmarked.value = false;
        triggerToast("Article retiré de vos favoris !");
    } else {
        bookmarks.push(slug);
        isBookmarked.value = true;
        triggerToast("Article ajouté à vos favoris !");
    }
    localStorage.setItem("bookmarks", JSON.stringify(bookmarks));
};

const showShareMenu = ref(false);

const toggleShareMenu = () => {
    showShareMenu.value = !showShareMenu.value;
};

const shareWhatsApp = () => {
    if (!import.meta.client) return;
    const url = `https://api.whatsapp.com/send?text=${encodeURIComponent((article.value?.title ?? '') + ' ' + window.location.href)}`;
    window.open(url, '_blank');
    showShareMenu.value = false;
    triggerToast("Lien envoyé vers WhatsApp !");
};

const shareEmail = () => {
    if (!import.meta.client) return;
    const subject = encodeURIComponent(article.value?.title ?? 'Article BlogModerne');
    const body = encodeURIComponent(`Découvrez cet article sur BlogModerne : ${window.location.href}`);
    const url = `mailto:?subject=${subject}&body=${body}`;
    window.open(url, '_self');
    showShareMenu.value = false;
    triggerToast("Ouverture de votre client e-mail !");
};

const copyLink = () => {
    if (!import.meta.client) return;
    navigator.clipboard.writeText(window.location.href).then(() => {
        triggerToast("Lien de l'article copié dans le presse-papiers !");
    }).catch(() => {
        triggerToast("Impossible de copier le lien.");
    });
    showShareMenu.value = false;
};

let observer: IntersectionObserver | null = null;

const observeHeaders = () => {
    if (typeof IntersectionObserver === "undefined" || !import.meta.client) return;
    
    if (observer) {
        observer.disconnect();
    }
    
    observer = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    activeSection.value = entry.target.id;
                }
            }
        },
        { rootMargin: "-20% 0px -70% 0px" },
    );
    
    tocLinks.value.forEach(({ id }) => {
        const el = document.getElementById(id);
        if (el) observer!.observe(el);
    });
};

onMounted(() => {
    observeHeaders();
    if (import.meta.client) {
        const bookmarks = JSON.parse(localStorage.getItem("bookmarks") || "[]");
        isBookmarked.value = bookmarks.includes(slug);
    }
});

watch(article, () => {
    nextTick(() => {
        observeHeaders();
    });
});

onUnmounted(() => {
    if (observer) observer.disconnect();
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

const startReply = (comment: any) => {
    if (!authStore.isAuthenticated) {
        triggerToast("Veuillez vous connecter pour répondre à un commentaire.");
        navigateTo("/auth/login");
        return;
    }
    
    // Remplir le champ de commentaire avec la mention de l'auteur
    commentText.value = `@${comment.author?.name || 'Membre'} : `;
    
    // Défiler vers le champ de saisie du commentaire
    const element = document.getElementById("comment-input");
    if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "center" });
        setTimeout(() => {
            element.focus();
        }, 300);
    }
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
                <span class="text-primary font-bold">{{ article?.category || 'Général' }}</span>
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
                                    scrollTo(link.id);
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
                    <div class="flex items-center gap-sm relative">
                        <!-- Dropdown wrapper -->
                        <div class="relative">
                            <button
                                class="w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors flex items-center justify-center active:scale-95 transition-all duration-200"
                                aria-label="Partager"
                                @click="toggleShareMenu"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >share</span
                                >
                            </button>

                            <!-- Premium Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-150 ease-out transform"
                                enter-from-class="scale-95 opacity-0 -translate-y-2"
                                enter-to-class="scale-100 opacity-100 translate-y-0"
                                leave-active-class="transition duration-100 ease-in transform"
                                leave-from-class="scale-100 opacity-100 translate-y-0"
                                leave-to-class="scale-95 opacity-0 -translate-y-2"
                            >
                                <div
                                    v-if="showShareMenu"
                                    class="absolute left-0 mt-xs z-30 bg-surface-container-lowest border border-outline-variant/50 rounded-xl p-xs shadow-xl w-48 flex flex-col gap-3xs backdrop-blur-md"
                                >
                                    <button
                                        @click="shareWhatsApp"
                                        class="flex items-center gap-xs px-sm py-2 hover:bg-surface-container-high rounded-lg text-left text-body-sm transition-colors text-on-surface w-full"
                                    >
                                        <svg class="w-4 h-4 text-emerald-600 fill-current shrink-0" viewBox="0 0 24 24">
                                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.965C16.588 1.977 14.12 .953 11.5 1.033c-5.439 0-9.863 4.37-9.867 9.8-.001 1.774.475 3.505 1.378 5.081L2.015 21.5l5.807-1.52c-1.563 1.157-1.042 1.107-.175.174z"/>
                                        </svg>
                                        WhatsApp
                                    </button>
                                    <button
                                        @click="shareEmail"
                                        class="flex items-center gap-xs px-sm py-2 hover:bg-surface-container-high rounded-lg text-left text-body-sm transition-colors text-on-surface w-full"
                                    >
                                        <span class="material-symbols-outlined text-[16px] text-primary shrink-0">mail</span>
                                        Email
                                    </button>
                                    <button
                                        @click="copyLink"
                                        class="flex items-center gap-xs px-sm py-2 hover:bg-surface-container-high rounded-lg text-left text-body-sm transition-colors text-on-surface w-full"
                                    >
                                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant shrink-0">content_copy</span>
                                        Copier le lien
                                    </button>
                                </div>
                            </Transition>
                        </div>
                        <button
                            class="w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high transition-colors flex items-center justify-center transition-all duration-300"
                            :class="{ 'scale-110 bg-primary-container': isBookmarked }"
                            :aria-label="isBookmarked ? 'Retirer des favoris' : 'Sauvegarder en favoris'"
                            @click="toggleBookmark"
                        >
                            <span
                                class="material-symbols-outlined text-[20px] transition-colors duration-300"
                                :class="isBookmarked ? 'text-primary' : 'text-on-surface-variant'"
                                :style="isBookmarked ? 'font-variation-settings: \'FILL\' 1' : ''"
                            >
                                bookmark
                            </span>
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
                                    {{ article?.category || 'Général' }}
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
                                alt=""
                                class="w-full h-auto rounded-xl shadow-md mb-xl object-cover bg-surface-container-highest"
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
                                v-html="parsedContent"
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
                                    v-if="authStore.isAuthenticated"
                                    class="bg-surface-container-low p-md rounded-xl mb-xl border border-outline-variant/20"
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

                                <!-- Fallback non connecté -->
                                <div
                                    v-else
                                    class="bg-surface-container-low p-lg rounded-xl mb-xl border border-outline-variant/30 text-center flex flex-col items-center gap-md"
                                >
                                    <span class="material-symbols-outlined text-[40px] text-primary/80">
                                        chat_bubble
                                    </span>
                                    <div class="flex flex-col gap-xs">
                                        <p class="font-headline-sm text-headline-sm text-on-surface">
                                            Rejoignez la discussion
                                        </p>
                                        <p class="font-body-md text-body-md text-on-surface-variant max-w-[400px]">
                                            Vous devez être connecté pour publier un commentaire et échanger avec la communauté.
                                        </p>
                                    </div>
                                    <NuxtLink
                                        to="/auth/login"
                                        class="bg-primary text-on-primary px-lg py-2.5 rounded-full font-label-sm text-label-sm hover:opacity-90 transition-all shadow-sm hover:shadow"
                                    >
                                        Se connecter
                                    </NuxtLink>
                                </div>

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
                                                    @click="startReply(comment)"
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
                                alt=""
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 bg-surface-container-highest"
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
                                {{ related.category || 'Général' }}
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



        <!-- Floating premium toast -->
        <Transition
            enter-active-class="transition duration-300 ease-out transform"
            enter-from-class="translate-y-10 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in transform"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-10 opacity-0"
        >
            <div
                v-if="showToast"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-neutral-900/90 text-neutral-50 px-md py-sm rounded-xl shadow-2xl flex items-center gap-sm font-label-md text-label-md backdrop-blur-md border border-neutral-800"
            >
                <span class="material-symbols-outlined text-[20px] text-primary">info</span>
                {{ toastMessage }}
            </div>
        </Transition>
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
