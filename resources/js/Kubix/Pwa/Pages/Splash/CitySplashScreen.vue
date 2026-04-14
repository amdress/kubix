<template>
  <div
    v-if="isReady"
    class="fixed inset-0 z-[9999] flex flex-col items-center justify-between bg-slate-950 overflow-hidden"
  >

  <h1 class="text-white">
  DEBUG: {{ cityName || 'Sin Nombre' }} - {{ isReady ? 'Listo' : 'Cargando' }}
</h1>
    <div class="absolute inset-0 z-0">
      <img
        v-if="citySplash"
        :key="citySplash"
        :src="citySplash"
        alt="City splash"
        class="w-full h-full object-cover opacity-40 animate-subtle-zoom"
        :class="{ 'scale-110 blur-sm': exiting }"
        @error="(e) => console.error('Error al cargar imagen del backend:', citySplash)"
      />
      <div v-else class="absolute inset-0 bg-slate-900 opacity-50"></div>
      
      <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-transparent to-slate-950"></div>
    </div>

    <header
      class="relative z-10 pt-20 flex flex-col items-center transition-all duration-1000"
      :class="contentVisible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'"
    >
      <img
        v-if="cityLogo"
        :src="cityLogo"
        alt="City logo"
        class="h-12 w-auto object-contain mb-6"
      />

      <h2
        v-else
        class="text-3xl font-black tracking-[0.3em] text-white mb-6"
      >
        KUBIX
      </h2>

      <div class="flex flex-col items-center text-center px-6">
        <h1
          class="text-4xl font-extralight tracking-[0.15em] text-white uppercase"
        >
          {{ cityName }}
        </h1>
      </div>
    </header>

    <StatsSection />

    <footer
      class="relative z-10 pb-20 w-full max-w-[280px] flex flex-col items-center transition-all duration-1000 delay-300"
      :class="contentVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
    >
      <div class="w-full h-[1px] bg-white/10 relative overflow-hidden rounded-full mb-4">
        <div
          class="absolute inset-y-0 left-0 transition-all duration-200 ease-out"
          :style="{
            width: progress + '%',
            backgroundColor: primaryColor,
            boxShadow: `0 0 15px ${primaryColor}`
          }"
        ></div>
      </div>

      <div class="flex justify-between w-full text-[9px] font-medium tracking-widest text-white/40 uppercase">
        <span>{{ statusMessage }}</span>
        <span class="font-mono">{{ Math.round(progress) }}%</span>
      </div>
    </footer>

    <div class="absolute bottom-6 text-[8px] text-white/20 tracking-[0.3em] uppercase">
      KUBIX © 2026
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useTerritoryStore } from '@/Kubix/core/stores/territoryStore'
import StatsSection from '../Landing/components/StatsSection.vue'

const router = useRouter()
const territory = useTerritoryStore()

const contentVisible = ref(false)
const exiting = ref(false)
const progress = ref(0)

/**
 * 🧠 ESTADO REACTIVO DESDE EL STORE
 * isReady: Ahora lee directamente el estado is_ready del store de territorios
 */
const isReady = computed(() => territory.is_ready)

/**
 * 🏙️ BRANDING (Basado estrictamente en la Ciudad)
 * Estos datos vienen del getter .city que configuramos en el store
 */
const cityName = computed(() => territory.city.name)
const primaryColor = computed(() => territory.city.primary_color)
const citySplash = computed(() => territory.city.splash_image) // URL completa del backend
const cityLogo = computed(() => territory.city.logo)

/**
 * ⏳ MENSAJES DE ESTADO
 */
const statusMessage = computed(() => {
  if (progress.value < 40) return 'Sincronizando señal...'
  if (progress.value < 70) return `Enlazando con ${cityName.value}...`
  return 'Identidad confirmada'
})

/**
 * 🚀 LÓGICA DE CARGA Y TRANSICIÓN
 */
onMounted(() => {
  // Delay inicial para suavizar la aparición del contenido
  setTimeout(() => (contentVisible.value = true), 300)

  const duration = 2800 
  const start = performance.now()

  const animate = (now) => {
    const elapsed = now - start
    progress.value = Math.min((elapsed / duration) * 100, 100)

    if (progress.value < 100) {
      requestAnimationFrame(animate)
    } else {
      // Al terminar el progreso, esperamos un momento y salimos hacia el Auth
      setTimeout(() => {
        exiting.value = true
        setTimeout(() => router.push({ name: 'auth.access' }), 800)
      }, 400)
    }
  }

  requestAnimationFrame(animate)
})
</script>

<style scoped>
@keyframes subtle-zoom {
  from { transform: scale(1); }
  to { transform: scale(1.08); }
}

.animate-subtle-zoom {
  animation: subtle-zoom 10s infinite alternate ease-in-out;
}

.blur-sm {
  filter: blur(10px);
  transition: all 0.8s ease;
}
</style>