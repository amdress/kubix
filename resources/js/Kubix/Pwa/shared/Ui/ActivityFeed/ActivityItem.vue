<template>
  <div class="flex gap-3 pb-4 border-b border-zinc-800 last:border-0 last:pb-0">
    <div 
      class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
      :class="iconBg"
    >
      <span class="text-lg">{{ icon }}</span>
    </div>

    <div class="flex-1 min-w-0">
      <p class="text-sm font-bold text-zinc-100 mb-1 truncate">
        {{ title }}
      </p>
      <p class="text-xs text-zinc-500 mb-2 line-clamp-2">
        {{ description }}
      </p>
      <span class="text-[10px] font-mono text-zinc-600 uppercase tracking-tighter">
        {{ time }}
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  title: String,
  description: String,
  time: String,
  iconBg: {
    type: String,
    default: 'bg-zinc-800'
  }
});
</script>