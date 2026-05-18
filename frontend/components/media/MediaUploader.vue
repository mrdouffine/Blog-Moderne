<script setup lang="ts">
// ============================================================
// MediaUploader — zone drag & drop pour l'upload de médias
// ============================================================
import type { Media } from '~/types'

interface Props {
  articleId?: number
  accept?:    string
  multiple?:  boolean
}

const props = withDefaults(defineProps<Props>(), {
  accept:   'image/*',
  multiple: false,
})

const emit = defineEmits<{
  uploaded: [media: Media]
}>()

const { uploadMedia } = useMedia()

const isDragging  = ref(false)
const uploading   = ref(false)
const progress    = ref(0)           // 0-100 (simulé)
const previews    = ref<{ url: string; name: string }[]>([])
const errorMsg    = ref('')

// ---- Drag events ----
const onDragEnter = (e: DragEvent) => { e.preventDefault(); isDragging.value = true }
const onDragOver  = (e: DragEvent) => { e.preventDefault() }
const onDragLeave = (e: DragEvent) => {
  // Ne désactiver que si on quitte réellement la zone (pas un enfant)
  if (!(e.currentTarget as HTMLElement).contains(e.relatedTarget as Node)) {
    isDragging.value = false
  }
}
const onDrop = (e: DragEvent) => {
  e.preventDefault()
  isDragging.value = false
  const files = Array.from(e.dataTransfer?.files ?? [])
  processFiles(files)
}

const onFileInput = (e: Event) => {
  const files = Array.from((e.target as HTMLInputElement).files ?? [])
  processFiles(files)
}

// ---- Upload ----
const processFiles = async (files: File[]) => {
  if (!files.length) return
  errorMsg.value  = ''
  uploading.value = true
  progress.value  = 0

  // Simulation de progression (barre de chargement)
  const interval = setInterval(() => {
    if (progress.value < 90) progress.value += 10
  }, 120)

  try {
    for (const file of files) {
      const fd = new FormData()
      fd.append('file', file)
      if (props.articleId) fd.append('article_id', String(props.articleId))

      const media = await uploadMedia(fd)
      previews.value.push({ url: media.url, name: file.name })
      emit('uploaded', media)
    }
    progress.value = 100
  } catch (e: any) {
    errorMsg.value = e?.message ?? "Erreur lors de l'upload."
  } finally {
    clearInterval(interval)
    // Reset après un court délai
    setTimeout(() => {
      uploading.value = false
      progress.value  = 0
    }, 600)
  }
}
</script>

<template>
  <div class="space-y-4">
    <!-- Zone de dépôt -->
    <div
      :class="[
        'relative rounded-2xl border-2 border-dashed p-8 text-center transition-all duration-200',
        isDragging
          ? 'border-indigo-500 bg-indigo-50/60 scale-[1.01]'
          : 'border-gray-300 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50/30',
      ]"
      @dragenter="onDragEnter"
      @dragover="onDragOver"
      @dragleave="onDragLeave"
      @drop="onDrop"
    >
      <!-- Icône -->
      <div class="flex flex-col items-center gap-3">
        <div :class="['w-12 h-12 rounded-full flex items-center justify-center transition-colors', isDragging ? 'bg-indigo-100' : 'bg-gray-100']">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            :class="['w-6 h-6 transition-colors', isDragging ? 'text-indigo-600' : 'text-gray-400']"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>

        <div>
          <p class="text-sm font-medium text-gray-700">
            {{ isDragging ? 'Relâchez pour uploader' : 'Glissez-déposez vos fichiers ici' }}
          </p>
          <p class="text-xs text-gray-400 mt-0.5">ou</p>
        </div>

        <!-- Input fichier caché -->
        <label class="cursor-pointer">
          <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Parcourir
          </span>
          <input
            type="file"
            class="sr-only"
            :accept="accept"
            :multiple="multiple"
            @change="onFileInput"
          />
        </label>

        <p class="text-xs text-gray-400">PNG, JPG, WEBP, GIF — max 10 Mo</p>
      </div>

      <!-- Overlay de chargement -->
      <div
        v-if="uploading"
        class="absolute inset-0 rounded-2xl bg-white/80 backdrop-blur-sm flex flex-col items-center justify-center gap-3"
      >
        <UiSpinner size="md" />
        <div class="w-48">
          <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div
              class="h-full bg-indigo-600 rounded-full transition-all duration-200"
              :style="{ width: `${progress}%` }"
            />
          </div>
          <p class="text-xs text-center text-gray-500 mt-1">{{ progress }}%</p>
        </div>
      </div>
    </div>

    <!-- Erreur -->
    <UiAlert v-if="errorMsg" type="error" :message="errorMsg" @close="errorMsg = ''" />

    <!-- Prévisualisations des fichiers uploadés -->
    <div v-if="previews.length > 0" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
      <div
        v-for="(preview, i) in previews"
        :key="i"
        class="relative rounded-xl overflow-hidden bg-gray-100 aspect-square"
      >
        <img :src="preview.url" :alt="preview.name" class="w-full h-full object-cover" />
        <div class="absolute inset-x-0 bottom-0 bg-black/50 px-2 py-1">
          <p class="text-xs text-white truncate">{{ preview.name }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
