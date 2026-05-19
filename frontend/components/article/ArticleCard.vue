<script setup lang="ts">
// ============================================================
// ArticleCard — reproduit exactement la maquette BlogModerne
// bg-surface-container-lowest, rounded-xl, hover shadow + scale
// ============================================================
import type { Article } from "~/types";

const props = defineProps<{ article: Article }>();

const coverImage = computed(
    () =>
        props.article.cover_image ??
        "https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=800&q=80",
);

// Format court : "15 Jan · 5 min"
const shortMeta = computed(() => {
    const d = new Date(props.article.published_at ?? props.article.created_at);
    const day = d.getDate();
    const month = d
        .toLocaleDateString("fr-FR", { month: "short" })
        .replace(".", "");
    const min = props.article.reading_time ?? "?";
    return `${day} ${month} · ${min} min`;
});
</script>

<template>
    <article
        class="flex flex-col bg-surface-container-lowest rounded-xl overflow-hidden border border-outline-variant/30 hover:shadow-lg transition-all group cursor-pointer"
    >
        <!-- Image de couverture -->
        <NuxtLink
            :to="`/articles/${article.slug}`"
            class="aspect-video overflow-hidden block bg-surface-container-highest"
        >
            <img
                :src="coverImage"
                alt=""
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            />
        </NuxtLink>

        <!-- Contenu de la card -->
        <div class="p-md flex flex-col gap-base flex-grow">
            <!-- Catégorie / tag -->
            <span class="text-primary font-label-sm text-label-sm">
                {{ article.category ?? "Technologie" }}
            </span>

            <!-- Titre -->
            <NuxtLink :to="`/articles/${article.slug}`">
                <h3
                    class="font-headline-md text-headline-md group-hover:text-primary transition-colors"
                >
                    {{ article.title }}
                </h3>
            </NuxtLink>

            <!-- Extrait -->
            <p
                class="font-body-md text-body-md text-on-surface-variant line-clamp-2 mt-base"
            >
                {{ article.excerpt }}
            </p>

            <!-- Footer : auteur + date -->
            <div
                class="flex items-center gap-sm mt-auto pt-md border-t border-outline-variant/20"
            >
                <!-- Avatar auteur -->
                <img
                    v-if="article.author?.avatar"
                    :src="article.author.avatar"
                    :alt="article.author.name"
                    class="w-8 h-8 rounded-full border border-primary-container flex-shrink-0"
                />
                <div
                    v-else
                    class="w-8 h-8 rounded-full border border-primary-container bg-primary-container flex items-center justify-center font-label-sm text-on-primary-fixed-variant uppercase flex-shrink-0"
                >
                    {{ article.author?.name?.charAt(0) ?? "?" }}
                </div>

                <div class="flex flex-col min-w-0">
                    <span
                        class="font-label-sm text-label-sm text-on-surface truncate"
                    >
                        {{ article.author?.name ?? "Auteur" }}
                    </span>
                    <span class="text-[12px] text-outline">{{
                        shortMeta
                    }}</span>
                </div>
            </div>
        </div>
    </article>
</template>
