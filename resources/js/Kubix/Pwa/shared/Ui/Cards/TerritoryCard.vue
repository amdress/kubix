<template>
  <div class="group relative bg-zinc-900/40 border border-zinc-800 hover:border-cyan-500/50 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-500/10">
    
    <div class="relative h-32 overflow-hidden">
      <img :src="heroImage" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100" />
      <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>
      
      <div class="absolute top-3 left-3 px-2 py-1 bg-zinc-950/80 backdrop-blur-md border border-zinc-700 rounded text-[10px] font-black uppercase tracking-widest text-zinc-400">
        {{ level }}
      </div>
    </div>

    <div class="absolute top-20 left-6">
      <div class="relative">
        <img :src="manager.avatar" class="w-14 h-14 rounded-xl border-2 border-zinc-900 object-cover shadow-xl" />
        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-zinc-900 rounded-full"></div>
      </div>
    </div>

    <div class="p-6 pt-8">
      <div class="mb-4">
        <h3 class="text-xl font-black tracking-tighter text-zinc-100 group-hover:text-cyan-400 transition-colors">
          {{ name }}
        </h3>
        <p class="text-xs font-mono text-zinc-500">{{ manager.name }} <span class="text-zinc-700">/ Manager</span></p>
      </div>

      <div class="grid grid-cols-2 gap-4 py-4 border-y border-zinc-800/50 mb-4">
        <div>
          <p class="text-[10px] font-mono text-zinc-500 uppercase">Companies</p>
          <p class="text-lg font-black text-zinc-200">{{ stats.companies }}</p>
        </div>
        <div>
          <p class="text-[10px] font-mono text-zinc-500 uppercase">Status</p>
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
            <p class="text-xs font-bold text-zinc-300 uppercase">{{ stats.status }}</p>
          </div>
        </div>
      </div>

      <button @click="$emit('manage', id)" 
              class="w-full py-3 bg-zinc-800 hover:bg-cyan-500 hover:text-zinc-950 text-zinc-400 text-xs font-black uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 group/btn">
        Manage Territory
        <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  id: [String, Number],
  level: String, // 'Neighborhood', 'City', 'State', 'Country'
  name: String,
  heroImage: String,
  manager: Object, // { name: string, avatar: string }
  stats: Object,  // { companies: number, status: string }
  status: bolean
});

defineEmits(['manage']);
</script>