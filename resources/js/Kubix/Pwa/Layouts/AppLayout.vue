<template>
  <div :class="['flex h-[100dvh] w-full overflow-hidden transition-colors duration-500', 
                  isDark ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-900']">
    
    <slot name="sidebar">
      <Sidebar />
    </slot>

    <div class="flex-1 flex flex-col min-w-0 h-full relative">
      
      <slot name="header">
        <Header
          v-if="$route.meta.showHeader !== false"
          :class="[
            'h-[64px] border-b backdrop-blur-md z-[80] w-full transition-colors duration-500',
            isDark ? 'bg-slate-900/80 border-slate-800' : 'bg-white/80 border-slate-200'
          ]"
        />
      </slot>

      <main id="main-content" class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 lg:pb-8 scrollbar-hide">
        <div class="max-w-6xl mx-auto w-full">
          <router-view v-slot="{ Component }">
            <transition name="page-fade" mode="out-in">
              <component :is="Component" :key="$route.fullPath" />
            </transition>
          </router-view>
        </div>
      </main>

      <slot name="tabs">
        <MobileTabs 
          v-if="$route.meta.showTabs !== false" 
          class="lg:hidden fixed bottom-0 left-0 right-0 z-[90]" 
        />
      </slot>
    </div>
  </div>
</template>

<script setup>
import Sidebar from "./components/Sidebar.vue";
import Header from "./components/Header.vue";
import MobileTabs from "./components/mobile/MobileTabs.vue";

/**
 * El Layout es un 'Dummy Component'. 
 * Recibe órdenes del orquestador superior.
 */
defineProps({
  isDark: {
    type: Boolean,
    default: false
  }
});
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

/* Transiciones suaves de PWA */
.page-fade-enter-active, 
.page-fade-leave-active { 
  transition: opacity 0.2s ease, transform 0.2s ease; 
}

.page-fade-enter-from { 
  opacity: 0; 
  transform: translateY(4px); /* Menos agresivo, más 'app native' */
}

.page-fade-leave-to { 
  opacity: 0; 
  transform: translateY(-4px); 
}
</style>