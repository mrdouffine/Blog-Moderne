<script setup lang="ts">
// =============================================================================
// blog/frontend/pages/admin/articles/index.vue
// Liste admin des articles — tableau complet avec filtres, stats et pagination
// =============================================================================
import type { Article } from "~/types";

definePageMeta({ middleware: "auth", layout: "admin" });
useSeoMeta({ title: "Articles — Admin BlogModerne" });

const {
    articles,
    loading,
    pagination,
    fetchArticles,
    deleteArticle,
    publishArticle,
    draftArticle,
} = useArticles();
const uiStore = useUiStore();

// ---------------------------------------------------------------------------
// State
// ---------------------------------------------------------------------------
const search = ref("");
const statusFilter = ref<"all" | "published" | "draft">("all");
const page = ref(1);
const perPage = ref(10);
const selectedRows = ref<number[]>([]);

// ---------------------------------------------------------------------------
// Stats calculées
// ---------------------------------------------------------------------------
const totalArticles = computed(
    () => pagination.value?.total ?? articles.value.length,
);
const publishedCount = computed(
    () => articles.value.filter((a) => a.status === "published").length,
);
const draftCount = computed(
    () => articles.value.filter((a) => a.status === "draft").length,
);

// ---------------------------------------------------------------------------
// Chargement
// ---------------------------------------------------------------------------
const load = async () => {
    await fetchArticles({
        search: search.value || undefined,
        status: statusFilter.value === "all" ? undefined : statusFilter.value,
        page: page.value,
        per_page: perPage.value,
    });
};

let searchTimer: ReturnType<typeof setTimeout>;
const onSearchInput = () => {
    clearTimeout(searchTimer);
    page.value = 1;
    searchTimer = setTimeout(load, 400);
};

await load();

// ---------------------------------------------------------------------------
// Sélection de lignes
// ---------------------------------------------------------------------------
const toggleRow = (id: number) => {
    const idx = selectedRows.value.indexOf(id);
    if (idx === -1) selectedRows.value.push(id);
    else selectedRows.value.splice(idx, 1);
};

// ---------------------------------------------------------------------------
// Suppression
// ---------------------------------------------------------------------------
const deleteModal = ref(false);
const toDelete = ref<Article | null>(null);
const deleteLoading = ref(false);

const confirmDelete = (article: Article) => {
    toDelete.value = article;
    deleteModal.value = true;
};

const onDelete = async () => {
    if (!toDelete.value) return;
    deleteLoading.value = true;
    try {
        await deleteArticle(toDelete.value.id);
        uiStore.success("Article supprimé.");
        deleteModal.value = false;
        toDelete.value = null;
        await load();
    } catch {
        uiStore.error("Erreur suppression.");
    } finally {
        deleteLoading.value = false;
    }
};

// ---------------------------------------------------------------------------
// Toggle statut
// ---------------------------------------------------------------------------
const onToggleStatus = async (article: Article) => {
    try {
        if (article.status === "published") await draftArticle(article.id);
        else await publishArticle(article.id);
        uiStore.success("Statut mis à jour.");
        await load();
    } catch {
        uiStore.error("Erreur.");
    }
};

// ---------------------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------------------
const pages = computed(() => {
    const last = pagination.value?.last_page ?? 1;
    if (last <= 7)
        return Array.from({ length: last }, (_, i) => i + 1) as (
            | number
            | "..."
        )[];
    const result: (number | "...")[] = [1];
    if (page.value > 3) result.push("...");
    for (
        let i = Math.max(2, page.value - 1);
        i <= Math.min(last - 1, page.value + 1);
        i++
    )
        result.push(i);
    if (page.value < last - 2) result.push("...");
    result.push(last);
    return result;
});

const onPageChange = (p: number) => {
    const last = pagination.value?.last_page ?? 1;
    if (p < 1 || p > last) return;
    page.value = p;
    load();
};

// ---------------------------------------------------------------------------
// Utilitaires
// ---------------------------------------------------------------------------
const formattedDate = (d: string) =>
    new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });

const fromEntry = computed(() => pagination.value?.from ?? 1);
const toEntry = computed(() => pagination.value?.to ?? articles.value.length);
const totalEntries = computed(
    () => pagination.value?.total ?? articles.value.length,
);
</script>

