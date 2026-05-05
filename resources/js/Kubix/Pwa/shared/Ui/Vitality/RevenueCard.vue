<template>
  <div class="bg-slate-900 border border-slate-800 rounded-lg p-4 flex flex-col justify-between h-full group hover:border-slate-700 transition-colors duration-300">
    
    <div class="flex flex-col gap-1">
      <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">
        {{ label || 'Revenue' }}
      </p>
      
      <div class="flex items-baseline gap-2">
        <h2 class="text-2xl font-black text-white leading-none">
          ${{ value.toLocaleString() }}
        </h2>
        <span 
          class="text-[10px] font-bold"
          :class="trend >= 0 ? 'text-emerald-400' : 'text-red-400'"
        >
          {{ trend >= 0 ? '↑' : '↓' }} {{ Math.abs(trend) }}%
        </span>
      </div>
    </div>

    <!-- SPARKLINE: Lo llamamos como un componente tonto -->
    <div class="h-16 w-full mt-4">
      <Sparkline 
        :data="series" 
        :color="trend >= 0 ? '#10b981' : '#f43f5e'" 
      />
    </div>

  </div>
</template>

<script setup>
import Sparkline from './KpiSparkline.vue';

defineProps({
  label: String,
  value: { type: Number, default: 0 },
  trend: { type: Number, default: 0 },
  series: { type: Array, default: () => [] }
});
</script>