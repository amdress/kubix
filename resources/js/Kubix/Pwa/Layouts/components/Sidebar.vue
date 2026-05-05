<template>
  <div class="sidebar-root">
    <!-- OVERLAY MÓVIL -->
    <Transition name="fade">
      <div 
        v-if="!ui.sidebarCollapsed" 
        class="fixed inset-0 bg-black/40 z-[950] lg:hidden"
        @click="ui.sidebarCollapsed = true"
      ></div>
    </Transition>

    <aside
      :class="[
        'h-screen flex flex-col transition-all duration-300 border-r z-[1000]',
        'fixed lg:relative top-0 left-0', 
        ui.sidebarCollapsed ? '-translate-x-full lg:translate-x-0 lg:w-15' : 'translate-x-0 w-72',
        'bg-slate-950 border-slate-800 text-white'
      ]"
    >
      <!-- PROFILE -->
      <div class="px-3 py-3 flex-shrink-0">
        <ProfileCard :collapsed="ui.sidebarCollapsed" :user="currentUser" />
      </div>

      <!-- NAV -->
      <nav class="flex-1 overflow-y-auto px-2 space-y-1 custom-scroll">
        <router-link 
          v-for="item in menuItems" 
          :key="item.label" 
          :to="item.to"
          @click="closeOnMobile"
          class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sm"
          :class="[
            ui.sidebarCollapsed ? 'justify-center' : 'justify-start',
            $route.name === item.to.name 
              ? 'bg-cyan-600 text-white' 
              : 'text-slate-400 hover:text-white hover:bg-slate-800'
          ]"
        >
          <component 
            :is="item.icon" 
            :size="20" 
            :weight="$route.name === item.to.name ? 'fill' : 'bold'" 
          />
          <span v-if="!ui.sidebarCollapsed" class="truncate font-semibold text-xs uppercase tracking-wide">
            {{ item.label }}
          </span>
        </router-link>
      </nav>

      <!-- FOOTER -->
      <div class="px-2 py-3 border-t border-slate-800 flex-shrink-0">
        <button 
          @click="handleLogout" 
          class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-950/30 transition-all" 
          :class="ui.sidebarCollapsed ? 'justify-center' : 'justify-start'"
        >
          <PhSignOut :size="20" weight="bold" />
          <span v-if="!ui.sidebarCollapsed" class="text-xs font-semibold uppercase tracking-wide">Logout</span>
        </button>
      </div>
    </aside>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useUIStore } from '@/Kubix/Pwa/Layouts/store/uiStore'
import { useWorkSpaceStore } from "@/Kubix/core/stores/workspaceStore";
import { 
  PhHouse, PhSignOut, PhCompass, PhLayout, 
  PhSpeedometer, PhStorefront, PhGearSix, PhCaretRight, PhMonitor
} from "@phosphor-icons/vue";
import ProfileCard from "./ProfileCard.vue";

const ui = useUIStore();
const workspace = useWorkSpaceStore();

defineEmits(['logout']);

// Cierra el sidebar en móvil después de hacer click
const closeOnMobile = () => {
  if (window.innerWidth < 1024) {
    ui.sidebarCollapsed = true;
  }
};

const currentUser = {
  name: 'Amdress Stark',
  role: 'nomada',
  avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop'
};

const activeBranding = computed(() => {
  if (workspace.isActive) {
    return { gradient: workspace.branding.gradient };
  }
  return { gradient: "linear-gradient(135deg, #3b82f6, #06b6d4)" };
});

const menuItems = computed(() => {
  return [
    { label: "Mural", to: { name: "social.mural" }, icon: PhHouse },
    { label: "Radar", to: { name: "social.radar" }, icon: PhCompass },
    { label: "Negócios", to: { name: "workspace.admin.businessList" }, icon: PhStorefront },
    { label: "Dashboard", to: { name: "workspace.business.dashboard" }, icon: PhSpeedometer },
    { label: "Configurações", to: { name: "workspace.admin.businessConfig" }, icon: PhGearSix },
  ];
});
</script>