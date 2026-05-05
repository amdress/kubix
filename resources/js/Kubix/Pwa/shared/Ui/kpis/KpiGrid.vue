<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div v-for="kpi in data" :key="kpi.id" 
      class="p-5 rounded-3xl border flex flex-col justify-between h-40 transition-all duration-300 group hover:border-lime-400/40"
      :class="isDark ? 'bg-slate-900/40 border-slate-800' : 'bg-white border-slate-200 shadow-sm'"
    >
      <div class="flex justify-between items-start">
        <div class="flex flex-col">
          <span class="text-[9px] uppercase tracking-[0.2em] opacity-40 font-black italic">
            {{ kpi.title }}
          </span>
          <h2 class="text-2xl font-black italic mt-1 tracking-tight" :class="isDark ? 'text-white' : 'text-slate-900'">
            {{ kpi.value }}
          </h2>
        </div>
        
        <div 
          :class="kpi.trend >= 0 ? 'text-lime-400 bg-lime-400/10' : 'text-red-400 bg-red-400/10'" 
          class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-lg border border-current border-opacity-20"
        >
          {{ kpi.trend >= 0 ? '↑' : '↓' }}{{ Math.abs(kpi.trend) }}%
        </div>
      </div>
      
      <div class="mt-2">
        <KpiSparkline 
          :history="kpi.history || []" 
          :color="kpi.trend >= 0 ? '#a3e635' : '#f87171'" 
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import KpiSparkline from './KpiSparkline.vue';

defineProps({
  data: { type: Array, required: true },
  isDark: { type: Boolean, default: true }
});
</script>