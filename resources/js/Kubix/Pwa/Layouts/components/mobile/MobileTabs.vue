<template>
  <nav
    :class="[
      'fixed bottom-0 left-0 right-0 h-[72px] px-2 z-[90] border-t transition-all duration-300 pb-safe shadow-[0_-10px_30px_rgba(0,0,0,0.1)]',
      ui.isDark ? 'bg-slate-900 border-white/10' : 'bg-white border-slate-200'
    ]"
  >
    <div class="flex items-center justify-between max-w-lg mx-auto h-full relative px-2">
      
      <router-link :to="{ name: 'social.mural' }" class="nav-item group">
        <div :class="['icon-pill', isActive('social.mural') ? 'active' : 'inactive']">
          <PhLayout :size="24" :weight="isActive('social.mural') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', isActive('social.mural') ? 'text-blue-600' : 'opacity-40']">
          Mural
        </span>
      </router-link>

      <router-link :to="{ name: 'social.radar' }" class="nav-item group">
        <div :class="['icon-pill', isActive('social.radar') ? 'active' : 'inactive']">
          <PhCompass :size="24" :weight="isActive('social.radar') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', isActive('social.radar') ? 'text-blue-600' : 'opacity-40']">
          Radar
        </span>
      </router-link>

      <!-- camera action -->
      <div class="relative -translate-y-6">
        <button 
          @click="$emit('action:camera')"
          class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-2xl 
                 bg-blue-600 text-white transition-all duration-300 
                 active:scale-75 active:rotate-12 hover:shadow-blue-500/40 outline-none"
        >
          <PhCamera :size="28" weight="bold" />
        </button>
      </div>

      <router-link :to="{ name: 'social.events' }" class="nav-item group">
        <div :class="['icon-pill', isActive('social.events') ? 'active' : 'inactive']">
          <PhCalendarStar :size="24" :weight="isActive('social.events') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', isActive('social.events') ? 'text-blue-600' : 'opacity-40']">
          Eventos
        </span>
      </router-link>

      <router-link :to="{ name: 'social.profile' }" class="nav-item group">
        <div :class="['icon-pill', isActive('social.profile') ? 'active' : 'inactive']">
          <PhUser :size="24" :weight="isActive('social.profile') ? 'fill' : 'bold'" />
        </div>
        <span :class="['label', isActive('social.profile') ? 'text-blue-600' : 'opacity-40']">
          Perfil
        </span>
      </router-link>

    </div>
  </nav>
</template>

<script setup>
import { useRoute } from "vue-router";
import { useUIStore } from '@/Kubix/Pwa/Layouts/store/uiStore'
import { 
  PhLayout, 
  PhCompass,
  PhCamera, 
  PhCalendarStar, 
  PhUser,
} from "@phosphor-icons/vue";

const route = useRoute();
const ui = useUIStore();

defineEmits(['action:camera']);

/**
 * Lógica de activación:
 * Si la ruta es la raíz ('/') o el nombre coincide, Mural se activa por defecto.
 */
const isActive = (name) => {
  if (name === 'social.mural' && (route.name === name || route.path === '/')) {
    return true;
  }
  return route.name === name;
};
</script>

<style scoped>
.pb-safe { 
  padding-bottom: env(safe-area-inset-bottom); 
}

.nav-item {
  @apply flex flex-col items-center justify-center gap-1 w-16 outline-none no-underline transition-all duration-300;
}

.icon-pill {
  @apply w-12 h-8 rounded-full flex items-center justify-center transition-all duration-300;
}

.icon-pill.active {
  @apply bg-blue-600/10 text-blue-600 scale-110;
}

:deep(.dark) .icon-pill.active {
  @apply bg-blue-500/20 text-blue-400;
}

.icon-pill.inactive {
  @apply opacity-50 text-slate-500;
}

.label {
  @apply text-[9px] font-black uppercase tracking-tighter text-slate-500 transition-colors duration-300;
}

nav { 
  -webkit-tap-highlight-color: transparent;
  transform: translateZ(0); 
}
</style>