<template>
  <div class="w-full h-screen flex flex-col p-6 md:p-10 overflow-hidden bg-slate-50">
    
    <header class="flex items-end justify-between mb-8 shrink-0 px-2 relative">
      <div class="shrink-0">
        <p class="text-[10px] uppercase tracking-[0.3em] font-black text-blue-600 mb-2">
          {{ subtitle }}
        </p>
        <h2 class="text-3xl font-black tracking-tighter uppercase leading-none text-slate-900">
          {{ title }}
        </h2>
      </div>

      <nav class="hidden md:flex items-center gap-1.5 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm">
        <div 
          v-for="(step, index) in steps" :key="index"
          class="h-1.5 rounded-full transition-all duration-500"
          :class="[
            index === currentStep ? 'w-8 bg-blue-600' : 'w-2 bg-slate-200',
            index < currentStep ? 'bg-blue-300' : ''
          ]"
        ></div>
      </nav>
    </header>

    <div
      class="flex-grow flex flex-col lg:flex-row rounded-[2.5rem] border overflow-hidden transition-all duration-500 min-h-0 relative bg-white border-slate-100 shadow-2xl shadow-slate-200/50"
    >
      <main class="flex-grow flex flex-col min-w-0 bg-transparent relative overflow-hidden">

        <div class="absolute top-6 right-6 z-50 pointer-events-none">
          <div class="relative w-14 h-14 flex items-center justify-center">
            <svg class="w-full h-full transform -rotate-90">
              <circle
                cx="28" cy="28" r="24"
                stroke="currentColor" stroke-width="3" fill="transparent"
                class="text-slate-50"
              />
              <circle
                cx="28" cy="28" r="24"
                stroke="url(#kubix-gradient)" stroke-width="4" fill="transparent"
                stroke-dasharray="150.8"
                :stroke-dashoffset="dashOffset"
                stroke-linecap="round"
                class="transition-all duration-1000 cubic-bezier(0.4, 0, 0.2, 1)"
              />
              <defs>
                <linearGradient id="kubix-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#2563eb" />
                  <stop offset="100%" stop-color="#60a5fa" />
                </linearGradient>
              </defs>
            </svg>

            <span class="absolute text-[11px] font-black tracking-tighter text-slate-900">
              {{ Math.round(progressPercent) }}%
            </span>
          </div>
        </div>

        <div class="flex-grow overflow-y-auto p-8 md:p-16 custom-scrollbar">
          <transition name="wizard-slide" mode="out-in">
            <component
              :is="steps[currentStep].component"
              :key="currentStep"
              v-bind="getStepProps(currentStep)"
              @completed="handleNext"
              @prev="handlePrev"
            />
          </transition>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from "vue";

const props = defineProps({
  steps: { type: Array, required: true },
  title: { type: String, default: "Nova Operação" },
  subtitle: { type: String, default: "Implantação KUBIX" },
});

const emit = defineEmits(["finish"]);

// --- ESTADO ---
const currentStep = ref(0);
const maxStepsSeen = ref(0);

// --- LÓGICA DE PROGRESO INTELIGENTE ---
watch(
  () => props.steps.length,
  (newLength) => {
    if (newLength > maxStepsSeen.value) {
      maxStepsSeen.value = newLength;
    }
  },
  { immediate: true }
);

const progressPercent = computed(() => {
  const total = maxStepsSeen.value || 1;
  return ((currentStep.value + 1) / total) * 100;
});

const dashOffset = computed(() => {
  const perimeter = 150.8; // 2 * PI * r(24)
  return perimeter - (progressPercent.value / 100) * perimeter;
});

// --- HELPERS ---
const getStepProps = (stepIndex) => {
  const stepProps = { ...props.steps[stepIndex].props };
  // Limpiamos el callback para evitar warnings de mutación directa
  delete stepProps.onCompleted;
  return stepProps;
};

// --- NAVEGACIÓN ---
const handleNext = async (data) => {
  const currentStepConfig = props.steps[currentStep.value];
  const callback = currentStepConfig?.props?.onCompleted;

  // Ejecución del callback de negocio
  if (typeof callback === 'function') {
    callback(data);
  } else if (Array.isArray(callback) && typeof callback[0] === 'function') {
    callback[0](data);
  }

  // Esperamos al DOM (por si el callback añadió pasos dinámicos)
  await nextTick();

  if (currentStep.value < props.steps.length - 1) {
    currentStep.value++;
  } else {
    emit("finish", data);
  }
};

const handlePrev = () => {
  if (currentStep.value > 0) {
    currentStep.value--;
  }
};
</script>

<style scoped>
/* Scrollbar KUBIX Style */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

/* Transición Horizontal Premium */
.wizard-slide-enter-active,
.wizard-slide-leave-active {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.wizard-slide-enter-from {
  opacity: 0;
  transform: translateX(50px);
  filter: blur(10px);
}
.wizard-slide-leave-to {
  opacity: 0;
  transform: translateX(-50px);
  filter: blur(10px);
}
</style>