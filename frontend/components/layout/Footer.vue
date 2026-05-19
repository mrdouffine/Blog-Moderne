<template>
    <footer
        class="bg-surface-container-lowest border-t border-outline-variant py-xl mt-auto"
    >
        <div
            class="w-full py-xl px-sm md:px-lg max-w-container-max mx-auto flex flex-col md:flex-row justify-between gap-xl text-left"
        >
            <!-- Bloc brand -->
            <div class="flex flex-col gap-md max-w-[300px]">
                <NuxtLink
                    to="/"
                    class="font-display-lg text-display-lg-mobile text-on-surface font-extrabold"
                >
                    BlogModerne
                </NuxtLink>
                <p
                    class="font-body-md text-body-md text-on-surface-variant"
                >
                    Une plateforme de pensée libre pour les esprits curieux.
                    Écrivez, lisez, apprenez.
                </p>
                <div class="flex gap-md">
                    <a
                        href="mailto:kouassidouffan@gmail.com"
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
                        href="https://wa.me/22897254050"
                        class="text-outline hover:text-primary transition-colors"
                        aria-label="WhatsApp"
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
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest font-bold"
                        >Navigation</span
                    >
                    <ul
                        class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant list-none p-0 m-0"
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
                                to="/#newsletter-widget"
                                class="hover:text-primary transition-colors"
                                >Newsletter</NuxtLink
                            >
                        </li>
                    </ul>
                </div>
                <!-- Légal -->
                <div class="flex flex-col gap-md">
                    <span
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest font-bold"
                        >Légal</span
                    >
                    <ul
                        class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant list-none p-0 m-0"
                    >
                        <li>
                            <NuxtLink
                                to="/mentions-legales"
                                class="hover:text-primary transition-colors"
                                >Conditions d'utilisation</NuxtLink
                            >
                        </li>
                        <li>
                            <NuxtLink
                                to="/confidentialite"
                                class="hover:text-primary transition-colors"
                                >Confidentialité</NuxtLink
                            >
                        </li>
                        <li>
                            <NuxtLink
                                to="/a-propos"
                                class="hover:text-primary transition-colors"
                                >Aide</NuxtLink
                            >
                        </li>
                    </ul>
                </div>
                <!-- Admin -->
                <div class="flex flex-col gap-md">
                    <span
                        class="font-label-sm text-label-sm text-on-surface uppercase tracking-widest font-bold"
                        >Admin</span
                    >
                    <ul
                        class="flex flex-col gap-xs font-body-md text-body-md text-on-surface-variant list-none p-0 m-0"
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
                            <NuxtLink
                                to="/admin"
                                class="hover:text-primary transition-colors"
                                >Paramètres</NuxtLink
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
                © {{ new Date().getFullYear() }} BlogModerne. Tous droits réservés.
            </span>
        </div>
    </footer>
</template>
