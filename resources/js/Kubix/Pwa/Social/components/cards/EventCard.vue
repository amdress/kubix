<template>
  <div 
    @click="$emit('select', data)"
    class="relative w-full transition-all duration-300 cursor-pointer active:scale-[0.99]"
  >
    
    <template v-if="data.type === 'LIVE'">
      <div class="h-[450px] rounded-[2rem] bg-slate-950 relative overflow-hidden group">
        <img :src="data.image" class="absolute inset-0 w-full h-full object-cover opacity-50 grayscale group-hover:grayscale-0 transition-all duration-700" />
        
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent p-8 flex flex-col justify-between">
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-xl">
              <div class="w-2 h-2 bg-red-600 rounded-full animate-pulse"></div>
              <span class="text-[10px] font-black text-slate-950 uppercase tracking-[0.2em]">Live Signal</span>
            </div>
            <div class="flex flex-col items-end">
               <span class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Viewers</span>
               <div class="bg-white/10 backdrop-blur-md px-3 py-1 rounded-lg border border-white/10 text-white font-mono text-xs">
                 {{ data.viewers || '1.2k' }}
               </div>
            </div>
          </div>

          <div class="space-y-6">
            <h3 class="text-4xl font-black text-white uppercase leading-[0.85] tracking-tighter">
              {{ data.title }}
            </h3>
            
            <div class="flex items-center justify-between border-t border-white/10 pt-6">
              <div class="flex items-center gap-3">
                <img :src="data.avatar" class="w-10 h-10 rounded-xl object-cover grayscale" />
                <div>
                  <p class="text-white text-[10px] font-black uppercase tracking-widest">{{ data.businessName }}</p>
                  <p class="text-white/40 text-[9px] font-bold uppercase tracking-tight">{{ data.distance }} • Terminal {{ data.id }}</p>
                </div>
              </div>
              <button class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-950 shadow-2xl">
                <PhBroadcast size="20" weight="bold" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template v-else-if="data.type === 'STANDARD'">
      <div class="bg-white border border-slate-200 rounded-[2rem] p-5 flex gap-6 hover:border-slate-400 transition-all shadow-sm">
        <div class="w-36 h-36 flex-none relative">
          <img :src="data.image" class="w-full h-full object-cover rounded-[1.5rem] grayscale" />
          <div class="absolute -top-2 -left-2 bg-slate-900 text-white px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-[0.2em]">
            {{ data.category || 'Event' }}
          </div>
        </div>
        
        <div class="flex flex-col justify-center flex-1">
          <div class="flex items-center gap-2 mb-2">
            <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded-md">
              {{ data.timeLabel }}
            </span>
          </div>
          <h3 class="text-xl font-black text-slate-900 uppercase leading-none tracking-tighter mb-4">
            {{ data.title }}
          </h3>
          <div class="flex items-center justify-between mt-auto">
            <div class="flex items-center gap-1 text-slate-400">
              <PhMapPin size="12" weight="bold" />
              <span class="text-[9px] font-bold uppercase tracking-widest">{{ data.place }}</span>
            </div>
            <PhArrowUpRight size="18" weight="bold" class="text-slate-300" />
          </div>
        </div>
      </div>
    </template>

    <template v-else-if="data.type === 'FLASH'">
      <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden border border-slate-800">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="relative z-10 flex flex-col items-start">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-8 h-[2px] bg-orange-500"></div>
            <span class="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em]">Flash Update</span>
          </div>
          
          <h3 class="text-2xl font-black uppercase tracking-tighter leading-[0.9] mb-4 max-w-[80%]">
            {{ data.title }}
          </h3>
          
          <div class="w-full flex items-end justify-between">
            <div class="space-y-1">
              <p class="text-[10px] font-black text-white/40 uppercase tracking-widest">Expiration</p>
              <p class="text-lg font-mono font-bold text-orange-400 leading-none">{{ data.timeLabel }}</p>
            </div>
            <button class="px-6 py-3 bg-white text-slate-950 rounded-xl text-[10px] font-black uppercase tracking-widest active:scale-95 transition-all">
              Claim Slot
            </button>
          </div>
        </div>
      </div>
    </template>

  </div>
</template>

<script setup>
import { 
  PhMapPin, PhBroadcast, PhArrowUpRight 
} from "@phosphor-icons/vue";

defineProps({
  data: { type: Object, required: true }
});

defineEmits(['select']);
</script>