<script setup lang="ts">
// ============================================================
// SubscribeForm — newsletter widget
// Supporte le mode "dark" (fond primary de la sidebar)
// ============================================================
const props = withDefaults(
    defineProps<{
        dark?: boolean; // true = fond primary, bouton secondary
    }>(),
    { dark: false },
);

const { subscribe, loading } = useNewsletter();

const email = ref("");
const success = ref(false);
const error = ref("");

const isValidEmail = (e: string) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e);

const onSubmit = async () => {
    error.value = "";
    success.value = false;

    if (!email.value.trim()) {
        error.value = "Veuillez entrer votre adresse email.";
        return;
    }
    if (!isValidEmail(email.value)) {
        error.value = "Adresse email invalide.";
        return;
    }

    try {
        await subscribe(email.value.trim());
        success.value = true;
        email.value = "";
    } catch (e: any) {
        error.value = e?.message ?? "Une erreur est survenue.";
    }
};
</script>

<template>
    <!-- Mode dark : sur fond primary (sidebar widget) -->
    <form v-if="dark" @submit.prevent="onSubmit" class="flex flex-col gap-xs">
        <!-- Succès inline dark -->
        <div v-if="success" class="text-center py-sm">
            <span
                class="material-symbols-outlined text-[32px] text-on-primary opacity-90 block mb-xs"
                >check_circle</span
            >
            <p class="font-label-sm text-label-sm text-on-primary">
                Inscription réussie !
            </p>
            <p class="text-[12px] text-on-primary opacity-75 mt-1">
                Merci, vous recevrez nos prochains articles.
            </p>
        </div>

        <template v-else>
            <input
                v-model="email"
                type="email"
                placeholder="votre@email.com"
                class="w-full px-4 py-2.5 rounded-lg border-none bg-white/10 text-on-primary placeholder:text-on-primary/60 focus:ring-2 focus:ring-white/50 outline-none font-body-md text-body-md"
            />
            <p v-if="error" class="text-[12px] text-on-primary/80 text-center">
                {{ error }}
            </p>
            <button
                type="submit"
                :disabled="loading"
                class="w-full py-2.5 bg-white text-primary font-bold font-label-sm text-label-sm rounded-lg hover:bg-white/90 active:scale-[0.98] transition-all disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-xs shadow-sm"
            >
                <span
                    v-if="loading"
                    class="material-symbols-outlined text-[18px] text-primary animate-spin"
                    >progress_activity</span
                >
                {{ loading ? "En cours..." : "S'abonner" }}
            </button>
        </template>
    </form>

    <!-- Mode light : section newsletter autonome -->
    <div v-else class="w-full max-w-md mx-auto">
        <Transition name="fade">
            <!-- Succès light -->
            <div
                v-if="success"
                class="flex flex-col items-center gap-sm py-md text-center"
            >
                <span class="material-symbols-outlined text-[40px] text-primary"
                    >check_circle</span
                >
                <div>
                    <p class="font-label-sm text-label-sm text-on-surface">
                        Vous êtes abonné(e) !
                    </p>
                    <p
                        class="font-body-md text-body-md text-on-surface-variant mt-base"
                    >
                        Merci de votre intérêt.
                    </p>
                </div>
                <button
                    @click="success = false"
                    class="text-[12px] text-outline hover:text-primary underline transition-colors"
                >
                    S'inscrire avec un autre email
                </button>
            </div>

            <!-- Formulaire light -->
            <form
                v-else
                @submit.prevent="onSubmit"
                class="flex flex-col gap-xs"
            >
                <div class="flex flex-col sm:flex-row gap-xs">
                    <input
                        v-model="email"
                        type="email"
                        placeholder="votre@email.com"
                        :class="[
                            'flex-1 px-4 py-3 rounded-xl border font-body-md text-body-md text-on-surface placeholder:text-outline outline-none transition-colors',
                            error
                                ? 'border-error focus:ring-2 focus:ring-error/30'
                                : 'border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest',
                        ]"
                    />
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-6 py-3 bg-primary text-on-primary rounded-xl font-label-sm text-label-sm hover:opacity-90 active:scale-95 transition-all disabled:opacity-60 flex items-center gap-xs"
                    >
                        <span
                            v-if="loading"
                            class="material-symbols-outlined text-[18px] animate-spin"
                            >progress_activity</span
                        >
                        {{ loading ? "..." : "S'abonner" }}
                    </button>
                </div>
                <p v-if="error" class="text-[12px] text-error text-center">
                    {{ error }}
                </p>
                <p class="text-[12px] text-outline text-center">
                    Pas de spam. Désinscription possible à tout moment.
                </p>
            </form>
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
