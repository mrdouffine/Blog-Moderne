<script setup lang="ts">
// ============================================================
// Admin Newsletter — Gestion Newsletter (maquette pixel-perfect)
// ============================================================
definePageMeta({ middleware: "auth", layout: "admin" });

useSeoMeta({ title: "Newsletter — Admin BlogModerne" });

import type { Subscriber } from "~/types";

const { subscribers, loading, pagination, fetchSubscribers } = useNewsletter();

// ── Recherche & abonnés filtrés ──────────────────────────────
const searchQuery = ref("");

const filteredSubscribers = computed(() => {
    if (!searchQuery.value) return subscribers.value;
    return subscribers.value.filter((s: Subscriber) =>
        s.email.toLowerCase().includes(searchQuery.value.toLowerCase()),
    );
});

// ── Formulaire d'envoi ───────────────────────────────────────
const subject = ref("");
const recipient = ref("all");
const content = ref("");

// ── Toast brouillon ──────────────────────────────────────────
const toastVisible = ref(false);

const showDraftToast = () => {
    toastVisible.value = true;
    setTimeout(() => {
        toastVisible.value = false;
    }, 3000);
};

// ── Hover row (translateX) ───────────────────────────────────
const hoveredRow = ref<number | null>(null);

// ── Formatage ────────────────────────────────────────────────
const formattedDate = (d: string) =>
    new Date(d).toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });

const formatTotal = (n: number | undefined) =>
    n !== undefined ? n.toLocaleString("fr-FR") : "—";

// ── Chargement initial ───────────────────────────────────────
await fetchSubscribers({ per_page: 10 });
</script>

