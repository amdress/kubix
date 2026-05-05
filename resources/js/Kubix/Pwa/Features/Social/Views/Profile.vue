<template>
  <div class="w-full pb-20">
    <header class="flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-12 mb-10 px-2">
      <div class="relative">
        <div 
          class="p-1 rounded-full bg-gradient-to-tr from-yellow-400 to-fuchsia-600 shadow-lg"
          :class="[ui.isDark ? 'shadow-white/5' : 'shadow-black/10']"
        >
          <div class="bg-white dark:bg-slate-900 p-1 rounded-full">
            <img 
              :src="user?.avatar || 'https://ui-avatars.com/api/?name=' + user?.name" 
              class="w-24 h-24 md:w-36 md:h-36 rounded-full object-cover border-2 border-transparent"
              alt="Profile"
            />
          </div>
        </div>
        <button class="absolute bottom-2 right-2 bg-blue-500 text-white p-2 rounded-full shadow-lg hover:scale-110 transition-transform">
          <PhCamera :size="18" weight="bold" />
        </button>
      </div>

      <div class="flex-1 text-center md:text-left space-y-4">
        <div class="flex flex-col md:flex-row md:items-center gap-4">
          <h2 class="text-2xl font-black tracking-tight">{{ user?.name || 'Amdress Stark' }}</h2>
          <div class="flex gap-2 justify-center">
            <button class="px-6 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition-colors">
              Editar Perfil
            </button>
            <button class="p-1.5 bg-slate-200 dark:bg-slate-800 rounded-lg hover:opacity-80">
              <PhGear :size="20" />
            </button>
          </div>
        </div>

        <div class="flex justify-center md:justify-start gap-8 border-y md:border-none py-3 md:py-0 border-slate-200/10">
          <div class="flex flex-col md:flex-row md:gap-1 items-center">
            <span class="font-black text-lg">124</span>
            <span class="text-slate-500 text-xs md:text-sm uppercase font-bold tracking-widest">Publicaciones</span>
          </div>
          <div class="flex flex-col md:flex-row md:gap-1 items-center">
            <span class="font-black text-lg">1.2k</span>
            <span class="text-slate-500 text-xs md:text-sm uppercase font-bold tracking-widest">Seguidores</span>
          </div>
        </div>

        <div class="text-sm max-w-md mx-auto md:mx-0">
          <p class="font-bold mb-1">Systems Architect & Developer</p>
          <p class="text-slate-500 dark:text-slate-400 leading-relaxed">
            Building KUBIX 🚀 | Perfectionist by nature | {{ user?.email }}
          </p>
        </div>
      </div>
    </header>

    <div class="flex justify-center border-t border-slate-200/10 mb-6">
      <button 
        v-for="tab in ['Mural', 'Radar', 'Guardados']" 
        :key="tab"
        class="px-8 py-4 text-[10px] font-black uppercase tracking-[0.2em] transition-all border-t-2 -mt-[2px]"
        :class="activeTab === tab ? 'border-blue-500 text-blue-500' : 'border-transparent text-slate-500'"
        @click="activeTab = tab"
      >
        {{ tab }}
      </button>
    </div>

    <div class="grid grid-cols-3 gap-1 md:gap-4">
      <div 
        v-for="n in 9" :key="n"
        class="aspect-square bg-slate-200 dark:bg-slate-800 rounded-sm md:rounded-xl overflow-hidden group relative cursor-pointer"
      >
        <img 
          :src="`https://picsum.photos/500/500?random=${n}`" 
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
        />
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-4 text-white">
          <div class="flex items-center gap-1"><PhHeart weight="fill" /> 12</div>
          <div class="flex items-center gap-1"><PhChatCircle weight="fill" /> 3</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/Kubix/core/stores/auth"; // Ajusta a tu path real
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/useUIStore";
import { PhCamera, PhGear, PhHeart, PhChatCircle } from "@phosphor-icons/vue";

const auth = useAuthStore();
const ui = useUIStore();
const activeTab = ref('Mural');

// Obtenemos los datos del usuario logueado
const user = computed(() => auth.user);
</script>

<style scoped>
/* Evitar el color azul feo al hacer clic en móvil */
button {
  -webkit-tap-highlight-color: transparent;
}
</style>