<template>
  <div class="group relative bg-slate-900 border border-slate-800 rounded-lg p-4 hover:border-slate-700 transition-all duration-300 overflow-hidden">
    <!-- GRADIENT BACKGROUND (Polimórfico) -->
    <div 
      class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br opacity-0 group-hover:opacity-20 transition-opacity duration-500 blur-2xl" 
      :class="gradientColor"
    ></div>
    
    <div class="relative">
      <!-- HEADER -->
      <div class="flex items-start justify-between mb-4">
        <!-- ICON UNIT -->
        <div class="relative w-10 h-10 flex-shrink-0">
          <div v-if="variant === 'pulse'" class="absolute inset-0 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 animate-pulse opacity-50"></div>
          <div class="absolute inset-0 rounded-lg flex items-center justify-center" :class="iconBg">
            <component :is="icon" :size="20" weight="bold" class="text-white" />
          </div>
        </div>
        
        <!-- ACCESSORY: Tendencia O Pulso -->
        <div v-if="variant === 'trend'" class="flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold" :class="trendBg">
          <component :is="trendDirection === 'up' ? PhArrowUp : PhArrowDown" :size="12" weight="bold" />
          <span>{{ trendText }}</span>
        </div>

        <div v-else-if="variant === 'pulse'" class="flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400">
          <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          <span>ONLINE</span>
        </div>
      </div>
      
      <!-- CONTENT (Con tu animación de números) -->
      <div class="space-y-2">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ label }}</p>
        <p class="text-3xl font-black tracking-tight text-white">
          {{ displayValue }}
        </p>
        <p class="text-xs text-slate-500">{{ subtitle }}</p>
      </div>

      <!-- PROGRESS BAR -->
      <div class="mt-4 h-1.5 bg-slate-800 rounded-full overflow-hidden">
        <div 
          class="h-full rounded-full transition-all duration-1000" 
          :class="progressBg"
          :style="{ width: progressPercentage + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { PhArrowUp, PhArrowDown } from "@phosphor-icons/vue";

const props = defineProps({
  variant: { type: String, default: 'trend' }, // 'trend' | 'pulse'
  icon: [Object, String],
  label: String,
  value: { type: Number, default: 0 },
  subtitle: String,
  // Estilos
  iconBg: { type: String, default: 'bg-slate-800' },
  gradientColor: { type: String, default: 'from-blue-500 to-cyan-500' },
  progressBg: { type: String, default: 'bg-blue-500' },
  // Data específica de Trend
  trendText: String,
  trendDirection: { type: String, default: 'up' },
  trendBg: { type: String, default: 'bg-slate-800 text-slate-400' },
  // Data específica de Progreso
  progressValue: Number, // Si no se pasa, usa 'value'
  maxCapacity: { type: Number, default: 100 }
});

// Lógica de animación de números que ya tenías
const displayValue = ref(0);
const animate = (target) => {
  const start = displayValue.value;
  const duration = 1000;
  const startTime = performance.now();

  const step = (now) => {
    const progress = Math.min((now - startTime) / duration, 1);
    displayValue.value = Math.floor(progress * (target - start) + start);
    if (progress < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
};

watch(() => props.value, (newVal) => animate(newVal));
onMounted(() => animate(props.value));

// Lógica de barra de progreso
const progressPercentage = computed(() => {
  const current = props.progressValue !== undefined ? props.progressValue : props.value;
  return Math.min((current / props.maxCapacity) * 100, 100);
});
</script>