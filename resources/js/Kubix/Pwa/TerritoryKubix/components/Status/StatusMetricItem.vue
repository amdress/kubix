<template>
  <div>
    <div class="flex justify-between items-center mb-2">
      <span class="text-xs font-mono text-zinc-400 uppercase tracking-tighter">
        {{ label }}
      </span>
      <span class="text-sm font-black text-zinc-100">
        {{ value }}
      </span>
    </div>
    <div class="h-2 bg-zinc-900/50 rounded-full overflow-hidden">
      <div
        class="h-full rounded-full transition-all duration-1000"
        :class="barColor"
        :style="{ width: percentage + '%' }"
      ></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: String,
  percentage: Number,
  barColor: String
});
</script>