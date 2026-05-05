<template>
  <section class="relative">
    <div class="h-44 w-full overflow-hidden">
      <img
        :src="coverImage"
        class="h-full w-full object-cover scale-105"
        alt="Cover background"
      />
      <div
        class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"
      ></div>
    </div>

    <div class="absolute -bottom-12 left-6 right-6 flex items-end gap-4">
      <div
        class="h-28 w-28 rounded-2xl overflow-hidden border-[2px] shadow-2xl flex-shrink-0"
        :class="isDark ? 'border-slate-950 bg-slate-900' : 'border-slate-50 bg-white'"
      >
        <img :src="avatarImage" class="w-full h-full object-cover" :alt="title" />
      </div>

      <div class="pb-4 flex-1">
        <h1
          class="text-3xl font-black uppercase tracking-tighter leading-none text-white drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)] z-10"
        >
          {{ title }}
        </h1>
        
        <p
          v-if="subtitle"
          class="text-[10px] font-bold uppercase tracking-[0.15em] opacity-70 mt-1 text-black"
        >
          {{ subtitle }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  coverImage: { type: String, required: true },
  avatarImage: { type: String, required: true },
  title: { type: String, required: true },
  subtitle: { type: String, default: null }, 
  isDark: { type: Boolean, default: false }
});
</script>