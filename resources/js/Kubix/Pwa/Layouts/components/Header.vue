<template>
  <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 px-4 lg:px-6 py-2 pb-8 border-b border-slate-800 sticky top-0 z-[100] bg-slate-950/95 backdrop-blur-md">
    <!-- LEFT -->
    <div class="flex items-center">
      <button
        @click.stop="ui.toggleSidebar()"
        type="button"
        class="p-2 rounded-xl transition-all active:scale-90 
               hover:bg-blue-500/10 outline-none 
               text-slate-500 dark:text-slate-400"
      >
        <PhList :size="26" weight="bold" />
      </button>
    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-2 ml-auto">
      <button
        class="p-2 rounded-xl transition-all active:scale-95
               hover:bg-slate-100 dark:hover:bg-white/10
               text-slate-500 dark:text-slate-400 relative"
      >
        <PhBell :size="20" weight="bold" />
        <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-blue-500 border-2 border-white dark:border-slate-900"></span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { useUIStore } from "../store/uiStore";
import { PhList, PhBell, PhCaretDown } from "@phosphor-icons/vue";

const ui = useUIStore();

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