<template>
  <div class="w-full h-screen flex flex-col py-0 px-10 overflow-hidden">
    <header class="flex items-center justify-between mb-4 shrink-0 px-2 relative">
      <div class="shrink-0">
        <h2
          class="text-2xl font-black tracking-tighter uppercase leading-none"
          :class="uiStore.isDark ? 'text-white' : 'text-slate-900'"
        >
          {{ title }}
        </h2>
        <p
          v-if="subtitle"
          class="text-[9px] uppercase tracking-widest opacity-40 font-bold mt-1"
        >
          {{ subtitle }}
        </p>
      </div>

      <nav class="hidden md:flex items-center gap-2">
        <div v-for="(step, index) in steps" :key="index" class="flex items-center gap-2">
          <div
            class="w-6 h-6 rounded-lg flex items-center justify-center border text-[8px] font-black transition-all"
            :style="getStepStyle(index)"
          >
            <i v-if="index < currentStep" class="fa-solid fa-check scale-75"></i>
            <span v-else>{{ index + 1 }}</span>
          </div>
          <div v-if="index < steps.length - 1" class="w-3 h-[1px] bg-slate-800/30"></div>
        </div>
      </nav>
    </header>

    <div
      class="flex-grow flex flex-col lg:flex-row rounded-[24px] border overflow-hidden transition-all duration-500 min-h-0 relative"
      :class="uiStore.isDark ? 'bg-slate-900/20 border-white/5' : 'bg-white border-slate-200 shadow-xl'"
    >
      <main class="flex-grow flex flex-col min-w-0 bg-transparent relative overflow-hidden">

        <!-- ===== CÍRCULO DE PROGRESO ===== -->
        <div class="absolute top-1 right-1 z-50 pointer-events-none">
          <div class="relative w-20 h-20 flex items-center justify-center">
            <svg
              class="w-full h-full transform -rotate-90 filter drop-shadow-[0_0_12px_rgba(163,230,53,0.4)]"
            >
              <circle
                cx="40"
                cy="40"
                r="34"
                stroke="currentColor"
                stroke-width="5"
                fill="transparent"
                :class="uiStore.isDark ? 'text-white/[0.03]' : 'text-slate-900/[0.04]'"
              />
              <circle
                cx="40"
                cy="40"
                r="34"
                stroke="#a3e635"
                stroke-width="5"
                fill="transparent"
                stroke-dasharray="213.6"
                :stroke-dashoffset="dashOffset"
                stroke-linecap="round"
                class="transition-all duration-700 cubic-bezier(0.4, 0, 0.2, 1)"
              />
            </svg>

            <span
              class="absolute text-[13px] font-black italic tracking-tighter"
              :class="uiStore.isDark ? 'text-white' : 'text-slate-800'"
            >
              {{ Math.round(progressPercent) }}%
            </span>
          </div>
        </div>

        <div class="flex-grow overflow-y-auto p-6 md:p-12 custom-scrollbar">
          <transition name="wizard-slide" mode="out-in">
            <component
              :is="steps[currentStep].component"
              :key="currentStep"
              v-bind="getStepProps(currentStep)"
              :is-dark="uiStore.isDark"
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
import { ref, computed, nextTick, watch } from "vue";
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/useUIStore";

const uiStore = useUIStore();

const props = defineProps({
  steps: { type: Array, required: true },
  title: { type: String, default: "Nova Filial" },
  subtitle: { type: String, default: "Operação de Implantação" },
});

const emit = defineEmits(["finish"]);

const currentStep = ref(0);
const maxStepsSeen = ref(0); // El máximo número de pasos que hemos visto

/**
 * ESTRATEGIA DE PROGRESO INTELIGENTE:
 * 
 * 1. Rastreamos el máximo de pasos que hemos visto hasta ahora
 * 2. El progreso se calcula sobre ese máximo
 * 3. Si se agregan más pasos, actualizamos el máximo
 * 4. El progreso nunca retrocede porque siempre dividimos por el máximo histórico
 * 
 * Ejemplo:
 * - Inicio: 2 pasos → maxStepsSeen = 2
 * - Paso 1: 1/2 = 50%
 * - Paso 2: 2/2 = 100%
 * - Se agregan 2 pasos más (total 4) → maxStepsSeen = 4
 * - Paso 3: 3/4 = 75% (ajustado proporcionalmente)
 * - Paso 4: 4/4 = 100%
 */

