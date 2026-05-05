<template>
  <div
    class="bg-gradient-to-br from-cyan-500/10 via-violet-500/10 to-fuchsia-500/10 backdrop-blur border border-cyan-500/20 rounded-2xl p-6"
  >
    <div class="flex items-center gap-3 mb-6">
      <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center">
        <slot name="icon">
          <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </slot>
      </div>
      <h3 class="text-lg font-black tracking-tight">
        {{ title }}
      </h3>
    </div>

    <div class="space-y-4">
      <StatusMetricItem 
        v-for="metric in metrics" 
        :key="metric.label" 
        v-bind="metric" 
      />
    </div>
  </div>
</template>

<script setup>
import StatusMetricItem from './StatusMetricItem.vue';

defineProps({
  title: {
    type: String,
    default: 'Branch Health'
  },
  metrics: {
    type: Array,
    required: true
  }
});
</script>