<template>
  <div class="w-full border border-gray-100 rounded-xl p-4 bg-gray-50/50">
    
    <div v-if="title" class="mb-4">
      <h4 class="text-sm font-bold text-gray-700 uppercase tracking-tight">{{ title }}</h4>
      <p v-if="subtitle" class="text-xs text-gray-500">{{ subtitle }}</p>
    </div>

    <div class="flex gap-2 mb-6">
      <div 
        v-for="(step, index) in steps" 
        :key="index"
        class="h-1.5 flex-1 rounded-full transition-all duration-500"
        :style="{ 
          backgroundColor: index <= currentStep ? 'var(--primary-color)' : '#e5e7eb',
          opacity: index <= currentStep ? 1 : 0.5
        }"
      ></div>
    </div>

    <div class="py-2">
      <transition name="mini-fade" mode="out-in">
        <component
          :is="steps[currentStep].component"
          :key="currentStep"
          v-bind="steps[currentStep].props"
          @next="handleNext"
          @prev="handlePrev"
        />
      </transition>
    </div>

    <div class="mt-4 flex justify-center">
      <span class="text-[10px] font-medium text-gray-400 uppercase tracking-widest">
        Sub-paso {{ currentStep + 1 }} de {{ steps.length }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    steps: { type: Array, required: true },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
});

const emit = defineEmits(["finish"]);
const currentStep = ref(0);

const handleNext = (data) => {
    if (currentStep.value < props.steps.length - 1) {
        currentStep.value++;
    } else {
        emit("finish", data); // Avisa al Wizard padre que este bloque terminó
    }
};

const handlePrev = () => {
    if (currentStep.value > 0) currentStep.value--;
};
</script>

<style scoped>
.mini-fade-enter-active, .mini-fade-leave-active {
  transition: all 0.2s ease;
}
.mini-fade-enter-from { opacity: 0; transform: translateY(5px); }
.mini-fade-leave-to { opacity: 0; transform: translateY(-5px); }
</style>















 