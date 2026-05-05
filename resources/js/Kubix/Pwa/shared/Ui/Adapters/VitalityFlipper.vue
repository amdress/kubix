<template>
  <div class="vitality-container w-full h-full min-h-[180px]">
    <div 
      class="flipper-scene w-full h-full cursor-pointer" 
      @click="isFlipped = !isFlipped"
    >
      <div class="flipper-card" :class="{ 'is-flipped': isFlipped }">
        
        <!-- FRONT: GAUGE -->
        <div class="card-face face-front bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-2xl flex flex-col items-center justify-between">
          <header class="w-full">
            <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500 text-center">
              {{ label }}
            </h3>
          </header>
          
          <div class="flex-1 w-full flex items-center justify-center overflow-hidden py-2">
            <KubeGauge :value="gaugeData?.value || 0" class="w-full h-full max-h-[100px]" />
          </div>
          
          <footer class="w-full flex justify-center">
            <div class="text-[8px] font-bold text-cyan-500/40 tracking-[0.2em] animate-pulse uppercase">
              Live Data
            </div>
          </footer>
        </div>

        <!-- BACK: REVENUE -->
        <div class="card-face face-back bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-2xl flex flex-col justify-between">
          <header class="w-full">
            <h3 class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-500">
              Zone Revenue
            </h3>
          </header>

          <div class="flex-1 w-full overflow-hidden py-2">
            <RevenueCard v-bind="revenueData" class="w-full h-full" />
          </div>

          <footer class="w-full">
             <div class="text-[8px] font-bold text-slate-600 tracking-[0.2em] uppercase">
              Financial Status
            </div>
          </footer>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import KubeGauge from '../Vitality/KubeGauge.vue';
import RevenueCard from '../Vitality/RevenueCard.vue';

defineProps({
  label: { type: String, default: 'Vitality' },
  gaugeData: Object,
  revenueData: Object
});

const isFlipped = ref(false);
</script>

<style scoped>
/* 1. Contenedor principal que respeta el grid */
.vitality-container {
  perspective: 1500px;
}

/* 2. El objeto que rota */
.flipper-scene {
  position: relative;
  width: 100%;
  height: 100%;
}

.flipper-card {
  width: 100%;
  height: 100%;
  position: relative;
  transition: transform 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
  transform-style: preserve-3d;
}

/* 3. Estado rotado */
.is-flipped {
  transform: rotateY(180deg);
}

/* 4. Las caras (Forzamos visibilidad y brillo) */
.card-face {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  -webkit-backface-visibility: hidden;
  
  /* Esto elimina el efecto "opaco" al forzar renderizado limpio */
  transform: rotateX(0deg); 
  background-color: #0f172a; /* slate-950 sólido */
}

/* 5. Posicionamiento de caras */
.face-front {
  z-index: 2;
  /* Sin transformación adicional */
}

.face-back {
  transform: rotateY(180deg);
}
</style>