// Watch para actualizar el máximo cuando cambia el número de steps
watch(
  () => props.steps.length,
  (newLength) => {
    if (newLength > maxStepsSeen.value) {
      console.log('📈 [WizardBase] Nuevo máximo de pasos:', maxStepsSeen.value, '→', newLength);
      maxStepsSeen.value = newLength;
    }
  },
  { immediate: true } // Ejecutar inmediatamente para capturar el valor inicial
);

const progressPercent = computed(() => {
  // Usamos el máximo de pasos vistos para calcular el progreso
  const total = maxStepsSeen.value || 1; // Evitar división por 0
  const percent = ((currentStep.value + 1) / total) * 100;
  
  console.log('🔢 [Progress]:', {
    currentStep: currentStep.value + 1,
    maxStepsSeen: maxStepsSeen.value,
    currentStepsLength: props.steps.length,
    percent: Math.round(percent)
  });
  
  return percent;
});

const dashOffset = computed(() => {
  const perimeter = 213.6;
  return perimeter - (progressPercent.value / 100) * perimeter;
});

// Filtra las props del step para eliminar onCompleted y evitar el warning
const getStepProps = (stepIndex) => {
  const stepProps = { ...props.steps[stepIndex].props };
  delete stepProps.onCompleted;
  return stepProps;
};

const getStepStyle = (index) => {
  if (index === currentStep.value)
    return { backgroundColor: "#a3e635", borderColor: "#a3e635", color: "#000" };
  if (index < currentStep.value)
    return { backgroundColor: "transparent", borderColor: "#a3e635", color: "#a3e635" };
  return { backgroundColor: "transparent", borderColor: "rgba(255,255,255,0.1)", color: "#475569" };
};

const handleNext = async (data) => {
  console.log('🎯 [WizardBase] handleNext called with data:', data);
  console.log('📍 [WizardBase] Current step:', currentStep.value);
  console.log('📦 [WizardBase] Total steps BEFORE callback:', props.steps.length);
  
  const currentStepConfig = props.steps[currentStep.value];
  
  // Extraemos el callback con seguridad
  const callback = currentStepConfig?.props?.onCompleted;

  // Ejecutamos el callback PRIMERO (esto actualiza el estado)
  if (typeof callback === 'function') {
    console.log('✅ [WizardBase] Ejecutando callback onCompleted');
    callback(data);
  } else if (Array.isArray(callback)) {
    if (typeof callback[0] === 'function') {
      console.log('✅ [WizardBase] Ejecutando callback[0] onCompleted');
      callback[0](data);
    }
  }

  // Esperamos a que Vue procese las actualizaciones del estado
  await nextTick();
  
  console.log('⏭️ [WizardBase] Total steps AFTER callback:', props.steps.length);

  // AHORA avanzamos al siguiente paso
  if (currentStep.value < props.steps.length - 1) {
    console.log('➡️ [WizardBase] Avanzando al paso:', currentStep.value + 1);
    currentStep.value++;
  } else {
    console.log('🏁 [WizardBase] Último paso alcanzado, emitiendo finish');
    emit("finish", data);
  }
};

const handlePrev = () => {
  if (currentStep.value > 0) {
    console.log('⬅️ [WizardBase] Retrocediendo al paso:', currentStep.value - 1);
    currentStep.value--;
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(163, 230, 53, 0.2);
  border-radius: 10px;
}

.wizard-slide-enter-active,
.wizard-slide-leave-active {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.wizard-slide-enter-from {
  opacity: 0;
  transform: translateY(30px);
  filter: blur(10px);
}
.wizard-slide-leave-to {
  opacity: 0;
  transform: translateY(-30px);
  filter: blur(10px);
}
</style>