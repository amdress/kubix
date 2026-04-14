<template>
  <header
    :class="[
      'h-16 px-4 flex items-center justify-between transition-all duration-300 backdrop-blur-md border-b',
      'sticky top-0 z-[100] w-full',
      ui.isDark ? 'bg-slate-900/90 border-white/10 text-white' : 'bg-white/90 border-slate-200 text-slate-900',
    ]"
  >
    <!-- btn toggle hamburger  -->
    <div class="flex-1 flex items-center">
      <button
        @click.stop="ui.toggleSidebar()"
        type="button"
        class="p-3 -ml-2 rounded-xl transition-all active:scale-90 hover:bg-blue-500/10 outline-none text-slate-500"
        aria-label="Toggle Menu"
      >
        <PhList :size="26" weight="bold" />
      </button>
    </div>

    <!-- <div 
      class="flex-[2] flex flex-col items-center justify-center cursor-pointer group py-1" 
      @click="$emit('change-location')"
    >
      <span class="text-[8px] font-black uppercase tracking-widest opacity-50 group-hover:text-blue-600 transition-colors">
        {{ workspace.isActive ? 'Contexto Empresarial' : 'Localização' }}
      </span>
      <div class="flex items-center gap-1">
        <h2 class="text-[11px] font-black truncate max-w-[120px] md:max-w-[140px]">
          {{ workspace.isActive ? workspace.current.label : 'Curitiba, Batel' }}
        </h2>
        <PhCaretDown :size="10" weight="bold" class="opacity-40" />
      </div>
    </div> -->

    <div class="flex-1 flex items-center gap-1 justify-end">

      <button class="p-2 rounded-xl hover:bg-slate-100 text-slate-500 relative transition-all active:scale-95">
        <PhBell :size="20" weight="bold" />
        <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-blue-500 border-2 border-white dark:border-slate-900"></span>
      </button>
    </div>

  </header>
</template>

<script setup>
import { useUIStore } from "../store/useUIStore";
import { useAuthStore } from "@/Kubix/core/stores/authStore";
import { useWorkSpaceStore } from "@/Kubix/core/stores/workspaceStore";
import { PhList, PhBell, PhCaretDown } from "@phosphor-icons/vue";

const ui = useUIStore();
const auth = useAuthStore();
const workspace = useWorkSpaceStore();

defineEmits(["change-location", "open-profile"]);
</script>

<style scoped>
header {
  /* Evita que el doble tap haga zoom en mobile y joda el click del hamburger */
  touch-action: manipulation;
  -webkit-user-select: none;
  user-select: none;
}
</style>