<template>
    <!-- ══════════════════════════════════════════════════════════
       HEADER STICKY
  ══════════════════════════════════════════════════════════ -->
    <header
        class="sticky top-0 bg-surface/80 backdrop-blur-md z-40 border-b border-outline-variant/20 px-sm md:px-lg py-sm flex justify-between items-center"
    >
        <div>
            <h2 class="font-headline-md text-headline-md text-on-surface">
                Gestion Newsletter
            </h2>
            <nav class="flex gap-sm mt-1 text-on-surface-variant font-label-sm">
                <span class="text-primary font-bold border-b-2 border-primary"
                    >Dashboard</span
                >
                <span
                    class="hover:text-primary cursor-pointer transition-colors"
                    >Campagnes</span
                >
                <span
                    class="hover:text-primary cursor-pointer transition-colors"
                    >Paramètres</span
                >
            </nav>
        </div>

        <div class="flex items-center gap-md">
            <span
                class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary"
            >
                notifications
            </span>
            <button
                class="bg-primary-container text-on-primary-container px-sm py-xs rounded-lg font-label-sm flex items-center gap-xs hover:opacity-90 transition-opacity"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                Créer une campagne
            </button>
        </div>
    </header>

    <!-- ══════════════════════════════════════════════════════════
       CONTENU PRINCIPAL
  ══════════════════════════════════════════════════════════ -->
    <div class="max-w-container-max mx-auto px-sm md:px-lg py-lg">
        <!-- ── Bento Grid Stats ──────────────────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-md mb-lg">
            <!-- Carte 1 — Total Abonnés -->
            <div
                class="bg-surface-container-lowest p-md rounded-xl border border-outline-variant/10 shadow-sm flex flex-col gap-sm"
            >
                <span
                    class="font-label-sm text-on-surface-variant uppercase tracking-wider text-xs"
                >
                    Total Abonnés
                </span>
                <span
                    class="font-display-lg text-display-lg text-primary leading-none"
                >
                    {{ pagination ? formatTotal(pagination.total) : "12,842" }}
                </span>
                <div class="flex items-center gap-xs">
                    <span
                        class="material-symbols-outlined text-[18px] text-secondary"
                        >trending_up</span
                    >
                    <span class="font-label-sm text-secondary"
                        >+4.2% vs mois dernier</span
                    >
                </div>
            </div>

            <!-- Carte 2 — Taux d'ouverture -->
            <div
                class="bg-surface-container-lowest p-md rounded-xl border border-outline-variant/10 shadow-sm flex flex-col gap-sm"
            >
                <span
                    class="font-label-sm text-on-surface-variant uppercase tracking-wider text-xs"
                >
                    Taux d'ouverture
                </span>
                <span
                    class="font-display-lg text-display-lg text-secondary leading-none"
                >
                    42.8%
                </span>
                <div class="flex items-center gap-xs">
                    <span
                        class="material-symbols-outlined text-[18px] text-on-surface-variant"
                        >query_stats</span
                    >
                    <span class="font-label-sm text-on-surface-variant"
                        >Moyenne secteur&nbsp;: 21%</span
                    >
                </div>
            </div>

            <!-- Carte 3 — Nouveaux ce mois -->
            <div
                class="bg-surface-container-lowest p-md rounded-xl border border-outline-variant/10 shadow-sm flex flex-col gap-sm"
            >
                <span
                    class="font-label-sm text-on-surface-variant uppercase tracking-wider text-xs"
                >
                    Nouveaux ce mois
                </span>
                <span
                    class="font-display-lg text-display-lg text-tertiary leading-none"
                >
                    524
                </span>
                <div class="flex items-center gap-xs">
                    <span
                        class="material-symbols-outlined text-[18px] text-error"
                        >person_remove</span
                    >
                    <span class="font-label-sm text-error"
                        >-12 désabonnements</span
                    >
                </div>
            </div>
        </div>
        <!-- /Bento Grid Stats -->

        <!-- ── Grille principale ─────────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-lg items-start">
            <!-- ════════════════════════════════════════════════════
           SECTION LISTE ABONNÉS (7 colonnes)
      ════════════════════════════════════════════════════ -->
            <div
                class="lg:col-span-7 bg-surface-container-lowest rounded-xl border border-outline-variant/10 overflow-hidden"
            >
                <!-- Header section -->
                <div
                    class="p-md flex items-center justify-between border-b border-outline-variant/10"
                >
                    <h3
                        class="font-headline-md text-headline-md text-on-surface"
                    >
                        Liste des abonnés
                    </h3>

                    <!-- Input recherche -->
                    <div class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant pointer-events-none"
                        >
                            search
                        </span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher…"
                            class="bg-surface-container-low border-none rounded-lg text-label-sm pl-9 pr-md py-xs w-48 focus:ring-2 focus:ring-primary/20 focus:outline-none text-on-surface placeholder:text-on-surface-variant"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <!-- Skeleton loading -->
                    <div v-if="loading" class="p-md space-y-3 animate-pulse">
                        <div
                            v-for="i in 6"
                            :key="i"
                            class="flex items-center gap-md"
                        >
                            <div
                                class="h-4 bg-surface-container rounded flex-1"
                            />
                            <div
                                class="h-4 bg-surface-container rounded w-28"
                            />
                            <div
                                class="h-4 bg-surface-container rounded w-16"
                            />
                            <div class="h-4 bg-surface-container rounded w-8" />
                        </div>
                    </div>

                    <table v-else class="w-full text-left font-body-md">
                        <thead>
                            <tr
                                class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-widest border-b border-outline-variant/10"
                            >
                                <th class="px-md py-sm font-semibold">Email</th>
                                <th
                                    class="px-md py-sm font-semibold hidden sm:table-cell"
                                >
                                    Date d'inscription
                                </th>
                                <th class="px-md py-sm font-semibold">
                                    Statut
                                </th>
                                <th
                                    class="px-md py-sm font-semibold text-right"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-outline-variant/10">
                            <!-- Vide -->
                            <tr v-if="filteredSubscribers.length === 0">
                                <td
                                    colspan="4"
                                    class="px-md py-xl text-center text-on-surface-variant font-label-sm"
                                >
                                    Aucun abonné trouvé.
                                </td>
                            </tr>

                            <!-- Lignes -->
                            <tr
                                v-for="sub in filteredSubscribers"
                                :key="sub.id"
                                class="transition-all duration-150 hover:bg-surface-container-low/50 cursor-default"
                                :style="
                                    hoveredRow === sub.id
                                        ? { transform: 'translateX(4px)' }
                                        : {}
                                "
                                @mouseenter="hoveredRow = sub.id"
                                @mouseleave="hoveredRow = null"
                            >
                                <!-- Email -->
                                <td class="px-md py-sm">
                                    <span
                                        class="font-semibold text-primary text-sm truncate max-w-[200px] block"
                                    >
                                        {{ sub.email }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td
                                    class="px-md py-sm hidden sm:table-cell text-on-surface-variant text-sm"
                                >
                                    {{ formattedDate(sub.created_at) }}
                                </td>

                                <!-- Statut -->
                                <td class="px-md py-sm">
                                    <span
                                        v-if="sub.is_active"
                                        class="bg-primary/10 text-primary px-xs py-0.5 rounded-full text-xs font-bold"
                                    >
                                        Actif
                                    </span>
                                    <span
                                        v-else
                                        class="bg-outline-variant/20 text-on-surface-variant px-xs py-0.5 rounded-full text-xs font-bold"
                                    >
                                        Inactif
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="px-md py-sm text-right">
                                    <button
                                        class="material-symbols-outlined text-outline hover:text-primary transition-colors"
                                        title="Plus d'options"
                                    >
                                        more_vert
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer table -->
                <div
                    class="p-sm bg-surface-container-low/30 border-t border-outline-variant/10 text-center"
                >
                    <button
                        class="text-primary font-label-sm hover:underline transition-all"
                    >
                        Voir tous les abonnés
                    </button>
                </div>
            </div>
            <!-- /Section liste abonnés -->

            <!-- ════════════════════════════════════════════════════
           SECTION COMPOSER (5 colonnes)
      ════════════════════════════════════════════════════ -->
            <div class="lg:col-span-5 flex flex-col gap-md">
                <!-- Carte envoi -->
                <div
                    class="bg-surface-container-lowest rounded-xl border border-outline-variant/10 p-md"
                >
                    <h4
                        class="font-headline-md text-headline-md text-on-surface mb-md"
                    >
                        Nouvel envoi
                    </h4>

                    <div class="flex flex-col gap-sm">
                        <!-- Objet du mail -->
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-sm text-on-surface-variant"
                                >Objet du mail</label
                            >
                            <input
                                v-model="subject"
                                type="text"
                                placeholder="Votre objet ici…"
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-md py-sm font-body-md focus:ring-2 focus:ring-primary outline-none text-on-surface placeholder:text-on-surface-variant/60 transition-shadow"
                            />
                        </div>

                        <!-- Destinataires -->
                        <div class="flex flex-col gap-xs">
                            <label class="font-label-sm text-on-surface-variant"
                                >Destinataires</label
                            >
                            <select
                                v-model="recipient"
                                class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-md py-sm font-body-md focus:ring-2 focus:ring-primary outline-none text-on-surface appearance-none cursor-pointer transition-shadow"
                            >
                                <option value="all">
                                    Tous les abonnés actifs
                                </option>
                                <option value="new">Nouveaux ce mois</option>
                                <option value="inactive">
                                    Inactifs uniquement
                                </option>
                            </select>
                        </div>

                        <!-- Contenu / éditeur -->
                        <div class="flex flex-col gap-0">
                            <label
                                class="font-label-sm text-on-surface-variant mb-xs"
                                >Contenu</label
                            >
                            <div
                                class="rounded-xl border border-outline-variant/30 overflow-hidden"
                            >
                                <!-- Toolbar -->
                                <div
                                    class="bg-surface-container-low p-xs flex gap-xs border-b border-outline-variant/20"
                                >
                                    <button
                                        v-for="icon in [
                                            'format_bold',
                                            'format_italic',
                                            'link',
                                            'image',
                                            'format_list_bulleted',
                                        ]"
                                        :key="icon"
                                        class="material-symbols-outlined text-[20px] text-on-surface-variant hover:text-primary hover:bg-surface-container rounded p-0.5 transition-colors"
                                        :title="icon.replace(/_/g, ' ')"
                                    >
                                        {{ icon }}
                                    </button>
                                </div>
                                <!-- Zone de saisie -->
                                <textarea
                                    v-model="content"
                                    rows="8"
                                    placeholder="Rédigez le contenu de votre email…"
                                    class="w-full bg-surface border-none focus:ring-0 p-md font-body-md resize-none text-on-surface placeholder:text-on-surface-variant/50 outline-none block"
                                />
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <div class="flex items-center justify-between pt-sm">
                            <!-- Gauche : Planifier -->
                            <button
                                class="flex items-center gap-xs text-on-surface-variant font-label-sm hover:text-primary transition-colors"
                            >
                                <span
                                    class="material-symbols-outlined text-[18px]"
                                    >schedule</span
                                >
                                Planifier
                            </button>

                            <!-- Droite : Brouillon + Envoyer -->
                            <div class="flex items-center gap-sm">
                                <button
                                    @click="showDraftToast"
                                    class="text-on-surface-variant font-label-sm hover:text-on-surface transition-colors"
                                >
                                    Brouillon
                                </button>
                                <button
                                    class="bg-primary text-on-primary px-lg py-sm rounded-xl font-label-sm hover:opacity-90 transition-opacity"
                                >
                                    Envoyer maintenant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Carte envoi -->

                <!-- Carte prévisualisation -->
                <div
                    class="bg-primary/5 rounded-xl p-md border border-primary/20 flex gap-md items-center"
                >
                    <div
                        class="w-12 h-12 rounded-lg bg-primary-container flex items-center justify-center text-on-primary-container shadow-sm flex-shrink-0"
                    >
                        <span class="material-symbols-outlined"
                            >visibility</span
                        >
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5 class="font-label-sm text-primary">
                            Prévisualisation mobile
                        </h5>
                        <p
                            class="text-xs text-on-surface-variant mt-1 leading-relaxed"
                        >
                            Vérifiez le rendu de votre contenu sur smartphone
                            avant l'envoi massif.
                        </p>
                    </div>
                    <button
                        class="material-symbols-outlined text-primary hover:scale-110 transition-transform flex-shrink-0"
                    >
                        chevron_right
                    </button>
                </div>
                <!-- /Carte prévisualisation -->
            </div>
            <!-- /Section composer -->
        </div>
        <!-- /Grille principale -->

        <!-- ── Footer ──────────────────────────────────────────── -->
        <footer
            class="w-full py-xl px-sm md:px-lg max-w-container-max mx-auto flex flex-col items-center gap-md border-t border-outline-variant/30 mt-xl"
        >
            <h1
                class="font-display-lg-mobile text-display-lg-mobile text-on-surface"
            >
                BlogModerne
            </h1>
            <nav class="flex flex-wrap gap-md justify-center">
                <a
                    href="#"
                    class="font-label-sm text-on-surface-variant hover:text-primary transition-colors"
                >
                    Mentions légales
                </a>
                <a
                    href="#"
                    class="font-label-sm text-on-surface-variant hover:text-primary transition-colors"
                >
                    Politique de confidentialité
                </a>
                <a
                    href="#"
                    class="font-label-sm text-on-surface-variant hover:text-primary transition-colors"
                >
                    CGU
                </a>
                <a
                    href="#"
                    class="font-label-sm text-on-surface-variant hover:text-primary transition-colors"
                >
                    Contact
                </a>
            </nav>
            <p class="text-on-surface-variant font-label-sm">
                © 2024 BlogModerne.
            </p>
        </footer>
    </div>
    <!-- /max-w-container-max -->

    <!-- ══════════════════════════════════════════════════════════
       TOAST BROUILLON (fixed)
  ══════════════════════════════════════════════════════════ -->
    <div
        class="fixed bottom-lg right-lg bg-on-surface text-surface py-sm px-md rounded-xl shadow-lg z-50 flex items-center gap-sm font-label-sm transition-all duration-300 pointer-events-none"
        :class="
            toastVisible
                ? 'translate-y-0 opacity-100'
                : 'translate-y-24 opacity-0'
        "
    >
        <span class="material-symbols-outlined text-green-400"
            >check_circle</span
        >
        Campagne enregistrée en tant que brouillon.
    </div>
</template>
