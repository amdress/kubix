<template>
  <nav
    :class="[
      'fixed bottom-0 left-0 right-0 h-[70px] px-2 pb-safe z-[90] border-t transition-all duration-300 shadow-[0_-10px_30px_rgba(0,0,0,0.15)]',
      ui.isDark ? 'bg-slate-900 border-white/10' : 'bg-white border-slate-200'
    ]"
  >
    <div class="flex items-center justify-between max-w-lg mx-auto h-full relative overflow-visible">
      
      <router-link :to="{ name: 'social.mural' }" class="nav-item">
        <div :class="['icon-wrapper', isActive('user.mural') ? 'active' : 'inactive']">
          <PhLayout :size="24" :weight="isActive('user.mural') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', { 'text-white': ui.isDark && isActive('user.mural'), 'text-blue-600': !ui.isDark && isActive('user.mural') }]">
          Mural
        </span>
      </router-link>

      <router-link :to="{ name: 'social.directory' }" class="nav-item">
        <div :class="['icon-wrapper', isActive('user.directory') ? 'active' : 'inactive']">
          <PhAddressBook :size="24" :weight="isActive('user.directory') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', { 'text-white': ui.isDark && isActive('user.directory') }]">
          Guia
        </span>
      </router-link>

      <!-- Camara principal  -->
      <div class="relative -translate-y-5">
        <button 
          @click="$emit('action:camera')"
          class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-2xl transition-all duration-300 active:scale-90 bg-blue-600 shadow-blue-600/40 text-white"
        >
          <PhCamera :size="32" weight="bold" />
        </button>
        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 label text-slate-500">
          Postar
        </span>
      </div>

      <router-link :to="{ name: 'social.events' }" class="nav-item">
        <div :class="['icon-wrapper', isActive('user.events') ? 'active' : 'inactive']">
          <PhCalendarStar :size="24" :weight="isActive('user.events') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', { 'text-white': ui.isDark && isActive('user.events') }]">
          Eventos
        </span>
      </router-link>

      <router-link :to="{ name: 'social.market' }" class="nav-item">
        <div :class="['icon-wrapper', isActive('user.market') ? 'active' : 'inactive']">
          <PhStorefront :size="24" :weight="isActive('user.market') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', { 'text-white': ui.isDark && isActive('user.market') }]">
          Market
        </span>
      </router-link>

    </div>
  </nav>
</template>

<script setup>
import { useRoute } from "vue-router";
import { useUIStore } from "@/Kubix/pwa/layouts/store/useUIStore"; 
import { 
  PhLayout, 
  PhAddressBook, 
  PhCamera, 
  PhCalendarStar, 
  PhStorefront 
} from "@phosphor-icons/vue";

const route = useRoute();
const ui = useUIStore();

defineEmits(['action:camera']);

const isActive = (name) => route.name === name;
</script>

<style scoped>
.pb-safe { padding-bottom: calc(env(safe-area-inset-bottom) + 0.5rem); }

.nav-item {
  @apply flex flex-col items-center justify-center gap-1 w-16 outline-none no-underline transition-all duration-200;
}

.icon-wrapper {
  @apply transition-all duration-300;
}

.icon-wrapper.active {
  @apply scale-110 text-blue-600;
}

.icon-wrapper.inactive {
  @apply opacity-60 text-slate-500;
}

.label {
  @apply text-[8px] font-black uppercase tracking-tighter text-slate-500;
}

nav { 
  transform: translateZ(0); 
  -webkit-transform: translateZ(0); 
}
</style>