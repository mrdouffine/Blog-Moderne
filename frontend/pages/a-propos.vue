<script setup lang="ts">
// ============================================================
// blog/frontend/pages/a-propos.vue
// Page À Propos — Rendu haut de gamme, pixel-perfect et immersif
// ============================================================

definePageMeta({ layout: "default" });

useSeoMeta({
    title: "À propos de nous — BlogModerne",
    description: "Découvrez notre histoire, notre vision, et l'équipe passionnée derrière BlogModerne, la voix de l'expression libre au Togo.",
});

// Équipe éditoriale avec les noms complets localisés
const team = [
    {
        name: "COZI moucharif",
        role: "Directeur de Publication & Fondateur",
        bio: "Passionné de technologie et d'émancipation intellectuelle, COZI moucharif a fondé BlogModerne pour offrir une tribune libre et moderne au Togo.",
        avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80"
    },
    {
        name: "MENSAH audrey",
        role: "Rédactrice en Chef",
        bio: "Rédactrice chevronnée et passionnée de littérature d'Afrique de l'Ouest, MENSAH audrey supervise la ligne éditoriale globale avec une grande rigueur.",
        avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80"
    },
    {
        name: "SAHM maimouna",
        role: "Chef de Rubrique Culture & Société",
        bio: "Journaliste engagée, SAHM maimouna explore les mutations sociologiques contemporaines, l'histoire des Nana Benz et la gastronomie.",
        avatar: "https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&auto=format&fit=crop&q=80"
    },
    {
        name: "ADENYO pascaline",
        role: "Responsable Éducation Somatique & Bien-être",
        bio: "Diplômée en sciences de la santé, ADENYO pascaline décrypte avec bienveillance et précision scientifique les thématiques de santé sexuelle.",
        avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&auto=format&fit=crop&q=80"
    },
    {
        name: "KEKEY prisca",
        role: "Reporter-Photographe & Rubrique Sport",
        bio: "KEKEY prisca parcourt le pays, capturant la ferveur sportive dans les stades et les récits inspirants des communautés sportives.",
        avatar: "https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=200&auto=format&fit=crop&q=80"
    }
];

const values = [
    {
        icon: "article",
        title: "Rigueur Scientifique",
        desc: "Nos dossiers sur la biologie, l'éducation somatique et le sport reposent sur des sources rigoureuses et vérifiées."
    },
    {
        icon: "diversity_1",
        title: "Expression Libre",
        desc: "Nous offrons une tribune d'expression saine, inclusive et sans jugement pour débattre de tous les sujets de société."
    },
    {
        icon: "photo_camera",
        title: "Aesthetics Premium",
        desc: "Nous accordons une importance fondamentale à l'impact visuel et à la qualité des clichés photographiques illustrant nos articles."
    }
];
</script>

<template>
    <div class="bg-surface text-on-surface min-h-screen py-xl">
        <main class="max-w-container-max mx-auto px-sm md:px-lg">
            
            <!-- ── Hero Section ── -->
            <section class="text-center max-w-content-reading-width mx-auto mb-2xl">
                <span class="font-label-md text-label-md text-primary uppercase tracking-widest block mb-xs">
                    Qui sommes-nous ?
                </span>
                <h1 class="font-display-xl text-display-xl-mobile sm:text-display-xl text-on-surface font-extrabold mb-md tracking-tight leading-none">
                    BlogModerne : L'expression libre et inspirante
                </h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-[640px] mx-auto">
                    Fondé au Togo, BlogModerne est un espace éditorial unique dédié à la passion des mots, à la culture nationale, au bien-être physique et à la réflexion intime.
                </p>
            </section>

            <!-- ── Image de couverture inspirante ── -->
            <section class="rounded-2xl overflow-hidden aspect-[21/9] mb-2xl shadow-lg border border-outline-variant/30">
                <img 
                    src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=1600&auto=format&fit=crop&q=80" 
                    alt="Espace de travail et inspiration" 
                    class="w-full h-full object-cover"
                />
            </section>

            <!-- ── Notre Histoire & Vision ── -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-xl mb-2xl items-center">
                <div class="space-y-md">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">
                        Notre Vision
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        Chez BlogModerne, nous croyons au pouvoir de l'éducation partagée. Qu'il s'agisse de football togolais, de fiction magique, d'éducation à la sexualité somatique ou de culture générale, notre ambition est de dissiper les tabous et de cultiver l'intelligence collective à l'aide d'articles de haut vol rédigés avec rigueur et bienveillance.
                    </p>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        Chaque dossier, chaque illustration et chaque intervention au sein de la communauté est pensé pour offrir une expérience esthétique et sensorielle de premier ordre à nos lecteurs.
                    </p>
                </div>
                <div class="bg-surface-container-low p-xl rounded-2xl border border-outline-variant/30 space-y-md">
                    <h3 class="font-title-lg text-title-lg text-on-surface font-bold">
                        Nos valeurs fondamentales
                    </h3>
                    <div class="space-y-sm">
                        <div 
                            v-for="val in values" 
                            :key="val.title" 
                            class="flex gap-sm items-start"
                        >
                            <span class="material-symbols-outlined text-primary text-[24px] shrink-0 mt-0.5">
                                {{ val.icon }}
                            </span>
                            <div>
                                <h4 class="font-label-md text-label-md text-on-surface font-bold">
                                    {{ val.title }}
                                </h4>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">
                                    {{ val.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ── Équipe Éditoriale ── -->
            <section class="mb-2xl">
                <div class="text-center max-w-content-reading-width mx-auto mb-xl">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-xs">
                        Notre Équipe
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Les esprits passionnés qui font vibrer et vivre la plateforme chaque jour.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md">
                    <div 
                        v-for="member in team" 
                        :key="member.name"
                        class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md flex flex-col items-center text-center shadow-sm hover:shadow-md transition-shadow group"
                    >
                        <div class="w-24 h-24 rounded-full overflow-hidden mb-sm border-2 border-primary/20 group-hover:border-primary transition-colors">
                            <img 
                                :src="member.avatar" 
                                :alt="member.name" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                        </div>
                        <h3 class="font-title-md text-title-md text-on-surface font-bold mb-3xs">
                            {{ member.name }}
                        </h3>
                        <span class="font-label-sm text-label-sm text-primary mb-sm">
                            {{ member.role }}
                        </span>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            {{ member.bio }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- ── Appel à l'action ── -->
            <section class="bg-primary-container text-on-primary-container rounded-3xl p-xl text-center space-y-md border border-primary/10">
                <h2 class="font-headline-lg text-headline-lg font-bold">
                    Rejoignez la communauté
                </h2>
                <p class="font-body-md text-body-md text-on-primary-container/80 max-w-[600px] mx-auto">
                    Abonnez-vous à notre newsletter pour ne manquer aucune publication sur les thèmes de société, d'actualité culturelle, de sport et de bien-être.
                </p>
                <div class="flex justify-center">
                    <NuxtLink 
                        to="/articles" 
                        class="px-lg py-sm bg-primary text-on-primary font-label-lg text-label-lg rounded-full hover:bg-primary/95 transition-colors shadow-lg active:scale-95 transition-transform inline-block"
                    >
                        Parcourir nos articles
                    </NuxtLink>
                </div>
            </section>

        </main>
    </div>
</template>
