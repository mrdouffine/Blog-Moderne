<script setup lang="ts">
import { onMounted } from 'vue'
import { useRoute, useRouter } from '#app'
import { useAuthStore } from '~/stores/auth'
import { useUiStore } from '~/stores/ui'

definePageMeta({
  layout: 'default',
  middleware: 'guest'
})

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const ui = useUiStore()

onMounted(async () => {
  const token = route.query.token as string

  if (token) {
    try {
      // Stocker le token dans le store Pinia & cookie
      authStore._setToken(token)
      
      // Récupérer les infos utilisateur correspondantes
      await authStore.fetchMe()
      
      ui.success('Connexion réussie ! Bienvenue sur BlogModerne.')
      router.push('/')
    } catch (err) {
      authStore._clearToken()
      ui.error('Échec de la récupération du profil utilisateur.')
      router.push('/auth/login?error=fetch_user_failed')
    }
  } else {
    ui.error('Token d\'authentification manquant.')
    router.push('/auth/login?error=no_token')
  }
})
</script>

<template>
  <div class="min-h-[60vh] flex flex-col items-center justify-center gap-md">
    <!-- Spinner moderne de chargement -->
    <div class="relative w-16 h-16">
      <div class="w-full h-full rounded-full border-4 border-primary/20 animate-ping"></div>
      <div class="absolute inset-0 w-full h-full rounded-full border-4 border-transparent border-t-primary animate-spin"></div>
    </div>
    
    <div class="text-center flex flex-col gap-xs">
      <h1 class="font-headline-md text-headline-md text-on-surface">
        Connexion en cours...
      </h1>
      <p class="font-body-md text-body-md text-on-surface-variant">
        Veuillez patienter pendant que nous sécurisons votre session.
      </p>
    </div>
  </div>
</template>