<template>
    <!-- ── Wrapper global ────────────────────────────────────────────────────── -->
    <div class="min-h-screen bg-surface flex flex-col">
        <!-- ── Header sticky ──────────────────────────────────────────────────── -->
        <header
            class="sticky top-0 z-40 bg-surface/80 backdrop-blur-md px-sm md:px-lg py-sm flex justify-between items-center shadow-sm"
        >
            <div class="flex items-center gap-md">
                <h2 class="font-headline-md text-headline-md text-on-surface">
                    Mes Articles
                </h2>

                <!-- Barre de recherche desktop -->
                <div
                    class="hidden lg:flex items-center bg-surface-container-low rounded-full px-sm py-xs border border-outline-variant/30 focus-within:border-primary transition-all"
                >
                    <span
                        class="material-symbols-outlined text-on-surface-variant"
                        >search</span
                    >
                    <input
                        v-model="search"
                        @input="onSearchInput"
                        class="bg-transparent border-none focus:ring-0 font-body-md text-body-md w-64 px-xs outline-none"
                        placeholder="Rechercher un article..."
                        type="text"
                    />
                </div>
            </div>

            <div class="flex items-center gap-sm">
                <button
                    class="material-symbols-outlined p-xs text-on-surface-variant hover:bg-surface-variant/50 rounded-full transition-colors"
                >
                    notifications
                </button>

                <!-- Bouton "Nouvel article" desktop -->
                <div class="hidden md:block">
                    <NuxtLink to="/admin/articles/create">
                        <button
                            class="bg-secondary-container text-on-secondary-container font-label-sm text-label-sm px-md py-xs rounded-full shadow-sm hover:brightness-110 transition-all cursor-pointer"
                        >
                            Nouvel article
                        </button>
                    </NuxtLink>
                </div>
            </div>
        </header>

        <!-- ── Contenu principal ───────────────────────────────────────────────── -->
        <main class="flex-1 px-sm md:px-lg py-md space-y-md">
            <!-- ── Stats Grid ──────────────────────────────────────────────────── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-md mb-lg">
                <!-- Carte 1 — Total Articles -->
                <div
                    class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-xs">
                        <span
                            class="material-symbols-outlined p-xs bg-primary-container/10 text-primary rounded-lg text-[20px]"
                        >
                            article
                        </span>
                        <span class="text-[12px] text-primary font-bold"
                            >+12%</span
                        >
                    </div>
                    <p
                        class="text-on-surface-variant font-label-sm text-label-sm mt-base"
                    >
                        Total Articles
                    </p>
                    <p
                        class="font-headline-md text-headline-md text-on-surface mt-base"
                    >
                        {{ totalArticles }}
                    </p>
                </div>

                <!-- Carte 2 — Publiés -->
                <div
                    class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-xs">
                        <span
                            class="material-symbols-outlined p-xs bg-green-100 text-green-700 rounded-lg text-[20px]"
                        >
                            check_circle
                        </span>
                        <span class="text-[12px] text-green-700 font-bold"
                            >+5%</span
                        >
                    </div>
                    <p
                        class="text-on-surface-variant font-label-sm text-label-sm mt-base"
                    >
                        Publiés
                    </p>
                    <p
                        class="font-headline-md text-headline-md text-on-surface mt-base"
                    >
                        {{ publishedCount }}
                    </p>
                </div>

                <!-- Carte 3 — Brouillons -->
                <div
                    class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-xs">
                        <span
                            class="material-symbols-outlined p-xs bg-amber-100 text-amber-700 rounded-lg text-[20px]"
                        >
                            edit_note
                        </span>
                        <span class="text-[12px] text-amber-700 font-bold"
                            >-2%</span
                        >
                    </div>
                    <p
                        class="text-on-surface-variant font-label-sm text-label-sm mt-base"
                    >
                        Brouillons
                    </p>
                    <p
                        class="font-headline-md text-headline-md text-on-surface mt-base"
                    >
                        {{ draftCount }}
                    </p>
                </div>

                <!-- Carte 4 — Vues Totales -->
                <div
                    class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/20 hover:shadow-md transition-shadow"
                >
                    <div class="flex items-center justify-between mb-xs">
                        <span
                            class="material-symbols-outlined p-xs bg-secondary-container/20 text-on-secondary-container rounded-lg text-[20px]"
                        >
                            visibility
                        </span>
                        <span
                            class="text-[12px] text-on-secondary-container font-bold"
                            >+24%</span
                        >
                    </div>
                    <p
                        class="text-on-surface-variant font-label-sm text-label-sm mt-base"
                    >
                        Vues Totales
                    </p>
                    <p
                        class="font-headline-md text-headline-md text-on-surface mt-base"
                    >
                        48.2k
                    </p>
                </div>
            </div>

            <!-- ── Section tableau ─────────────────────────────────────────────── -->
            <div
                class="bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/20 overflow-hidden"
            >
                <!-- Toolbar tableau -->
                <div
                    class="p-md flex justify-between items-center border-b border-outline-variant/20"
                >
                    <div class="flex items-center gap-xs">
                        <!-- Filtrer -->
                        <button
                            class="bg-surface-container-high px-sm py-xs rounded-lg text-label-sm font-label-sm text-on-surface hover:bg-surface-variant transition-colors flex items-center gap-xs"
                        >
                            <span class="material-symbols-outlined text-[16px]"
                                >filter_list</span
                            >
                            Filtrer
                        </button>
                        <!-- Trier par -->
                        <button
                            class="bg-surface-container-high px-sm py-xs rounded-lg text-label-sm font-label-sm text-on-surface hover:bg-surface-variant transition-colors flex items-center gap-xs"
                        >
                            <span class="material-symbols-outlined text-[16px]"
                                >sort</span
                            >
                            Trier par
                        </button>
                    </div>

                    <div class="flex items-center gap-xs">
                        <span
                            class="material-symbols-outlined cursor-pointer text-on-surface-variant hover:text-primary transition-colors"
                            >view_list</span
                        >
                        <span
                            class="material-symbols-outlined cursor-pointer text-on-surface-variant hover:text-primary transition-colors"
                            >grid_view</span
                        >
                    </div>
                </div>

                <!-- État de chargement -->
                <div v-if="loading" class="p-md space-y-sm animate-pulse">
                    <div
                        v-for="i in perPage"
                        :key="i"
                        class="flex items-center gap-md"
                    >
                        <div
                            class="h-12 w-12 bg-surface-container rounded-lg flex-shrink-0"
                        />
                        <div class="flex-1 space-y-xs">
                            <div
                                class="h-4 bg-surface-container rounded w-3/4"
                            />
                            <div
                                class="h-3 bg-surface-container rounded w-1/2"
                            />
                        </div>
                        <div
                            class="h-6 bg-surface-container rounded-full w-20 hidden md:block"
                        />
                        <div
                            class="h-4 bg-surface-container rounded w-24 hidden lg:block"
                        />
                        <div
                            class="h-4 bg-surface-container rounded w-16 hidden lg:block"
                        />
                        <div
                            class="h-4 bg-surface-container rounded w-16 hidden xl:block"
                        />
                    </div>
                </div>

                <!-- Tableau -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <!-- Thead -->
                        <thead
                            class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider"
                        >
                            <tr>
                                <th class="px-md py-sm w-10">
                                    <span class="sr-only">Sélection</span>
                                </th>
                                <th class="px-md py-sm">Article</th>
                                <th class="px-md py-sm hidden md:table-cell">
                                    Statut
                                </th>
                                <th class="px-md py-sm hidden lg:table-cell">
                                    Auteur
                                </th>
                                <th class="px-md py-sm hidden lg:table-cell">
                                    Date
                                </th>
                                <th class="px-md py-sm hidden xl:table-cell">
                                    Vues
                                </th>
                                <th class="px-md py-sm text-right">Actions</th>
                            </tr>
                        </thead>

                        <!-- Tbody -->
                        <tbody
                            class="divide-y divide-outline-variant/20 font-body-md text-body-md"
                        >
                            <!-- Aucun résultat -->
                            <tr v-if="articles.length === 0">
                                <td
                                    colspan="7"
                                    class="px-md py-lg text-center text-on-surface-variant"
                                >
                                    <div
                                        class="flex flex-col items-center gap-sm"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[48px] opacity-30"
                                            >article</span
                                        >
                                        <p class="font-label-sm text-label-sm">
                                            Aucun article trouvé.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <!-- Lignes articles -->
                            <tr
                                v-for="article in articles"
                                :key="article.id"
                                class="hover:bg-surface-container-low transition-colors group cursor-pointer"
                                @click="toggleRow(article.id)"
                            >
                                <!-- Checkbox -->
                                <td class="px-md py-sm" @click.stop>
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedRows.includes(article.id)
                                        "
                                        @change="toggleRow(article.id)"
                                        class="w-4 h-4 accent-primary cursor-pointer rounded"
                                    />
                                </td>

                                <!-- Article (miniature + titre + sous-titre) -->
                                <td class="px-md py-sm">
                                    <div class="flex items-center gap-md">
                                        <!-- Miniature -->
                                        <img
                                            v-if="article.cover_image"
                                            :src="article.cover_image"
                                            :alt="article.title"
                                            class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
                                        />
                                        <div
                                            v-else
                                            class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0"
                                        >
                                            <span
                                                class="material-symbols-outlined text-on-surface-variant text-[20px]"
                                                >image</span
                                            >
                                        </div>
                                        <!-- Texte -->
                                        <div class="min-w-0">
                                            <p
                                                class="font-bold text-on-surface group-hover:text-primary transition-colors truncate max-w-[240px]"
                                            >
                                                {{ article.title }}
                                            </p>
                                            <p
                                                class="text-[12px] text-on-surface-variant mt-base truncate max-w-[240px]"
                                            >
                                                {{ article.author?.name }} ·
                                                {{ article.reading_time }} min
                                                de lecture
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Statut -->
                                <td class="px-md py-sm hidden md:table-cell">
                                    <span
                                        v-if="article.status === 'published'"
                                        class="px-sm py-base bg-green-100 text-green-700 text-[12px] font-bold rounded-full inline-block"
                                    >
                                        Publié
                                    </span>
                                    <span
                                        v-else
                                        class="px-sm py-base bg-amber-100 text-amber-700 text-[12px] font-bold rounded-full inline-block"
                                    >
                                        Brouillon
                                    </span>
                                </td>

                                <!-- Auteur -->
                                <td
                                    class="px-md py-sm hidden lg:table-cell text-on-surface-variant"
                                >
                                    {{ article.author?.name }}
                                </td>

                                <!-- Date -->
                                <td
                                    class="px-md py-sm hidden lg:table-cell text-on-surface-variant"
                                >
                                    {{ formattedDate(article.created_at) }}
                                </td>

                                <!-- Vues -->
                                <td
                                    class="px-md py-sm hidden xl:table-cell font-bold text-on-surface"
                                >
                                    {{
                                        article.views_count.toLocaleString(
                                            "fr-FR",
                                        )
                                    }}
                                </td>

                                <!-- Actions -->
                                <td class="px-md py-sm" @click.stop>
                                    <div class="flex justify-end gap-xs">
                                        <!-- Éditer -->
                                        <NuxtLink
                                            :to="`/admin/articles/${article.id}/edit`"
                                            @click.stop
                                        >
                                            <button
                                                class="p-xs hover:bg-primary/10 hover:text-primary rounded-full transition-all material-symbols-outlined text-[20px] text-on-surface-variant"
                                                title="Éditer"
                                            >
                                                edit
                                            </button>
                                        </NuxtLink>

                                        <!-- Supprimer -->
                                        <button
                                            @click="confirmDelete(article)"
                                            class="p-xs hover:bg-error/10 hover:text-error rounded-full transition-all material-symbols-outlined text-[20px] text-on-surface-variant"
                                            title="Supprimer"
                                        >
                                            delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="!loading && pagination && pagination.last_page >= 1"
                    class="p-md flex flex-col md:flex-row justify-between items-center gap-md bg-surface-container-low border-t border-outline-variant/20"
                >
                    <!-- Texte résumé -->
                    <p
                        class="font-label-sm text-label-sm text-on-surface-variant"
                    >
                        Affichage de {{ fromEntry }} à {{ toEntry }} sur
                        {{ totalEntries }} articles
                    </p>

                    <!-- Contrôles de pagination -->
                    <div class="flex items-center gap-xs">
                        <!-- Précédent -->
                        <button
                            @click="onPageChange(page - 1)"
                            :disabled="page <= 1"
                            class="p-xs hover:bg-surface-variant rounded-lg transition-all material-symbols-outlined text-on-surface-variant disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            chevron_left
                        </button>

                        <!-- Pages numérotées -->
                        <template v-for="(p, idx) in pages" :key="idx">
                            <!-- Ellipsis -->
                            <span
                                v-if="p === '...'"
                                class="w-8 h-8 flex items-center justify-center text-label-sm text-on-surface-variant select-none"
                            >
                                …
                            </span>
                            <!-- Page number -->
                            <button
                                v-else
                                @click="onPageChange(p as number)"
                                :class="[
                                    'w-8 h-8 flex items-center justify-center rounded-lg text-label-sm transition-colors',
                                    p === page
                                        ? 'bg-primary text-on-primary font-bold'
                                        : 'hover:bg-surface-variant text-on-surface',
                                ]"
                            >
                                {{ p }}
                            </button>
                        </template>

                        <!-- Suivant -->
                        <button
                            @click="onPageChange(page + 1)"
                            :disabled="page >= (pagination?.last_page ?? 1)"
                            class="p-xs hover:bg-surface-variant rounded-lg transition-all material-symbols-outlined text-on-surface-variant disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            chevron_right
                        </button>
                    </div>
                </div>
            </div>
            <!-- /Section tableau -->
        </main>

        <!-- ── Footer ─────────────────────────────────────────────────────────── -->
        <footer
            class="w-full py-md px-sm md:px-lg border-t border-outline-variant/20 flex flex-col md:flex-row justify-between items-center gap-sm bg-surface-container-lowest"
        >
            <p class="text-on-surface-variant font-label-sm text-label-sm">
                © 2024 BlogModerne. Tous droits réservés.
            </p>
            <div
                class="flex gap-md font-label-sm text-label-sm text-on-surface-variant"
            >
                <a class="hover:text-primary transition-colors" href="#"
                    >Conditions d'utilisation</a
                >
                <a class="hover:text-primary transition-colors" href="#"
                    >Confidentialité</a
                >
                <a class="hover:text-primary transition-colors" href="#"
                    >Aide</a
                >
            </div>
        </footer>
    </div>
    <!-- /Wrapper global -->

    <!-- ── FAB Mobile ─────────────────────────────────────────────────────── -->
    <div class="md:hidden fixed bottom-6 right-6 z-50">
        <NuxtLink to="/admin/articles/create">
            <button
                class="bg-primary text-on-primary w-14 h-14 rounded-full shadow-lg flex items-center justify-center active:scale-95 transition-all cursor-pointer"
            >
                <span class="material-symbols-outlined">add</span>
            </button>
        </NuxtLink>
    </div>

    <!-- ── Modal de confirmation de suppression ───────────────────────────── -->
    <UiModal v-model="deleteModal" title="Supprimer l'article" size="sm">
        <div class="space-y-sm">
            <div class="flex items-start gap-sm">
                <div class="p-xs bg-error/10 rounded-full flex-shrink-0">
                    <span
                        class="material-symbols-outlined text-error text-[24px]"
                        >warning</span
                    >
                </div>
                <div>
                    <p
                        class="font-label-sm text-label-sm text-on-surface mb-xs"
                    >
                        Cette action est irréversible.
                    </p>
                    <p
                        class="text-[14px] text-on-surface-variant leading-relaxed"
                    >
                        Êtes-vous sûr de vouloir supprimer l'article
                        <strong class="text-on-surface"
                            >« {{ toDelete?.title }} »</strong
                        >
                        ? Toutes les données associées seront définitivement
                        perdues.
                    </p>
                </div>
            </div>
        </div>

        <template #footer>
            <button
                @click="deleteModal = false"
                class="px-md py-xs rounded-full font-label-sm text-label-sm bg-surface-container-high text-on-surface hover:bg-surface-variant transition-colors"
            >
                Annuler
            </button>
            <button
                @click="onDelete"
                :disabled="deleteLoading"
                class="px-md py-xs rounded-full font-label-sm text-label-sm bg-error text-on-error hover:brightness-110 transition-all disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-xs"
            >
                <span
                    v-if="deleteLoading"
                    class="material-symbols-outlined text-[16px] animate-spin"
                    >progress_activity</span
                >
                <span>Supprimer</span>
            </button>
        </template>
    </UiModal>
</template>
