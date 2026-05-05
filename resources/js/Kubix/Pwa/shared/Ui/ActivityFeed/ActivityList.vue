<template>
  <div class="bg-zinc-900/50 backdrop-blur border border-zinc-800 rounded-2xl p-6">
    <h3 class="text-lg font-black tracking-tight mb-6">
      {{ title }}
    </h3>

    <div class="space-y-4">
      <ActivityItem 
        v-for="activity in items" 
        :key="activity.id" 
        v-bind="activity" 
      />
    </div>
    
    <slot name="footer"></slot>
  </div>
</template>

<script setup>
import ActivityItem from './ActivityItem.vue';

defineProps({
  title: {
    type: String,
    default: 'Recent Activity'
  },
  items: {
    type: Array,
    required: true
  }
});
</script>