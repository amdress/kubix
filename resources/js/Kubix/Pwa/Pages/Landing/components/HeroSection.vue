<template>
  <section
    v-if="isReady"
    class="relative min-h-screen flex items-center justify-center px-6 overflow-hidden pt-24"
  >
    <!-- 🌈 BACKGROUND BLOBS -->
    <div 
      class="absolute top-[-10%] left-[-5%] w-[700px] h-[700px] rounded-full blur-[180px] pointer-events-none transition-colors duration-1000"
      :style="{ backgroundColor: `${brandColor}15` }" 
    />
    <div 
      class="absolute bottom-0 right-[-5%] w-[500px] h-[500px] rounded-full blur-[140px] pointer-events-none transition-colors duration-1000"
      :style="{ backgroundColor: `${brandColor}10` }"
    />

    <div class="max-w-7xl w-full mx-auto grid md:grid-cols-2 gap-16 items-center relative z-10">
      
      <!-- 🧠 TEXTO -->
      <div>
        <div class="inline-flex items-center gap-3 mb-10 px-5 py-2.5 rounded-full bg-white/[0.04] border border-white/[0.07]">
          <span class="flex h-2 w-2 relative">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :style="{ backgroundColor: brandColor }" />
            <span class="relative inline-flex rounded-full h-2 w-2" :style="{ backgroundColor: brandColor }" />
          </span>
          <span class="text-[9px] font-black uppercase tracking-[0.4em]" :style="{ color: brandColor }">
            {{ t('landing.status') }}
          </span>
        </div>

        <h1 class="text-5xl md:text-7xl font-black leading-[0.88] tracking-[-0.04em] mb-8">
          <span class="text-white block">{{ t('landing.hero.title_1') }}</span>
          <span class="italic font-extralight text-slate-600 block">{{ t('landing.hero.title_2') }}</span>
          
          <span class="relative inline-block mt-2">
            <span
              v-if="currentName"
              class="text-transparent bg-clip-text bg-gradient-to-r block transition-all duration-700 pb-2"
              :style="{ backgroundImage: `linear-gradient(to right, ${brandColor}, #6EE7B7)` }"
            >
              {{ currentName }}.
            </span>
            <span v-else class="text-transparent bg-clip-text bg-gradient-to-b from-white to-slate-500 block">
              {{ t('landing.hero.title_3') }}
            </span>
          </span>
        </h1>

        <p class="text-lg text-slate-400 max-w-lg leading-relaxed font-light mb-10">
          <i18n-t keypath="landing.hero.subtitle" tag="span">
            <template #highlight>
              <span class="text-white font-normal">{{ t('landing.hero.highlight') }}</span>
            </template>
          </i18n-t>
        </p>

        <div class="flex flex-col sm:flex-row gap-4">
          <button
            @click="router.push({ name: 'auth.access' })"
            class="group relative px-10 py-5 bg-white text-black rounded-full font-black uppercase tracking-widest text-[11px] transition-all hover:scale-105 active:scale-95 overflow-hidden shadow-xl"
          >
            <span class="relative z-10">{{ t('landing.hero.cta_primary') }}</span>
            <div 
              class="absolute inset-0 translate-y-[100%] group-hover:translate-y-0 transition-transform duration-500 opacity-20" 
              :style="{ backgroundColor: brandColor }" 
            />
          </button>
        </div>
      </div>

      <!-- 🗺️ MAPA -->
      <div class="relative hidden md:block">
        <div 
          class="absolute inset-0 rounded-[2rem] blur-2xl opacity-20 transition-colors duration-1000" 
          :style="{ backgroundColor: brandColor }" 
        />
        
        <div class="relative w-full h-[520px] rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl bg-slate-950">
          <KubixMap 
            :items="mapItems"
            :config="mapConfig"
            class="absolute inset-0 w-full h-full"
          />
          
          <div class="absolute inset-0 bg-gradient-to-t from-[#05070a] via-transparent to-transparent pointer-events-none" />
          <div class="absolute inset-0 bg-gradient-to-r from-[#05070a] via-transparent to-transparent pointer-events-none" />

          <div
            v-if="currentName"
            class="absolute bottom-8 left-8 px-5 py-3 rounded-2xl bg-[#05070a]/80 backdrop-blur-xl border border-white/10"
          >
            <p class="text-[9px] text-slate-500 uppercase tracking-widest mb-1">
              Território detectado
            </p>
            <p class="text-sm font-black text-white capitalize">
              {{ currentName }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useTerritoryStore } from '@/Kubix/core/stores/territoryStore'
import KubixMap from '@/Kubix/Pwa/shared/Ui/maps/WorldMap.vue' 

const router = useRouter()
const { t } = useI18n({ useScope: 'global' })
const territory = useTerritoryStore()

// 🧠 READY
const isReady = computed(() => !!territory.territory_data)

// 🏙️ GEO
const cityName = computed(() => territory.geo?.city || '')
const neighborhoodName = computed(() => territory.geo?.neighborhood || '')

// 🎨 BRANDING
const cityBranding = computed(() => territory.branding?.city || {})
const neighborhoodBranding = computed(() => territory.branding?.neighborhood || {})

const hasNeighborhood = computed(() => !!neighborhoodBranding.value?.is_active)

// 🎨 COLOR
const brandColor = computed(() => {
  if (hasNeighborhood.value && neighborhoodBranding.value?.primary_color) {
    return neighborhoodBranding.value.primary_color
  }
  return cityBranding.value?.primary_color || '#6366f1'
})

// 🧠 NOMBRE ACTUAL
const currentName = computed(() => {
  return neighborhoodName.value || cityName.value || ''
})

// 🗺️ CONFIG MAPA
const mapConfig = computed(() => ({
  zoom: 13,
  maxZoom: 15,
  center: [-25.429, -49.267], // fallback Curitiba
  interactive: false,
  primaryColor: brandColor.value,
  tileLayer: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
}))

// 📍 ITEMS
const mapItems = computed(() => {
  const items = []

  const geojson = territory.territory_data?.geojson || null

  if (geojson) {
    items.push({
      type: 'geojson',
      data: geojson,
      color: brandColor.value,
      fillOpacity: 0.2,
      label: currentName.value
    })
  }

  return items
})
</script>

<style scoped>
@media (min-width: 2000px) {
  h1 { font-size: 10rem !important; }
  .max-w-7xl { max-width: 1800px !important; }
}
</style>