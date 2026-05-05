<template>
  <div class="fixed inset-0 z-[200] bg-slate-950 overflow-hidden font-sans">
    
    <div class="absolute inset-0 transition-all duration-1000 ease-in-out">
      <img
        :src="slides[current].image"
        class="w-full h-full object-cover grayscale opacity-40 scale-110"
        :key="current"
      />
      <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
    </div>

    <div class="relative z-10 flex flex-col h-full">
      
      <div class="p-8 flex items-center justify-between">
        <div class="flex gap-1.5">
          <div 
            v-for="(_, i) in slides" :key="i"
            class="h-1 rounded-full transition-all duration-500"
            :class="i <= current ? 'bg-blue-500 w-8' : 'bg-white/10 w-4'"
          ></div>
        </div>
        <span class="font-mono text-[10px] text-white/30 tracking-[0.3em] uppercase">
          Step {{ current + 1 }} // {{ slides.length }}
        </span>
      </div>

      <div class="flex-1 flex flex-col justify-end px-8 pb-12">
        <transition name="slide-fade" mode="out-in">
          <div :key="current" class="max-w-xl space-y-6">
            <div>
              <span class="text-blue-500 font-mono text-[10px] font-black uppercase tracking-[0.4em] mb-2 block">
                {{ slides[current].category }}
              </span>
              <h2 class="text-5xl md:text-6xl font-black text-white leading-[0.85] tracking-tighter uppercase">
                {{ slides[current].title }}
              </h2>
            </div>

            <p class="text-lg text-slate-400 font-medium leading-tight max-w-[280px]">
              {{ slides[current].text }}
            </p>
          </div>
        </transition>
      </div>

      <div class="p-8 bg-slate-950/50 backdrop-blur-xl border-t border-white/5">
        <div class="flex flex-col gap-4">
          <button
            @click="handleNext"
            class="group relative w-full py-6 bg-white overflow-hidden rounded-2xl transition-all active:scale-[0.98]"
          >
            <div class="relative z-10 flex items-center justify-center gap-3">
              <span class="text-slate-950 font-black uppercase tracking-[0.2em] text-xs">
                {{ isLast ? 'Começar' : 'Siguiente Fase' }}
              </span>
              <PhArrowRight size="18" weight="bold" class="text-slate-950 group-hover:translate-x-1 transition-transform" />
            </div>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { PhArrowRight } from "@phosphor-icons/vue"

const router = useRouter()
const current = ref(0)
let interval = null

const slides = [
  {
    category: 'Analytics',
    title: 'Métrica en Tiempo Real',
    text: 'Visualiza el impacto de tu marca con precisión quirúrgica en el mapa local.',
    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1000'
  },
  {
    category: 'Logistics',
    title: 'Conexión Directa B2B',
    text: 'Elimina intermediarios. Tus servicios, tus reglas, tu ecosistema.',
    image: 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1000'
  },
  {
    category: 'Billing',
    title: 'Eficiencia Operativa',
    text: 'Arquitectura escalable sin costos ocultos. Paga por despliegue activo.',
    image: 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1000'
  }
]

const isLast = computed(() => current.value === slides.length - 1)

const handleNext = () => {
  if (!isLast.value) current.value++
  else goNext()
}

const skip = () => goNext()

const goNext = () => {
  if (interval) clearInterval(interval)
  router.push({ name: 'workspace.admin.businessCreate' })
}

onMounted(() => {
  interval = setInterval(() => {
    if (current.value < slides.length - 1) current.value++
  }, 5000)
})

onUnmounted(() => { if (interval) clearInterval(interval) })
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.4s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>