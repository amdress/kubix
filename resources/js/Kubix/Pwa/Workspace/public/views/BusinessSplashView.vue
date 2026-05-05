<template>
  <transition name="splash-fade" appear>
    <div
      v-if="isVisible"
      class="fixed inset-0 z-[1000] flex items-center justify-center overflow-hidden"
    >
      <!-- 🖼️ BACKGROUND IMAGE -->
      <div class="absolute inset-0">
        <img
          :src="companyImage"
          alt="business"
          class="w-full h-full object-cover scale-105 blur-[2px]"
        />
        <div class="absolute inset-0 bg-black/60"></div>
      </div>

      <!-- 🧠 CONTENT -->
      <div class="relative z-10 flex flex-col items-center text-center px-6 animate-block-in">

        <!-- LOGO / FALLBACK -->
        <div
          class="h-28 w-28 rounded-2xl overflow-hidden border border-white/10 shadow-xl mb-6"
        >
          <img
            v-if="companyLogo"
            :src="companyLogo"
            class="w-full h-full object-cover"
          />
          <div
            v-else
            class="flex items-center justify-center w-full h-full bg-white/10"
          >
            <PhStorefront size="48" class="text-white/80" />
          </div>
        </div>

        <!-- TEXT -->
        <p class="text-[10px] uppercase tracking-[0.3em] text-white/50 mb-2">
          Bem-vindo a
        </p>

        <h1 class="text-4xl sm:text-5xl font-black text-white tracking-tight">
          {{ companyName }}
        </h1>

        <div class="mt-3 flex items-center gap-2 text-white/70">
          <PhMapPin size="14" />
          <span class="text-xs uppercase tracking-wider">
            {{ companyNeighborhood }}
          </span>
        </div>
      </div>

      <!-- ⏳ LOADING -->
      <div class="absolute bottom-10 w-full flex flex-col items-center gap-3">
        <div class="h-[3px] w-24 bg-white/20 overflow-hidden rounded-full">
          <div class="h-full bg-white animate-loading-bar"></div>
        </div>

        <span class="text-[10px] uppercase tracking-widest text-white/40">
          Carregando...
        </span>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { PhStorefront, PhMapPin } from "@phosphor-icons/vue"

const isVisible = ref(true)
const router = useRouter()


defineProps({
  companyName: {
    type: String,
    default: "Mercado do Juan"
  },
  companyNeighborhood: {
    type: String,
    default: "Capão Raso"
  },
  companyImage: {
    type: String,
    default: "https://images.unsplash.com/photo-1600891964599-f61ba0e24092"
  },
  companyLogo: {
    type: String,
    default: null
  }
})

// router.replace({
//   name: 'workspace.public.landing',
//   params: { businessId: id }
// })

const SPLASH_DURATION = 5000

onMounted(() => {
  setTimeout(() => {
    isVisible.value = false

    router.push({ name: 'workspace.public.businessProfile' })
    
  }, SPLASH_DURATION)
})
</script>

<style scoped>
.splash-fade-leave-active {
  transition: opacity 0.4s ease, transform 0.4s ease;
}
.splash-fade-leave-to {
  opacity: 0;
  transform: scale(1.03);
}

@keyframes blockIn {
  0% { opacity: 0; transform: translateY(25px); }
  100% { opacity: 1; transform: translateY(0); }
}
.animate-block-in {
  animation: blockIn 0.6s ease-out forwards;
}

@keyframes loadingBar {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
.animate-loading-bar {
  animation: loadingBar 1.2s linear infinite;
}
</style>