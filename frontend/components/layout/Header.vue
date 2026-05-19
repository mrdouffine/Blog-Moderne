<script setup lang="ts">
// ============================================================
// Header — reproduit exactement la maquette BlogModerne
// sticky, bg blur, logo Playfair, nav desktop, actions, burger
// ============================================================
const authStore = useAuthStore();
const { logout } = useAuth();
const route = useRoute();

const isAuthenticated = computed(() => !!authStore.user);
const isAdmin = computed(() => authStore.user?.role === "admin");

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref<HTMLElement | null>(null);

onClickOutside(userMenuRef, () => (userMenuOpen.value = false));

watch(
    () => route.path,
    () => {
        mobileMenuOpen.value = false;
    },
);

const handleLogout = async () => {
    userMenuOpen.value = false;
    await logout();
    navigateTo("/");
};
</script>

<template>
    <!-- Sticky header avec backdrop blur — identique maquette -->
    <header
        class="sticky top-0 z-50 bg-surface/80 backdrop-blur-md shadow-sm transition-shadow duration-300"
    >
        <nav
            class="flex justify-between items-center w-full px-sm md:px-lg max-w-container-max mx-auto h-20"
        >
            <!-- ── Brand Logo ─────────────────────────────────────── -->
            <NuxtLink
                to="/"
                class="font-display-lg text-display-lg-mobile md:text-headline-md text-on-surface hover:opacity-80 transition-opacity"
            >
                BlogModerne
            </NuxtLink>

            <!-- ── Desktop Links ─────────────────────────────────── -->
            <ul class="hidden md:flex gap-md items-center">
                <li>
                    <NuxtLink
                        to="/"
                        :class="
                            route.path === '/'
                                ? 'text-primary font-bold border-b-2 border-primary pb-1 font-label-sm text-label-sm'
                                : 'text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm'
                        "
                    >
                        Accueil
                    </NuxtLink>
                </li>
                <li>
                    <NuxtLink
                        to="/articles"
                        :class="
                            route.path.startsWith('/articles')
                                ? 'text-primary font-bold border-b-2 border-primary pb-1 font-label-sm text-label-sm'
                                : 'text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm'
                        "
                    >
                        Articles
                    </NuxtLink>
                </li>
                <li>
                    <NuxtLink
                        to="/a-propos"
                        class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm"
                    >
                        À propos
                    </NuxtLink>
                </li>
            </ul>

            <!-- ── Actions desktop ───────────────────────────────── -->
            <div class="flex items-center gap-md">
                <!-- Liens de connexion et d'inscription ou avatar dropdown -->
                <template v-if="!isAuthenticated">
                    <NuxtLink to="/auth/login" class="hidden md:block">
                        <button
                            class="font-label-sm text-label-sm text-on-surface-variant hover:text-primary transition-colors px-4 py-2"
                        >
                            Connexion
                        </button>
                    </NuxtLink>
                    <NuxtLink to="/auth/register" class="hidden md:block">
                        <button
                            class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-sm text-label-sm hover:opacity-90 active:scale-95 transition-all"
                        >
                            Inscription
                        </button>
                    </NuxtLink>
                </template>

                <template v-else>

                    <!-- Avatar utilisateur avec dropdown -->
                    <div ref="userMenuRef" class="relative">
                        <button
                            class="w-10 h-10 rounded-full overflow-hidden border-2 border-primary-container cursor-pointer hover:opacity-90 transition-opacity"
                            @click="userMenuOpen = !userMenuOpen"
                            :aria-expanded="userMenuOpen"
                            aria-label="Menu utilisateur"
                        >
                            <img
                                v-if="authStore.user?.avatar"
                                :src="authStore.user.avatar"
                                :alt="authStore.user.name"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-primary-container flex items-center justify-center font-label-sm text-on-primary-fixed-variant uppercase"
                            >
                                {{ authStore.user?.name?.charAt(0) ?? "?" }}
                            </div>
                        </button>

                        <!-- Dropdown menu -->
                        <Transition name="dropdown">
                            <div
                                v-if="userMenuOpen"
                                class="absolute right-0 mt-2 w-52 bg-surface-container-lowest rounded-xl shadow-lg border border-outline-variant/30 py-1 overflow-hidden"
                            >
                                <div
                                    class="px-4 py-2.5 border-b border-outline-variant/20"
                                >
                                    <p
                                        class="font-label-sm text-label-sm text-on-surface truncate"
                                    >
                                        {{ authStore.user?.name }}
                                    </p>
                                    <p
                                        class="text-[12px] text-outline truncate"
                                    >
                                        {{ authStore.user?.email }}
                                    </p>
                                </div>
                                <NuxtLink
                                    v-if="isAdmin"
                                    to="/admin"
                                    class="flex items-center gap-2 px-4 py-2.5 font-body-md text-body-md text-on-surface hover:bg-surface-container transition-colors"
                                    @click="userMenuOpen = false"
                                >
                                    <span
                                        class="material-symbols-outlined text-[18px]"
                                        >dashboard</span
                                    >
                                    Dashboard
                                </NuxtLink>
                                <hr class="border-outline-variant/20 my-1" />
                                <button
                                    @click="handleLogout"
                                    class="w-full flex items-center gap-2 px-4 py-2.5 font-body-md text-body-md text-error hover:bg-error-container/30 transition-colors"
                                >
                                    <span
                                        class="material-symbols-outlined text-[18px]"
                                        >logout</span
                                    >
                                    Se déconnecter
                                </button>
                            </div>
                        </Transition>
                    </div>
                </template>

                <!-- ── Burger mobile ──────────────────────────────── -->
                <button
                    class="md:hidden text-on-surface p-1 rounded hover:bg-surface-container transition-colors"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Menu"
                >
                    <span class="material-symbols-outlined">{{
                        mobileMenuOpen ? "close" : "menu"
                    }}</span>
                </button>
            </div>
        </nav>

        <!-- ── Menu mobile ────────────────────────────────────── -->
        <Transition name="mobile-menu">
            <div
                v-if="mobileMenuOpen"
                class="md:hidden border-t border-outline-variant/30 bg-surface-container-lowest"
            >
                <ul class="flex flex-col px-sm py-sm gap-xs">
                    <li>
                        <NuxtLink
                            to="/"
                            class="block px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                        >
                            Accueil
                        </NuxtLink>
                    </li>
                    <li>
                        <NuxtLink
                            to="/articles"
                            class="block px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                        >
                            Articles
                        </NuxtLink>
                    </li>
                    <li>
                        <NuxtLink
                            to="/a-propos"
                            class="block px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                        >
                            À propos
                        </NuxtLink>
                    </li>
                    <li class="border-t border-outline-variant/20 pt-xs mt-xs">
                        <template v-if="!isAuthenticated">
                            <NuxtLink
                                to="/auth/login"
                                class="block px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                            >
                                Connexion
                            </NuxtLink>
                            <NuxtLink
                                to="/auth/register"
                                class="block mt-xs px-3 py-2.5 rounded-full text-center font-label-sm text-label-sm bg-primary text-on-primary hover:opacity-90 transition-opacity"
                            >
                                Inscription
                            </NuxtLink>
                        </template>
                        <template v-else>
                            <NuxtLink
                                v-if="isAdmin"
                                to="/admin"
                                class="block px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                            >
                                Dashboard admin
                            </NuxtLink>
                            <button
                                @click="handleLogout"
                                class="w-full text-left px-3 py-2.5 rounded-lg font-label-sm text-label-sm text-error hover:bg-error-container/30 transition-colors"
                            >
                                Se déconnecter
                            </button>
                        </template>
                    </li>
                </ul>
            </div>
        </Transition>
    </header>
</template>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.15s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.97);
}

.mobile-menu-enter-active,
.mobile-menu-leave-active {
    transition: all 0.2s ease;
}
.mobile-menu-enter-from,
.mobile-menu-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
