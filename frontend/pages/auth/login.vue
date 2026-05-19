<script setup lang="ts">
// ============================================================
// Page de connexion — L'Érudit — layout standalone
// ============================================================
definePageMeta({ middleware: "guest", layout: false });

useSeoMeta({ title: "Connexion — BlogModerne" });

const { login, loading } = useAuth();

const showPassword = ref(false);
const apiError = ref("");

const form = reactive({ email: "", password: "" });
const errors = reactive<Record<string, string>>({});

const mainRef = ref<HTMLElement | null>(null);

// ── Animation fade-in au mount + Gestion des erreurs URL ───────────────────
onMounted(() => {
    if (mainRef.value) {
        mainRef.value.style.opacity = "0";
        mainRef.value.style.transform = "translateY(10px)";
        mainRef.value.style.transition =
            "opacity 0.6s ease-out, transform 0.6s ease-out";
        requestAnimationFrame(() => {
            if (mainRef.value) {
                mainRef.value.style.opacity = "1";
                mainRef.value.style.transform = "translateY(0)";
            }
        });
    }

    const route = useRoute();
    if (route.query.error) {
        if (route.query.error === "oauth_failed") {
            apiError.value = "La connexion sociale a échoué. Veuillez réessayer.";
        } else if (route.query.error === "provider_unsupported") {
            apiError.value = "Ce service de connexion n'est pas supporté.";
        } else if (route.query.error === "fetch_user_failed") {
            apiError.value = "Impossible de récupérer votre profil utilisateur.";
        }
    }
});

const validate = () => {
    Object.keys(errors).forEach((k) => delete errors[k]);
    if (!form.email) errors.email = "L'email est requis.";
    if (!form.password) errors.password = "Le mot de passe est requis.";
    return Object.keys(errors).length === 0;
};

const onSubmit = async () => {
    if (!validate()) return;
    apiError.value = "";
    try {
        const redirectTo = route.query.redirect as string || undefined;
        await login({ email: form.email, password: form.password }, redirectTo);
    } catch (e: any) {
        apiError.value = e?.message ?? "Identifiants invalides.";
    }
};

const fillAdminDemo = async () => {
    form.email = "kouassidouffan@gmail.com";
    form.password = "vladimir123";
    await onSubmit();
};

const socialLoading = ref<string | null>(null);

const loginWithSocial = async (provider: string) => {
    socialLoading.value = provider;
    apiError.value = "";
    try {
        const { $api } = useNuxtApp();
        const res = await ($api as any)(`/auth/${provider}/redirect`);
        if (res.success && res.url) {
            window.location.href = res.url;
        } else {
            apiError.value = "Impossible de se connecter avec ce compte.";
        }
    } catch (e: any) {
        apiError.value = e?.response?._data?.message ?? "Une erreur est survenue lors de la connexion.";
    } finally {
        socialLoading.value = null;
    }
};
</script>

