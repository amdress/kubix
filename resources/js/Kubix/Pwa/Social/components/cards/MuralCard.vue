<template>
  <div 
    class="mural-card bg-white border-b border-slate-50 last:border-0 py-8 px-6 transition-all"
    :class="[`type-${data.type?.toLowerCase()}`]"
  >
    <div 
      @click="$emit('open-business', data.businessId)" 
      class="flex items-center gap-3 mb-5 cursor-pointer group"
    >
      <div class="relative">
        <img :src="data.avatar" class="w-10 h-10 rounded-xl object-cover bg-slate-100 shadow-sm" />
        <div v-if="data.isOpen" class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
      </div>
      <div class="flex-1">
        <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-tight leading-none">
          {{ data.businessName }}
        </h4>
        <div class="flex items-center gap-2 mt-1">
          <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ data.timeAgo }}</span>
          <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
          <span class="text-[9px] font-bold text-blue-500 uppercase tracking-tighter">{{ data.distance }}</span>
        </div>
      </div>
      <PhCaretRight size="14" weight="bold" class="text-slate-200 group-hover:text-blue-500 transition-colors" />
    </div>

    <div @click="$emit('select', data)" class="cursor-pointer">
      
      <template v-if="data.type === 'PROMO'">
        <div class="relative rounded-[2.5rem] overflow-hidden mb-4 shadow-sm">
          <img :src="data.image" class="w-full h-64 object-cover" />
          <div class="absolute top-4 left-4 bg-yellow-400 text-black px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
            {{ data.badge || 'Oferta' }}
          </div>
        </div>
        <h3 class="text-xl font-black text-slate-900 leading-none tracking-tighter mb-2">{{ data.title }}</h3>
      </template>

      <template v-else-if="data.type === 'ALERTA'">
        <div class="bg-red-600 p-8 rounded-[2.5rem] mb-4 text-white">
          <PhWarningOctagon size="40" weight="fill" class="mb-4 opacity-80" />
          <h3 class="text-2xl font-black leading-[1.1] tracking-tighter uppercase italic">
            {{ data.title }}
          </h3>
        </div>
      </template>

      <template v-else-if="data.type === 'EVENTO'">
        <div class="flex gap-4 mb-4">
          <div class="flex-none w-16 h-20 bg-slate-900 rounded-2xl flex flex-col items-center justify-center text-white">
            <span class="text-[9px] font-black uppercase opacity-60">{{ data.eventMonth }}</span>
            <span class="text-2xl font-black leading-none">{{ data.eventDay }}</span>
          </div>
          <div class="flex-1 py-1">
            <h3 class="text-lg font-black text-slate-900 leading-tight">{{ data.title }}</h3>
            <p class="text-[10px] font-bold text-orange-500 uppercase mt-1">{{ data.eventTime }}</p>
          </div>
        </div>
        <img v-if="data.image" :src="data.image" class="w-full h-40 object-cover rounded-[2rem] mb-3" />
      </template>

      <template v-else>
        <div class="border-l-4 border-blue-500 pl-4 py-2 mb-2">
          <h3 class="text-lg font-bold text-slate-800 leading-tight">{{ data.title }}</h3>
        </div>
      </template>

      <p v-if="data.description" class="text-sm text-slate-500 font-medium leading-relaxed line-clamp-3">
        {{ data.description }}
      </p>
    </div>

    <button 
      @click="$emit('open-business', data.businessId)"
      class="mt-6 w-full h-14 bg-slate-50 hover:bg-slate-100 rounded-2xl flex items-center justify-center gap-3 transition-colors active:scale-[0.98]"
    >
      <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">
        {{ getActionLabel(data.type) }}
      </span>
      <PhArrowUpRight size="14" weight="bold" class="text-slate-400" />
    </button>
  </div>
</template>

<script setup>
import { 
  PhCaretRight, PhWarningOctagon, PhArrowUpRight 
} from "@phosphor-icons/vue";

const props = defineProps({
  data: { type: Object, required: true }
});

defineEmits(['select', 'open-business']);

const getActionLabel = (type) => {
  const labels = {
    PROMO: 'Ver Promoção',
    ALERTA: 'Saber Mais',
    EVENTO: 'Garantir Vaga',
    INFO: 'Ver Detalhes'
  };
  return labels[type] || 'Acessar';
};
</script>