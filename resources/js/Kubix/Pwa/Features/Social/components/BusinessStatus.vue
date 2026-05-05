<template>
  <section 
    :class="[
      'top-0 z-20 py-4 border-b transition-all duration-300 backdrop-blur-md',
      ui.isDark 
        ? 'bg-slate-950/80 border-white/10' 
        : 'bg-white/80 border-slate-200/60'
    ]"
  >
    <div class="flex gap-5 overflow-x-auto px-5 scrollbar-hide items-start">
      
      <div 
        @click="$emit('create')"
        class="flex flex-col items-center gap-1.5 shrink-0 active:scale-90 transition-all cursor-pointer group"
      >
        <div class="relative w-16 h-16">
          <div 
            :class="[
              'w-full h-full rounded-full border-2 border-dashed flex items-center justify-center transition-colors',
              ui.isDark ? 'border-slate-700 bg-slate-900' : 'border-slate-300 bg-slate-50'
            ]"
          >
            <PhPlus :size="20" weight="bold" :class="ui.isDark ? 'text-slate-500' : 'text-slate-400'" />
          </div>
          <div class="absolute bottom-0 right-0 w-5 h-5 bg-blue-600 rounded-full border-2 flex items-center justify-center"
            :class="ui.isDark ? 'border-slate-950' : 'border-white'"
          >
            <PhPlus :size="10" weight="bold" class="text-white" />
          </div>
        </div>
        <span class="text-[10px] font-bold tracking-tighter opacity-60 uppercase">Explorar</span>
      </div>

      <div 
        v-for="item in items" 
        :key="item.id"
        @click="$emit('select', item)"
        class="flex flex-col items-center gap-1.5 shrink-0 active:scale-95 transition-all cursor-pointer relative group"
      >
        <div 
          class="relative w-16 h-16 rounded-full flex items-center justify-center transition-all duration-500"
          :class="[
            item.hasUpdate 
              ? 'bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-600 p-[2.5px]' 
              : ui.isDark ? 'bg-slate-800 p-[1px]' : 'bg-slate-200 p-[1px]'
          ]"
        >
          <div 
            :class="[
              'w-full h-full rounded-full p-[2px] overflow-hidden',
              ui.isDark ? 'bg-slate-950' : 'bg-white'
            ]"
          >
            <img 
              :src="item.logo || 'https://ui-avatars.com/api/?name=' + item.name" 
              :alt="item.name"
              class="w-full h-full object-cover rounded-full transition-transform duration-700 group-hover:scale-110" 
            />
          </div>

          <div 
            v-if="item.isActive"
            class="absolute -bottom-1 px-1.5 py-0.5 bg-red-600 rounded-md border-2 text-[8px] font-black text-white uppercase tracking-tighter"
            :class="ui.isDark ? 'border-slate-950' : 'border-white'"
          >
            LIVE
          </div>
        </div>

        <span 
          class="text-[10px] font-bold tracking-tight w-16 text-center truncate px-1"
          :class="[
            item.hasUpdate 
              ? (ui.isDark ? 'text-white' : 'text-slate-900') 
              : 'text-slate-500'
          ]"
        >
          {{ item.name }}
        </span>
      </div>
    </div>
  </section>
</template>

<script setup>
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/useUIStore";
import { PhPlus } from "@phosphor-icons/vue";

const ui = useUIStore();

defineProps({
  items: {
    type: Array,
    default: () => []
  }
});

defineEmits(['select', 'create']);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
  -webkit-overflow-scrolling: touch;
}

/* Animación sutil de pulso para cuando hay actualizaciones */
@keyframes border-pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}

.group:active .relative {
  animation: border-pulse 0.3s ease-out;
}
</style>