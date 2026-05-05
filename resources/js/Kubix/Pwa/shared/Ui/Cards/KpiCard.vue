<template>
  <div 
    class="group relative bg-slate-900 border border-slate-800 rounded-lg p-4 hover:border-slate-700 transition-all duration-300 overflow-hidden"
  >
    <!-- GRADIENT BACKGROUND -->
    <div 
      class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br opacity-0 group-hover:opacity-20 transition-opacity duration-500 blur-2xl" 
      :class="gradientColor"
    ></div>
    
    <div class="relative">
      <!-- HEADER: ICON + TREND -->
      <div class="flex items-start justify-between mb-4">
        <!-- ICON -->
        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="iconBg">
          <component 
            :is="icon" 
            :size="20" 
            weight="bold"
            class="text-white"
          />
        </div>
        
        <!-- TREND BADGE -->
        <div class="flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold" :class="trendBg">
          <component 
            :is="trendDirection === 'up' ? PhArrowUp : PhArrowDown"
            :size="12"
            weight="bold"
          />
          <span>{{ trend }}</span>
        </div>
      </div>
      
      <!-- CONTENT -->
      <div class="space-y-2">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ label }}</p>
        <p class="text-3xl font-black tracking-tight text-white">{{ value }}</p>
        <p class="text-xs text-slate-500">{{ subtitle }}</p>
      </div>

      <!-- PROGRESS BAR -->
      <div class="mt-4 h-1.5 bg-slate-800 rounded-full overflow-hidden">
        <div 
          class="h-full rounded-full transition-all duration-1000" 
          :class="progressBg"
          :style="{ width: progress + '%' }"
        ></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PhArrowUp, PhArrowDown } from "@phosphor-icons/vue";

defineProps({
  icon: {
    type: [String, Object],
    required: true
  },
  label: String,
  value: [String, Number],
  subtitle: String,
  trend: String,
  trendDirection: {
    type: String,
    default: 'up'
  },
  trendBg: String,
  iconBg: String,
  gradientColor: String,
  progress: Number,
  progressBg: String
});
</script>