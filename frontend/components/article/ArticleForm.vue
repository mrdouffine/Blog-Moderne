<script setup lang="ts">
// ============================================================
// ArticleForm — création et édition d'un article
// ============================================================
import type { Article } from '~/types'

interface Props {
  article?: Article        // Si fourni, mode édition
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false,
})

const emit = defineEmits<{
  submit: [data: FormData]
}>()

// ---- État du formulaire ----
const form = reactive({
  title:   props.article?.title   ?? '',
  excerpt: props.article?.excerpt ?? '',
  content: props.article?.content ?? '',
  status:  (props.article?.status ?? 'draft') as 'published' | 'draft',
})

const coverFile     = ref<File | null>(null)
const coverPreview  = ref<string | null>(props.article?.cover_image ?? null)
const errors        = reactive<Record<string, string>>({})

// ---- Prévisualisation de l'image ----
const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0]
  if (!file) return
  coverFile.value = file
  coverPreview.value = URL.createObjectURL(file)
}

const removeCover = () => {
  coverFile.value   = null
  coverPreview.value = null
}

// ---- Validation ----
const validate = (): boolean => {
  Object.keys(errors).forEach((k) => delete errors[k])
  if (!form.title.trim())   errors.title   = 'Le titre est requis.'
  if (!form.excerpt.trim()) errors.excerpt  = "L'extrait est requis."
  if (!form.content.trim()) errors.content  = 'Le contenu est requis.'
  return Object.keys(errors).length === 0
}

// ---- Soumission ----
const onSubmit = () => {
  if (!validate()) return

  const fd = new FormData()
  fd.append('title',   form.title)
  fd.append('excerpt', form.excerpt)
  fd.append('content', form.content)
  fd.append('status',  form.status)
  if (coverFile.value) fd.append('cover_image', coverFile.value)
  // En mode édition, Laravel nécessite _method pour PUT via FormData
  if (props.article?.id) fd.append('_method', 'PUT')

  emit('submit', fd)
}

// Synchronise si l'article est chargé de façon asynchrone
watch(() => props.article, (a) => {
  if (!a) return
  form.title   = a.title
  form.excerpt = a.excerpt
  form.content = a.content
  form.status  = a.status
  coverPreview.value = a.cover_image ?? null
})
</script>

<template>
  <form @submit.prevent="onSubmit" class="space-y-6">
    <!-- Titre -->
    <UiInput
      v-model="form.title"
      label="Titre de l'article"
      placeholder="Un titre accrocheur..."
      required
      :error="errors.title"
    />

    <!-- Extrait -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Extrait <span class="text-red-500">*</span>
      </label>
      <textarea
        v-model="form.excerpt"
        rows="3"
        placeholder="Résumé court affiché dans les listes d'articles..."
        :class="[
          'w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400',
          'resize-none focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors',
          errors.excerpt
            ? 'border-red-400 focus:ring-red-400'
            : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500',
        ]"
      />
      <p v-if="errors.excerpt" class="mt-1 text-xs text-red-600">{{ errors.excerpt }}</p>
    </div>

    <!-- Contenu complet -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">
        Contenu <span class="text-red-500">*</span>
      </label>
      <textarea
        v-model="form.content"
        rows="14"
        placeholder="Rédigez votre article ici (Markdown supporté)..."
        :class="[
          'w-full rounded-lg border px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400',
          'resize-y min-h-[240px] focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors font-mono',
          errors.content
            ? 'border-red-400 focus:ring-red-400'
            : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500',
        ]"
      />
      <p v-if="errors.content" class="mt-1 text-xs text-red-600">{{ errors.content }}</p>
    </div>

    <!-- Image de couverture -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Image de couverture</label>

      <!-- Prévisualisation si image sélectionnée -->
      <div v-if="coverPreview" class="relative mb-3 rounded-xl overflow-hidden aspect-video bg-gray-100">
        <img :src="coverPreview" alt="Prévisualisation" class="w-full h-full object-cover" />
        <button
          type="button"
          @click="removeCover"
          class="absolute top-2 right-2 p-1.5 bg-black/50 hover:bg-black/70 text-white rounded-lg transition-colors"
          aria-label="Supprimer l'image"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Zone de sélection fichier -->
      <label
        v-else
        class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 rounded-xl p-8 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/40 transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <span class="text-sm text-gray-500">Cliquez pour choisir une image</span>
        <span class="text-xs text-gray-400">PNG, JPG, WEBP — max 5 Mo</span>
        <input
          type="file"
          accept="image/*"
          class="sr-only"
          @change="onFileChange"
        />
      </label>
    </div>

    <!-- Statut -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
      <div class="flex gap-4">
        <label
          v-for="opt in [{ value: 'published', label: 'Publié' }, { value: 'draft', label: 'Brouillon' }]"
          :key="opt.value"
          :class="[
            'flex items-center gap-2.5 px-4 py-2.5 rounded-lg border cursor-pointer transition-colors',
            form.status === opt.value
              ? 'border-indigo-500 bg-indigo-50 text-indigo-700'
              : 'border-gray-200 hover:border-gray-300 text-gray-600',
          ]"
        >
          <input
            type="radio"
            :value="opt.value"
            v-model="form.status"
            class="accent-indigo-600"
          />
          <span class="text-sm font-medium">{{ opt.label }}</span>
        </label>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
      <NuxtLink to="/admin/articles">
        <UiButton variant="ghost" type="button">Annuler</UiButton>
      </NuxtLink>
      <UiButton type="submit" :loading="loading">
        {{ article ? 'Mettre à jour' : 'Publier l\'article' }}
      </UiButton>
    </div>
  </form>
</template>
