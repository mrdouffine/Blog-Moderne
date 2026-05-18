<script setup lang="ts">
// ============================================================
// Page de création d'article — layout full-screen custom
// ============================================================
definePageMeta({ middleware: "auth", layout: false });
useSeoMeta({ title: "Nouvel article — Admin BlogModerne" });

const { createArticle, publishArticle, loading } = useArticles();
const uiStore = useUiStore();
const router = useRouter();

// ── Champs de l'article ─────────────────────────────────────
const title = ref("");
const content = ref("");
const excerpt = ref("");
const category = ref("Technologie");
const status = ref<"draft" | "published">("draft");
const tags = ref<string[]>(["Minimalisme", "UI/UX"]);
const coverImageUrl = ref<string | null>(null);
const coverImageFile = ref<File | null>(null);
const newTag = ref("");
const fileInput = ref<HTMLInputElement | null>(null);
const sidebarVisible = ref(false); // Toggle mobile métadonnées

// ── Autosave ────────────────────────────────────────────────
const lastSaved = ref("14:32");
const autoSaveText = ref(`Brouillon enregistré à ${lastSaved.value}`);
const autoSaveTimer = ref<ReturnType<typeof setTimeout> | null>(null);

// ── Slug auto-généré depuis le titre ────────────────────────
const slug = computed(() =>
    title.value
        .toLowerCase()
        .replace(/[àáâãäå]/g, "a")
        .replace(/[èéêë]/g, "e")
        .replace(/[ìíîï]/g, "i")
        .replace(/[òóôõö]/g, "o")
        .replace(/[ùúûü]/g, "u")
        .replace(/[ç]/g, "c")
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-")
        .trim(),
);

// ── Watch pour autosave ─────────────────────────────────────
watch([title, content], () => {
    autoSaveText.value = "Enregistrement...";
    if (autoSaveTimer.value) clearTimeout(autoSaveTimer.value);
    autoSaveTimer.value = setTimeout(() => {
        const now = new Date();
        lastSaved.value = `${now.getHours().toString().padStart(2, "0")}:${now.getMinutes().toString().padStart(2, "0")}`;
        autoSaveText.value = `Brouillon enregistré à ${lastSaved.value}`;
    }, 2000);
});

// ── Tags ────────────────────────────────────────────────────
const addTag = () => {
    const trimmed = newTag.value.trim();
    if (trimmed && !tags.value.includes(trimmed)) {
        tags.value.push(trimmed);
        newTag.value = "";
    }
};
const removeTag = (tag: string) => {
    tags.value = tags.value.filter((t) => t !== tag);
};

// ── Image de couverture ─────────────────────────────────────
const onFileChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        coverImageFile.value = file;
        coverImageUrl.value = URL.createObjectURL(file);
    }
};

// ── Enregistrer le brouillon ─────────────────────────────────
const saveDraft = async () => {
    if (!title.value.trim()) {
        uiStore.addToast("Le titre est requis.", "error");
        return;
    }
    const fd = new FormData();
    fd.append("title", title.value);
    fd.append("content", content.value);
    fd.append("excerpt", excerpt.value);
    fd.append("status", "draft");
    if (coverImageFile.value) fd.append("cover_image", coverImageFile.value);
    try {
        await createArticle(fd);
        uiStore.addToast("Brouillon enregistré !", "success");
    } catch {
        uiStore.addToast("Erreur lors de la sauvegarde.", "error");
    }
};

// ── Publier ──────────────────────────────────────────────────
const publish = async () => {
    await saveDraft();
    uiStore.addToast("Article publié !", "success");
    router.push("/admin/articles");
};
</script>

