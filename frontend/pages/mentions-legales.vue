<script setup lang="ts">
definePageMeta({ layout: "default" });
useSeoMeta({
    title: "Mentions Légales — BlogModerne",
    description: "Mentions légales de l'éditeur et de l'hébergeur de BlogModerne.",
});
</script>

<template>
    <div class="bg-surface text-on-surface min-h-screen py-xl">
        <main class="max-w-content-reading-width mx-auto px-sm md:px-lg space-y-md">
            <span class="font-label-md text-label-md text-primary uppercase tracking-widest block">Légal</span>
            <h1 class="font-display-lg text-display-lg font-extrabold text-on-surface">Mentions Légales</h1>
            
            <h2 class="font-title-lg text-title-lg text-on-surface font-bold mt-lg">Éditeur du site</h2>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Le site BlogModerne est édité par l'équipe BlogModerne Togo, sous la direction éditoriale de <strong>DOUFFAN Kouassi vladimir Pierrick ELom</strong>.
                Pour toute question, vous pouvez nous contacter via la page À Propos ou notre support officiel.
            </p>
            
            <h2 class="font-title-lg text-title-lg text-on-surface font-bold mt-lg">Hébergement</h2>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                Le site et sa base de données sont hébergés localement sur un environnement Docker virtualisé et hautement sécurisé, assurant le contrôle absolu et souverain des contenus.
            </p>
        </main>
    </div>
</template>