<template>
    <div class="flex flex-col min-h-screen bg-surface">
        <!-- ── Header ─────────────────────────────────────────────────────── -->
        <header class="bg-surface-bright sticky top-0 z-50">
            <div
                class="flex justify-between items-center w-full px-md py-sm max-w-content-reading-width mx-auto"
            >
                <div class="flex items-center gap-xs">
                    <span
                        class="material-symbols-outlined text-primary text-[32px]"
                        >menu_book</span
                    >
                    <h1
                        class="font-display-lg-mobile text-display-lg-mobile text-primary"
                    >
                        BlogModerne
                    </h1>
                </div>
            </div>
        </header>

        <!-- ── Main ───────────────────────────────────────────────────────── -->
        <main
            ref="mainRef"
            class="flex-grow flex flex-col items-center justify-center px-md py-xl"
        >
            <div class="w-full max-w-content-reading-width">
                <!-- Section titre -->
                <div class="text-center mb-lg">
                    <h2
                        class="font-headline-md text-headline-md text-on-surface mb-xs"
                    >
                        Connexion
                    </h2>
                    <p
                        class="font-body-md text-body-md text-on-surface-variant"
                    >
                        Ravi de vous revoir !
                    </p>
                </div>

                <!-- Card login -->
                <div
                    class="bg-surface-container-lowest rounded-xl p-md md:p-lg border border-outline-variant/30"
                    style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05)"
                >
                    <form class="space-y-md" @submit.prevent="onSubmit">
                        <!-- Email -->
                        <div class="space-y-xs">
                            <label
                                class="font-label-sm text-label-sm text-on-surface-variant block"
                            >
                                Email
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-sm top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant/60"
                                >
                                    mail
                                </span>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="nom@exemple.com"
                                    class="w-full pl-[44px] pr-md py-sm bg-surface-container-low border border-outline-variant rounded-xl font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                />
                            </div>
                            <p
                                v-if="errors.email"
                                class="text-[12px] text-error"
                            >
                                {{ errors.email }}
                            </p>
                        </div>

                        <!-- Mot de passe -->
                        <div class="space-y-xs">
                            <div class="flex justify-between items-center">
                                <label
                                    class="font-label-sm text-label-sm text-on-surface-variant"
                                >
                                    Mot de passe
                                </label>
                                <a
                                    href="#"
                                    class="font-label-sm text-label-sm text-primary hover:underline"
                                >
                                    Mot de passe oublié ?
                                </a>
                            </div>
                            <div class="relative">
                                <span
                                    class="absolute left-sm top-1/2 -translate-y-1/2 material-symbols-outlined text-on-surface-variant/60"
                                >
                                    lock
                                </span>
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    placeholder="••••••••"
                                    class="w-full pl-[44px] pr-[44px] py-sm bg-surface-container-low border border-outline-variant rounded-xl font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-sm top-1/2 -translate-y-1/2 text-on-surface-variant/60 hover:text-primary transition-colors"
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

                        <!-- Erreur API -->
                        <div
                            v-if="apiError"
                            class="p-sm bg-error-container rounded-xl font-body-md text-body-md text-error text-[14px]"
                        >
                            {{ apiError }}
                        </div>

                        <!-- CTA -->
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-primary hover:bg-primary-container text-on-primary font-label-sm text-label-sm py-md rounded-xl transition-all active:scale-[0.98] shadow-sm disabled:opacity-60 flex items-center justify-center gap-xs"
                        >
                            <span
                                v-if="loading"
                                class="material-symbols-outlined animate-spin"
                            >
                                progress_activity
                            </span>
                            <span v-else>Se connecter</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-lg">
                        <div class="absolute inset-0 flex items-center">
                            <div
                                class="w-full border-t border-outline-variant"
                            ></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span
                                class="bg-surface-container-lowest px-sm font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider"
                            >
                                Ou continuer avec
                            </span>
                        </div>
                    </div>

                    <!-- Boutons sociaux -->
                    <div class="grid grid-cols-2 gap-sm">
                        <!-- Google -->
                        <button
                            type="button"
                            :disabled="socialLoading !== null || loading"
                            @click="loginWithSocial('google')"
                            class="flex items-center justify-center gap-xs border border-outline-variant hover:bg-surface-container-low py-sm rounded-xl transition-all group disabled:opacity-50"
                        >
                            <svg v-if="socialLoading !== 'google'" class="w-5 h-5" viewBox="0 0 24 24">
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
                            <span v-else class="material-symbols-outlined text-[20px] animate-spin text-primary">
                                progress_activity
                            </span>
                            <span
                                class="font-label-sm text-label-sm text-on-surface"
                                >Google</span
                            >
                        </button>

                        <!-- GitHub -->
                        <button
                            type="button"
                            :disabled="socialLoading !== null || loading"
                            @click="loginWithSocial('github')"
                            class="flex items-center justify-center gap-xs border border-outline-variant hover:bg-surface-container-low py-sm rounded-xl transition-all group disabled:opacity-50"
                        >
                            <svg
                                v-if="socialLoading !== 'github'"
                                class="w-5 h-5"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                />
                            </svg>
                            <span v-else class="material-symbols-outlined text-[20px] animate-spin text-primary">
                                progress_activity
                            </span>
                            <span
                                class="font-label-sm text-label-sm text-on-surface"
                                >GitHub</span
                            >
                        </button>
                    </div>

                    <!-- Lien inscription -->
                    <p
                        class="mt-lg text-center font-body-md text-body-md text-on-surface-variant"
                    >
                        Pas encore de compte ?
                        <NuxtLink
                            to="/auth/register"
                            class="text-primary font-bold hover:underline transition-all"
                        >
                            S'inscrire
                        </NuxtLink>
                    </p>
                </div>
            </div>
        </main>

        <!-- ── Footer ─────────────────────────────────────────────────────── -->
        <footer
            class="bg-surface-container-lowest border-t border-outline-variant"
        >
            <div
                class="flex flex-col items-center w-full px-md py-lg gap-md max-w-content-reading-width mx-auto"
            >
                <div class="flex gap-md flex-wrap justify-center">
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-secondary transition-all"
                    >
                        À propos
                    </a>
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-secondary transition-all"
                    >
                        Confidentialité
                    </a>
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-secondary transition-all"
                    >
                        Conditions
                    </a>
                    <a
                        href="#"
                        class="font-label-sm text-label-sm text-on-surface-variant hover:text-secondary transition-all"
                    >
                        Contact
                    </a>
                </div>
                <p
                    class="font-label-sm text-label-sm text-secondary text-center"
                >
                    © 2024 BlogModerne. Méditations et Perspectives.
                </p>
            </div>
        </footer>
    </div>
</template>
