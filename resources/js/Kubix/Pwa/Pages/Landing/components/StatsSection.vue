<template>
  <section 
    ref="statsSection" 
    class="py-12 lg:py-24 px-4 lg:px-6 border-y border-white/5 relative transition-all duration-700"
    :class="[contextStore.device?.isMobile ? 'bg-transparent' : 'lg:bg-slate-900/60 lg:backdrop-blur-xl']"
  >
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-y-12 lg:gap-8">
        <div
          v-for="(stat, index) in stats"
          :key="stat.label"
          class="text-center group flex flex-col items-center"
        >
          <div 
            class="flex justify-center mb-4 lg:mb-6 p-3 rounded-2xl transition-all duration-500 bg-white/0 group-hover:bg-white/10"
          >
            <component 
              :is="stat.icon" 
              :size="contextStore.device?.isMobile ? '20' : '32'" 
              class="text-white opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500 drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]"
            />
          </div>
          
          <div class="text-4xl lg:text-6xl font-black text-white mb-1 lg:mb-2 tabular-nums tracking-tighter italic">
            {{ displays[index] }}
          </div>
          
          <p class="text-[9px] lg:text-[11px] text-slate-400 group-hover:text-white uppercase tracking-[0.25em] lg:tracking-[0.4em] font-black transition-colors duration-300">
            {{ stat.label }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, markRaw, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import { MARKETING_STATS } from '../config/marketingStats'
import {
  PhBuildings,
  PhMapPinArea,
  PhStar,
  PhUsersThree
} from '@phosphor-icons/vue'

const { t } = useI18n({ useScope: 'global' })
const contextStore = useContextStore()

// Mantenemos brandColor por si decides usarlo en el borde o acentos, 
// pero los iconos ahora son blancos por tu requerimiento.
const brandColor = computed(() => contextStore.currentBranding.primaryColor)
const statsSection = ref(null)
const hasAnimated = ref(false)

const stats = [
  { icon: markRaw(PhBuildings),  value: MARKETING_STATS.companies,     label: t('landing.stats.companies'),     suffix: '+' },
  { icon: markRaw(PhUsersThree), value: MARKETING_STATS.users,         label: t('landing.stats.users'),         suffix: '+' },
  { icon: markRaw(PhMapPinArea), value: MARKETING_STATS.neighborhoods, label: t('landing.stats.neighborhoods'), suffix: '+' },
  { icon: markRaw(PhStar),       value: MARKETING_STATS.satisfaction,  label: t('landing.stats.satisfaction'), suffix: '%' },
]

const displays = ref(stats.map(() => '0'))

const startCounterAnimation = () => {
  if (hasAnimated.value) return
  hasAnimated.value = true

  stats.forEach((stat, index) => {
    let start = 0
    const end = stat.value
    const duration = 2500 // Un poco más lento para mayor impacto visual
    const frameDuration = 1000 / 60
    const totalFrames = duration / frameDuration
    const increment = end / totalFrames

    const timer = setInterval(() => {
      start += increment
      if (start >= end) {
        start = end
        clearInterval(timer)
      }
      displays.value[index] = Math.floor(start).toLocaleString() + stat.suffix
    }, frameDuration)
  })
}

onMounted(() => {
  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      startCounterAnimation()
      observer.disconnect()
    }
  }, { threshold: 0.2 })

  if (statsSection.value) observer.observe(statsSection.value)
})
</script>

<style scoped>
.tabular-nums { font-variant-numeric: tabular-nums; }

/* Efecto sutil de cristal para Web */
@media (min-width: 1024px) {
  section {
    box-shadow: inset 0 1px 1px 0 rgba(255, 255, 255, 0.05);
  }
}
</style>