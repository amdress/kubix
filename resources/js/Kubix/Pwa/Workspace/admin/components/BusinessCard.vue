<template>
  <div
    v-if="business"
    class="group cursor-pointer rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition hover:shadow-lg active:scale-[0.98]"
  >
    <!-- IMAGE -->
    <div class="relative aspect-[4/3] bg-slate-100 dark:bg-slate-800">
      
      <img
        v-if="business?.cover"
        :src="business.cover"
        class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
      />

      <!-- fallback -->
      <div
        v-else
        class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-bold"
      >
        Sin imagen
      </div>

      <!-- overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

      <!-- name -->
      <div class="absolute bottom-2 left-2 right-2">
        <h2 class="text-white text-sm font-black leading-tight line-clamp-2">
          {{ business?.name || 'Sin nombre' }}
        </h2>
      </div>
    </div>

    <!-- INFO -->
    <div class="p-3 space-y-2">
      <p class="text-[11px] text-slate-400 font-semibold truncate uppercase tracking-wide">
        {{ business?.category || 'Sin categoría' }}
      </p>

      <div class="flex justify-between items-center text-[11px] font-bold text-slate-500">
        <span class="flex items-center gap-1">
          👁 {{ business?.stats?.views ?? 0 }}
        </span>

        <span class="flex items-center gap-1">
          🔗 {{ business?.stats?.clicks ?? 0 }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  business: {
    type: Object,
    required: true
  }
})
</script>