<template>
    <!-- Conteneur racine full-screen -->
    <div class="overflow-hidden h-screen flex">
        <!-- ══════════════════════════════════════════════════════
         SIDEBAR GAUCHE
    ══════════════════════════════════════════════════════ -->
        <aside
            class="hidden md:flex flex-col h-full py-md px-xs gap-xs bg-surface-container-low border-r border-outline-variant w-64 shrink-0"
        >
            <!-- Titre + sous-titre -->
            <div class="px-sm pt-xs pb-xs">
                <p
                    class="font-headline-md text-headline-md text-primary leading-tight"
                >
                    Admin Panel
                </p>
                <p
                    class="font-label-sm text-label-sm text-on-surface-variant mt-1"
                >
                    Gestion du contenu
                </p>
            </div>

            <!-- Navigation -->
            <nav class="flex flex-col gap-1 flex-1">
                <!-- Articles (actif) -->
                <NuxtLink
                    to="/admin/articles"
                    class="bg-primary-container text-on-primary-container rounded-xl mx-2 my-1 flex items-center gap-sm px-sm py-xs"
                >
                    <span class="material-symbols-outlined text-[20px]"
                        >description</span
                    >
                    <span class="font-label-sm text-label-sm">Articles</span>
                </NuxtLink>

                <!-- Commentaires -->
                <NuxtLink
                    to="/admin/comments"
                    class="text-on-surface-variant hover:bg-surface-variant/50 rounded-xl mx-2 my-1 flex items-center gap-sm px-sm py-xs transition-all"
                >
                    <span class="material-symbols-outlined text-[20px]"
                        >forum</span
                    >
                    <span class="font-label-sm text-label-sm"
                        >Commentaires</span
                    >
                </NuxtLink>

                <!-- Médias -->
                <NuxtLink
                    to="/admin/media"
                    class="text-on-surface-variant hover:bg-surface-variant/50 rounded-xl mx-2 my-1 flex items-center gap-sm px-sm py-xs transition-all"
                >
                    <span class="material-symbols-outlined text-[20px]"
                        >image</span
                    >
                    <span class="font-label-sm text-label-sm">Médias</span>
                </NuxtLink>

                <!-- Newsletter -->
                <NuxtLink
                    to="/admin/newsletter"
                    class="text-on-surface-variant hover:bg-surface-variant/50 rounded-xl mx-2 my-1 flex items-center gap-sm px-sm py-xs transition-all"
                >
                    <span class="material-symbols-outlined text-[20px]"
                        >mail</span
                    >
                    <span class="font-label-sm text-label-sm">Newsletter</span>
                </NuxtLink>

                <!-- Paramètres -->
                <NuxtLink
                    to="/admin/settings"
                    class="text-on-surface-variant hover:bg-surface-variant/50 rounded-xl mx-2 my-1 flex items-center gap-sm px-sm py-xs transition-all"
                >
                    <span class="material-symbols-outlined text-[20px]"
                        >settings</span
                    >
                    <span class="font-label-sm text-label-sm">Paramètres</span>
                </NuxtLink>
            </nav>

            <!-- Footer sidebar : utilisateur -->
            <div class="mt-auto px-sm flex items-center gap-sm pb-xs">
                <div
                    class="w-10 h-10 rounded-full bg-surface-container-high overflow-hidden shrink-0"
                >
                    <img
                        src="https://ui-avatars.com/api/?name=Admin&background=6063ee&color=fff&size=40"
                        alt="Avatar"
                        class="w-full h-full object-cover"
                    />
                </div>
                <div class="min-w-0">
                    <p
                        class="font-label-sm text-label-sm text-on-surface truncate"
                    >
                        Administrateur
                    </p>
                    <p
                        class="text-[10px] uppercase tracking-wider text-outline truncate"
                    >
                        Super Admin
                    </p>
                </div>
            </div>
        </aside>

        <!-- ══════════════════════════════════════════════════════
         COLONNE DROITE (header + main)
    ══════════════════════════════════════════════════════ -->
        <div class="flex-1 flex flex-col h-full bg-surface overflow-hidden">
            <!-- ── HEADER STICKY ───────────────────────────────── -->
            <header
                class="sticky top-0 w-full z-10 bg-surface/80 backdrop-blur-md px-sm md:px-lg h-16 flex items-center justify-between border-b border-outline-variant/30 shrink-0"
            >
                <!-- Gauche -->
                <div class="flex items-center gap-xs">
                    <!-- Bouton retour -->
                    <NuxtLink
                        to="/admin/articles"
                        class="p-xs hover:bg-surface-variant/50 rounded-full transition-colors"
                        aria-label="Retour aux articles"
                    >
                        <span
                            class="material-symbols-outlined text-on-surface-variant"
                            >arrow_back</span
                        >
                    </NuxtLink>

                    <!-- Séparateur -->
                    <span class="h-4 w-[1px] bg-outline-variant inline-block" />

                    <!-- Texte autosave -->
                    <span
                        class="font-label-sm text-label-sm text-on-surface-variant"
                        >{{ autoSaveText }}</span
                    >
                </div>

                <!-- Droite -->
                <div class="flex items-center gap-xs">
                    <!-- Aperçu -->
                    <button
                        type="button"
                        class="px-sm py-xs font-label-sm text-label-sm text-primary hover:bg-primary/5 rounded-lg transition-colors"
                    >
                        Aperçu
                    </button>

                    <!-- Enregistrer le brouillon -->
                    <button
                        type="button"
                        :disabled="loading"
                        class="px-sm py-xs font-label-sm text-label-sm text-on-surface-variant border border-outline-variant rounded-lg hover:bg-surface-variant/30 transition-colors disabled:opacity-50"
                        @click="saveDraft"
                    >
                        Enregistrer le brouillon
                    </button>

                    <!-- Publier -->
                    <button
                        type="button"
                        :disabled="loading"
                        class="px-sm py-xs font-label-sm text-label-sm bg-primary text-on-primary rounded-lg shadow-sm hover:opacity-90 active:scale-95 transition-all disabled:opacity-50"
                        @click="publish"
                    >
                        Publier
                    </button>
                </div>
            </header>

            <!-- ── MAIN ─────────────────────────────────────────── -->
            <main class="flex flex-1 overflow-hidden">
                <!-- ── Zone éditeur centrale ────────────────────── -->
                <div
                    class="flex-1 overflow-y-auto custom-scrollbar px-sm py-lg"
                >
                    <section class="max-w-content-reading-width mx-auto">
                        <!-- Input titre -->
                        <input
                            v-model="title"
                            class="w-full bg-transparent border-none focus:ring-0 font-display-lg text-display-lg-mobile md:text-display-lg text-on-surface placeholder:text-outline-variant mb-md px-0 outline-none"
                            placeholder="Titre de votre article"
                            type="text"
                        />

                        <!-- Toolbar rich-text -->
                        <div
                            class="sticky top-0 bg-surface py-xs border-b border-outline-variant/20 mb-md flex flex-wrap gap-xs items-center"
                        >
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Gras"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >format_bold</span
                                >
                            </button>
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Italique"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >format_italic</span
                                >
                            </button>
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Liste à puces"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >format_list_bulleted</span
                                >
                            </button>
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Lien"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >link</span
                                >
                            </button>
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Image"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >image</span
                                >
                            </button>

                            <!-- Séparateur -->
                            <span
                                class="h-6 w-[1px] bg-outline-variant/30 mx-xs inline-block"
                            />

                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Code"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >code</span
                                >
                            </button>
                            <button
                                type="button"
                                class="p-xs hover:bg-surface-container-high rounded-md transition-colors"
                                title="Citation"
                            >
                                <span
                                    class="material-symbols-outlined text-[20px] text-on-surface-variant"
                                    >format_quote</span
                                >
                            </button>
                        </div>

                        <!-- Textarea corps -->
                        <textarea
                            v-model="content"
                            class="w-full bg-transparent border-none focus:ring-0 font-body-lg text-body-lg text-on-surface-variant min-h-[614px] resize-none px-0 outline-none"
                            placeholder="Commencez à écrire votre histoire ici..."
                        />
                    </section>
                </div>

                <!-- ── Sidebar métadonnées droite ──────────────── -->
                <aside
                    class="hidden lg:flex w-80 bg-surface-container-lowest border-l border-outline-variant/30 flex-col overflow-y-auto custom-scrollbar p-sm gap-md shrink-0"
                >
                    <!-- Carte 1 — Statut -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            Statut
                        </p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-xs">
                                <span
                                    class="w-2 h-2 rounded-full bg-secondary inline-block"
                                />
                                <span
                                    class="font-body-md text-body-md text-on-surface"
                                    >Brouillon</span
                                >
                            </div>
                            <button
                                type="button"
                                class="text-primary font-label-sm text-label-sm hover:underline transition-all"
                            >
                                Modifier
                            </button>
                        </div>
                    </div>

                    <!-- Carte 2 — Catégorie -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            Catégorie
                        </p>
                        <select
                            v-model="category"
                            class="w-full bg-surface-container border border-outline-variant rounded-lg p-xs font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none"
                        >
                            <option>Technologie</option>
                            <option>Design</option>
                            <option>Culture</option>
                            <option>Lifestyle</option>
                        </select>
                    </div>

                    <!-- Carte 3 — Étiquettes -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            Étiquettes
                        </p>

                        <!-- Tags existants -->
                        <div class="flex flex-wrap gap-xs">
                            <span
                                v-for="tag in tags"
                                :key="tag"
                                class="bg-surface-variant/50 text-on-surface-variant px-xs py-1 rounded-full text-[12px] flex items-center gap-1"
                            >
                                {{ tag }}
                                <button
                                    type="button"
                                    class="leading-none text-on-surface-variant hover:text-on-surface transition-colors"
                                    :aria-label="`Supprimer le tag ${tag}`"
                                    @click="removeTag(tag)"
                                >
                                    <span
                                        class="material-symbols-outlined text-[14px]"
                                        >close</span
                                    >
                                </button>
                            </span>
                        </div>

                        <!-- Input ajout tag -->
                        <input
                            v-model="newTag"
                            type="text"
                            class="w-full bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 p-xs font-body-md text-body-md placeholder:italic placeholder:text-outline-variant outline-none transition-colors"
                            placeholder="Ajouter un tag..."
                            @keydown.enter.prevent="addTag"
                        />
                    </div>

                    <!-- Carte 4 — Image de couverture -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            Image de couverture
                        </p>

                        <div
                            class="relative group cursor-pointer aspect-video bg-surface-container flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-outline-variant hover:border-primary transition-colors overflow-hidden"
                            @click="fileInput?.click()"
                        >
                            <!-- Image si présente -->
                            <img
                                v-if="coverImageUrl"
                                :src="coverImageUrl"
                                alt="Image de couverture"
                                class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-60 transition-opacity"
                            />

                            <!-- Overlay upload -->
                            <div
                                class="relative z-10 flex flex-col items-center text-on-surface"
                            >
                                <span
                                    class="material-symbols-outlined text-display-lg-mobile"
                                    >upload_file</span
                                >
                                <span class="font-label-sm text-label-sm mt-xs">
                                    {{
                                        coverImageUrl
                                            ? "Remplacer l'image"
                                            : "Choisir une image"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Input file caché -->
                        <input
                            ref="fileInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            @change="onFileChange"
                        />
                    </div>

                    <!-- Carte 5 — Extrait -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            Extrait
                        </p>
                        <textarea
                            v-model="excerpt"
                            class="w-full bg-surface-container border border-outline-variant rounded-lg p-xs font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary h-24 resize-none outline-none transition-all"
                            placeholder="Une brève description..."
                        />
                        <p class="text-[10px] text-outline italic">
                            Recommandé : 150-160 caractères.
                        </p>
                    </div>

                    <!-- Carte 6 — SEO Metadata -->
                    <div
                        class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                    >
                        <!-- Header carte SEO -->
                        <div class="flex items-center justify-between">
                            <p
                                class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                            >
                                SEO Metadata
                            </p>
                            <span
                                class="material-symbols-outlined text-[20px] text-outline"
                                >expand_more</span
                            >
                        </div>

                        <!-- Permalien -->
                        <div>
                            <p
                                class="font-label-sm text-label-sm text-on-surface-variant mb-1"
                            >
                                Permalien
                            </p>
                            <p
                                class="bg-surface-container p-2 rounded text-[12px] text-outline break-all"
                            >
                                blogmoderne.fr/articles/{{
                                    slug || "titre-de-votre-article"
                                }}
                            </p>
                        </div>
                    </div>
                </aside>
            </main>
        </div>

        <!-- ══════════════════════════════════════════════════════
         FAB MOBILE (toggle sidebar métadonnées)
    ══════════════════════════════════════════════════════ -->
        <button
            type="button"
            class="lg:hidden fixed bottom-6 right-6 w-14 h-14 bg-primary text-on-primary rounded-full shadow-lg flex items-center justify-center active:scale-95 transition-transform z-20"
            aria-label="Afficher les métadonnées"
            @click="sidebarVisible = !sidebarVisible"
        >
            <span class="material-symbols-outlined">settings</span>
        </button>

        <!-- Sidebar métadonnées mobile (overlay) -->
        <Transition name="slide-right">
            <aside
                v-if="sidebarVisible"
                class="lg:hidden fixed inset-y-0 right-0 w-80 bg-surface-container-lowest border-l border-outline-variant/30 flex flex-col overflow-y-auto custom-scrollbar p-sm gap-md z-30 shadow-xl"
            >
                <!-- Bouton fermer -->
                <div class="flex items-center justify-between mb-xs">
                    <p
                        class="font-headline-md text-headline-md text-on-surface"
                    >
                        Métadonnées
                    </p>
                    <button
                        type="button"
                        class="p-xs hover:bg-surface-variant/50 rounded-full transition-colors"
                        @click="sidebarVisible = false"
                    >
                        <span
                            class="material-symbols-outlined text-on-surface-variant"
                            >close</span
                        >
                    </button>
                </div>

                <!-- Carte 1 — Statut -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <p
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                    >
                        Statut
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-xs">
                            <span
                                class="w-2 h-2 rounded-full bg-secondary inline-block"
                            />
                            <span
                                class="font-body-md text-body-md text-on-surface"
                                >Brouillon</span
                            >
                        </div>
                        <button
                            type="button"
                            class="text-primary font-label-sm text-label-sm hover:underline"
                        >
                            Modifier
                        </button>
                    </div>
                </div>

                <!-- Carte 2 — Catégorie -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <p
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                    >
                        Catégorie
                    </p>
                    <select
                        v-model="category"
                        class="w-full bg-surface-container border border-outline-variant rounded-lg p-xs font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none"
                    >
                        <option>Technologie</option>
                        <option>Design</option>
                        <option>Culture</option>
                        <option>Lifestyle</option>
                    </select>
                </div>

                <!-- Carte 3 — Étiquettes -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <p
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                    >
                        Étiquettes
                    </p>
                    <div class="flex flex-wrap gap-xs">
                        <span
                            v-for="tag in tags"
                            :key="`mob-${tag}`"
                            class="bg-surface-variant/50 text-on-surface-variant px-xs py-1 rounded-full text-[12px] flex items-center gap-1"
                        >
                            {{ tag }}
                            <button type="button" @click="removeTag(tag)">
                                <span
                                    class="material-symbols-outlined text-[14px]"
                                    >close</span
                                >
                            </button>
                        </span>
                    </div>
                    <input
                        v-model="newTag"
                        type="text"
                        class="w-full bg-transparent border-b border-outline-variant focus:border-primary focus:ring-0 p-xs font-body-md text-body-md placeholder:italic placeholder:text-outline-variant outline-none transition-colors"
                        placeholder="Ajouter un tag..."
                        @keydown.enter.prevent="addTag"
                    />
                </div>

                <!-- Carte 4 — Image de couverture -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <p
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                    >
                        Image de couverture
                    </p>
                    <div
                        class="relative group cursor-pointer aspect-video bg-surface-container flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-outline-variant hover:border-primary transition-colors overflow-hidden"
                        @click="fileInput?.click()"
                    >
                        <img
                            v-if="coverImageUrl"
                            :src="coverImageUrl"
                            alt="Image de couverture"
                            class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-60 transition-opacity"
                        />
                        <div
                            class="relative z-10 flex flex-col items-center text-on-surface"
                        >
                            <span
                                class="material-symbols-outlined text-display-lg-mobile"
                                >upload_file</span
                            >
                            <span class="font-label-sm text-label-sm mt-xs">
                                {{
                                    coverImageUrl
                                        ? "Remplacer l'image"
                                        : "Choisir une image"
                                }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Carte 5 — Extrait -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <p
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                    >
                        Extrait
                    </p>
                    <textarea
                        v-model="excerpt"
                        class="w-full bg-surface-container border border-outline-variant rounded-lg p-xs font-body-md text-body-md focus:ring-2 focus:ring-primary focus:border-primary h-24 resize-none outline-none transition-all"
                        placeholder="Une brève description..."
                    />
                    <p class="text-[10px] text-outline italic">
                        Recommandé : 150-160 caractères.
                    </p>
                </div>

                <!-- Carte 6 — SEO Metadata -->
                <div
                    class="bg-white p-sm rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] flex flex-col gap-sm"
                >
                    <div class="flex items-center justify-between">
                        <p
                            class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest opacity-60"
                        >
                            SEO Metadata
                        </p>
                        <span
                            class="material-symbols-outlined text-[20px] text-outline"
                            >expand_more</span
                        >
                    </div>
                    <div>
                        <p
                            class="font-label-sm text-label-sm text-on-surface-variant mb-1"
                        >
                            Permalien
                        </p>
                        <p
                            class="bg-surface-container p-2 rounded text-[12px] text-outline break-all"
                        >
                            blogmoderne.fr/articles/{{
                                slug || "titre-de-votre-article"
                            }}
                        </p>
                    </div>
                </div>
            </aside>
        </Transition>

        <!-- Backdrop mobile -->
        <Transition name="fade">
            <div
                v-if="sidebarVisible"
                class="lg:hidden fixed inset-0 bg-inverse-surface/30 z-20"
                @click="sidebarVisible = false"
            />
        </Transition>
    </div>
</template>

<style scoped>
/* ── Scrollbar personnalisée ───────────────────────────────── */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c7c4d7;
    border-radius: 10px;
}

/* ── Transition sidebar mobile ─────────────────────────────── */
.slide-right-enter-active,
.slide-right-leave-active {
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(100%);
}

/* ── Transition backdrop ───────────────────────────────────── */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* ── Désactiver le ring Tailwind natif sur les inputs ───────── */
textarea,
input,
select {
    --tw-ring-shadow: none;
}
</style>
