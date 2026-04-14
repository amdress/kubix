<template>
  <section class="py-20 px-6 relative overflow-hidden border-b border-white/5 bg-slate-950">
    <div class="max-w-7xl mx-auto">

      <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
        <div>
          <span 
            class="text-[10px] font-black uppercase tracking-[0.4em] mb-4 block"
            :style="{ color: brandColor }"
          >
            {{ t('landing.testimonials.tag') }}
          </span>
          <h2 class="text-5xl font-black text-white leading-none tracking-tighter">
            {{ t('landing.testimonials.title_1') }}<br />
            <span class="text-slate-600 font-thin italic">{{ t('landing.testimonials.title_2') }}</span>
          </h2>
        </div>

        <div class="flex gap-3">
          <div
            v-for="(_, i) in testimonials"
            :key="i"
            class="h-1 rounded-full transition-all duration-500 cursor-pointer"
            :class="currentSlide === i ? 'w-12' : 'w-4 bg-white/10'"
            :style="currentSlide === i ? { backgroundColor: brandColor } : {}"
            @click="goToSlide(i)"
          />
        </div>
      </div>

      <div
        ref="slider"
        class="flex gap-8 overflow-x-auto pb-12 cursor-grab active:cursor-grabbing no-scrollbar"
        style="scroll-snap-type: x mandatory; scroll-behavior: smooth;"
        @scroll="handleScroll"
        @mouseenter="stopAutoPlay"
        @mouseleave="startAutoPlay"
      >
        <div
          v-for="(test, i) in testimonials"
          :key="i"
          class="group shrink-0"
          style="scroll-snap-align: start; min-width: 480px;"
        >
          <div class="h-full p-10 rounded-[2.5rem] border border-white/5 bg-gradient-to-br from-white/[0.03] to-transparent backdrop-blur-2xl group-hover:border-white/10 transition-all duration-500 relative">
            
            <PhQuotes 
              size="32" 
              class="mb-8 opacity-20 transition-colors"
              :style="{ color: brandColor }" 
            />

            <p class="text-slate-300 leading-relaxed mb-10 font-light text-lg tracking-tight italic">
              "{{ t(`landing.testimonials.content.${i}.text`) }}"
            </p>

            <div class="flex items-center gap-4 pt-8 border-t border-white/5">
              <img
                :src="test.avatar"
                class="w-12 h-12 rounded-xl object-cover border border-white/10 grayscale group-hover:grayscale-0 transition-all duration-500 shadow-lg"
              />
              <div>
                <p class="text-sm font-black text-white uppercase tracking-wide">
                   {{ t(`landing.testimonials.content.${i}.author`) }}
                </p>
                <p 
                  class="text-[10px] uppercase tracking-[0.2em] font-bold"
                  :style="{ color: brandColor }"
                >
                  {{ t(`landing.testimonials.content.${i}.role`) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import { PhQuotes } from '@phosphor-icons/vue'

const { t, tm } = useI18n({ useScope: 'global' })
const contextStore = useContextStore()

// Brand Color dinámico
const brandColor = computed(() => contextStore.currentBranding?.primaryColor || '#3b82f6')

// Obtenemos el array de testimonios desde el i18n
const testimonials = computed(() => {
  // tm es 'translation message', devuelve el array crudo del JSON
  const content = tm('landing.testimonials.content')
  // Fallback con avatares si el array existe
  return Array.isArray(content) ? content.map((item, idx) => ({
    ...item,
    avatar: `https://i.pravatar.cc/150?u=${idx + 10}`
  })) : []
})

const slider = ref(null)
const currentSlide = ref(0)
const ITEM_WIDTH = 480
const GAP = 32
let interval = null

const goToSlide = (i) => {
  if (!slider.value) return
  slider.value.scrollTo({ left: i * (ITEM_WIDTH + GAP), behavior: 'smooth' })
  currentSlide.value = i
}

const handleScroll = () => {
  if (!slider.value) return
  currentSlide.value = Math.round(slider.value.scrollLeft / (ITEM_WIDTH + GAP))
}

const startAutoPlay = () => {
  if (testimonials.value.length <= 1) return
  interval = setInterval(() => {
    const next = (currentSlide.value + 1) % testimonials.value.length
    goToSlide(next)
  }, 5000)
}

const stopAutoPlay = () => clearInterval(interval)

onMounted(() => startAutoPlay())
onUnmounted(() => stopAutoPlay())
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>