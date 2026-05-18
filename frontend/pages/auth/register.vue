<script setup lang="ts">
// ============================================================
// Page d'inscription — L'Érudit — layout standalone
// ============================================================
definePageMeta({ middleware: "guest", layout: false });

useSeoMeta({ title: "Inscription — BlogModerne" });

const { register, loading } = useAuth();

const showPassword = ref(false);
const acceptTerms = ref(false);
const apiError = ref("");

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const errors = reactive<Record<string, string>>({});

const validate = (): boolean => {
    Object.keys(errors).forEach((k) => delete errors[k]);
    if (!form.name.trim()) errors.name = "Le nom est requis.";
    if (!form.email) errors.email = "L'email est requis.";
    if (!form.password) errors.password = "Le mot de passe est requis.";
    else if (form.password.length < 8)
        errors.password = "Minimum 8 caractères.";
    return Object.keys(errors).length === 0;
};

const onSubmit = async () => {
    if (!validate()) return;
    apiError.value = "";
    try {
        await register({ ...form, password_confirmation: form.password });
        navigateTo("/");
    } catch (e: any) {
        apiError.value = e?.message ?? "Erreur lors de l'inscription.";
    }
};
</script>

<template>
    <div class="flex flex-col min-h-screen bg-surface">
        <!-- ── Header sticky ──────────────────────────────────────────────── -->
        <header class="bg-surface-bright sticky top-0 z-50 shadow-sm">
            <div
                class="flex justify-between items-center w-full px-md py-sm max-w-content-reading-width mx-auto"
            >
                <!-- Logo -->
                <div class="flex items-center gap-xs">
                    <span
                        class="material-symbols-outlined text-primary text-2xl"
                        >menu_book</span
                    >
                    <span
                        class="font-display-lg-mobile text-display-lg-mobile text-primary"
                        >BlogModerne</span
                    >
                </div>
                <!-- Fermer -->
                <NuxtLink
                    to="/"
                    class="text-on-surface-variant hover:text-primary transition-colors"
                >
                    <span class="material-symbols-outlined">close</span>
                </NuxtLink>
            </div>
        </header>

        <!-- ── Main ───────────────────────────────────────────────────────── -->
        <main class="flex-grow flex items-center justify-center p-md">
            <div class="w-full max-w-content-reading-width">
                <!-- En-tête formulaire -->
                <div class="text-center mb-md">
                    <h1
                        class="font-headline-md text-headline-md text-on-surface mb-xs"
                    >
                        Créer un compte
                    </h1>
                    <p
                        class="font-body-md text-body-md text-on-surface-variant"
                    >
                        Rejoignez notre communauté de passionnés.
                    </p>
                </div>

                <!-- Card formulaire -->
                <div
                    class="bg-surface-container-lowest rounded-xl p-md border border-outline-variant/30"
                    style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05)"
                >
                    <form class="space-y-sm" @submit.prevent="onSubmit">
                        <!-- Champ Nom complet -->
                        <div class="space-y-xs">
                            <label
                                class="font-label-sm text-label-sm text-on-surface-variant block"
                            >
                                Nom complet
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                >
                                    person
                                </span>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Jean Dupont"
                                    class="w-full pl-10 pr-4 py-3 bg-surface rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md text-body-md"
                                />
                            </div>
                            <p
                                v-if="errors.name"
                                class="text-[12px] text-error"
                            >
                                {{ errors.name }}
                            </p>
                        </div>

                        <!-- Champ Email -->
                        <div class="space-y-xs">
                            <label
                                class="font-label-sm text-label-sm text-on-surface-variant block"
                            >
                                Email
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                >
                                    mail
                                </span>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="email@exemple.com"
                                    class="w-full pl-10 pr-4 py-3 bg-surface rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md text-body-md"
                                />
                            </div>
                            <p
                                v-if="errors.email"
                                class="text-[12px] text-error"
                            >
                                {{ errors.email }}
                            </p>
                        </div>

                        <!-- Champ Mot de passe -->
                        <div class="space-y-xs">
                            <label
                                class="font-label-sm text-label-sm text-on-surface-variant block"
                            >
                                Mot de passe
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline"
                                >
                                    lock
                                </span>
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-12 py-3 bg-surface rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md text-body-md"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                >
                                    <span class="material-symbols-outlined">
                                        {{
                                            showPassword
                                                ? "visibility_off"
                                                : "visibility"
                                        }}
                                    </span>
                                </button>
                            </div>
                            <p
                                v-if="errors.password"
                                class="text-[12px] text-error"
                            >
                                {{ errors.password }}
                            </p>
                        </div>

                        <!-- Checkbox CGU -->
                        <div class="flex items-start gap-xs py-xs">
                            <input
                                id="terms"
                                v-model="acceptTerms"
                                type="checkbox"
                                class="mt-1 h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary"
                            />
                            <label
                                for="terms"
                                class="font-body-md text-body-md text-on-surface-variant"
                            >
                                J'accepte les
                                <a
                                    href="#"
                                    class="text-primary font-semibold hover:underline"
                                >
                                    conditions d'utilisation
                                </a>
                            </label>
                        </div>

                        <!-- Erreur API -->
                        <div
                            v-if="apiError"
                            class="p-sm bg-error-container text-error rounded-lg font-body-md text-body-md text-[14px]"
                        >
                            {{ apiError }}
                        </div>

                        <!-- CTA Bouton -->
                        <button
                            type="submit"
                            :disabled="loading || !acceptTerms"
                            class="w-full bg-primary text-on-primary font-label-sm text-label-sm py-4 rounded-xl shadow-sm hover:bg-primary-container active:scale-[0.98] transition-all flex justify-center items-center gap-xs mt-md disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span
                                v-if="loading"
                                class="material-symbols-outlined animate-spin"
                            >
                                progress_activity
                            </span>
                            <template v-else>
                                S'inscrire
                                <span class="material-symbols-outlined"
                                    >arrow_forward</span
                                >
                            </template>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div
                        class="relative my-lg flex items-center justify-center"
                    >
                        <div
                            class="w-full border-t border-outline-variant"
                        ></div>
                        <span
                            class="absolute bg-surface-container-lowest px-4 font-label-sm text-label-sm text-on-surface-variant"
                        >
                            ou s'inscrire avec
                        </span>
                    </div>

                    <!-- Boutons sociaux -->
                    <div class="grid grid-cols-2 gap-sm">
                        <!-- Google -->
                        <button
                            type="button"
                            class="flex items-center justify-center gap-xs py-3 border border-outline-variant rounded-xl hover:bg-surface transition-colors"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4"
                                />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.65l-3.57-2.77c-.99.66-2.26 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853"
                                />
                                <path
                                    d="M5.84 14.11c-.22-.66-.35-1.36-.35-2.11s.13-1.45.35-2.11V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l3.66-2.83z"
                                    fill="#FBBC05"
                                />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.83c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335"
                                />
                            </svg>
                            <span
                                class="font-label-sm text-label-sm text-on-surface"
                                >Google</span
                            >
                        </button>

                        <!-- GitHub -->
                        <button
                            type="button"
                            class="flex items-center justify-center gap-xs py-3 border border-outline-variant rounded-xl hover:bg-surface transition-colors"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                />
                            </svg>
                            <span
                                class="font-label-sm text-label-sm text-on-surface"
                                >GitHub</span
                            >
                        </button>
                    </div>

                    <!-- Lien "Déjà un compte" -->
                    <p
                        class="mt-lg text-center font-body-md text-body-md text-on-surface-variant"
                    >
                        Déjà un compte ?
                        <NuxtLink
                            to="/auth/login"
                            class="text-primary font-bold hover:underline"
                        >
                            Se connecter
                        </NuxtLink>
                    </p>
                </div>
            </div>
        </main>

        <!-- ── Footer ─────────────────────────────────────────────────────── -->
        <footer
            class="bg-surface-container-lowest py-md border-t border-outline-variant mt-auto"
        >
            <div
                class="flex flex-col items-center gap-xs px-md max-w-content-reading-width mx-auto"
            >
                <span
                    class="font-display-lg-mobile text-display-lg-mobile text-primary opacity-50"
                >
                    BlogModerne
                </span>
                <p class="font-label-sm text-label-sm text-on-surface-variant">
                    © 2024 BlogModerne.
                </p>
                <div class="flex gap-md mt-xs">
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Confidentialité
                    </a>
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors"
                    >
                        Conditions
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>
