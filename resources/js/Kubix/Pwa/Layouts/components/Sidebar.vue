<template>
  <div class="sidebar-root">

    <Transition name="fade">
      <div 
        v-if="!ui.sidebarCollapsed" 
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[950] lg:hidden"
        @click="ui.sidebarCollapsed = true"
      ></div>
    </Transition>

    <aside
      :class="[
        'h-screen flex flex-col transition-all duration-300 border-r z-[1000]',
        // WEB: 'relative' empuja al Header. MOBILE: 'fixed' flota.
        'fixed lg:relative top-0 left-0', 
        ui.sidebarCollapsed ? '-translate-x-full lg:translate-x-0 lg:w-[80px]' : 'translate-x-0 w-[280px]',
        ui.isDark ? 'bg-slate-900 border-slate-800 text-white' : 'bg-white border-slate-100 text-slate-900'
      ]"
    >
    
      <div class="p-6 border-b flex flex-col items-center" :class="ui.isDark ? 'border-slate-800' : 'border-slate-50'">
        <div 
          class="relative rounded-2xl p-[2px] transition-all duration-500 shadow-xl"
          :style="{ background: activeBranding.gradient }"
          :class="ui.sidebarCollapsed ? 'w-10 h-10' : 'w-20 h-20'"
        >
          <div :class="['w-full h-full rounded-[calc(1rem-2px)] p-1', ui.isDark ? 'bg-slate-900' : 'bg-white']">
            <img :src="auth.user?.avatar" class="w-full h-full rounded-lg object-cover" />
          </div>
          <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg border-2 border-white flex items-center justify-center text-white" :style="{ backgroundColor: activeBranding.primary }">
            <component :is="activeBranding.icon" :size="12" weight="bold" />
          </div>
        </div>

        <div v-if="!ui.sidebarCollapsed" class="mt-4 w-full text-center">
          <h2 class="text-sm font-black truncate tracking-tight">{{ auth.user?.name }} Amdress</h2>
          <div class="mt-3 px-2">
            <select 
              @change="(e) => handleSwitch(e.target.value)"
              class="w-full text-[10px] font-black uppercase py-2 bg-slate-100 rounded-xl border-none text-slate-500 text-center cursor-pointer appearance-none hover:bg-slate-200 transition-all"
            >
              <option v-for="ctx in auth.availableContexts" :key="ctx.id" :value="ctx.id" :selected="currentId === ctx.id">
                {{ ctx.label }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <nav class="flex-1 overflow-y-auto p-4 space-y-2 custom-scroll">
        <router-link 
          v-for="item in menuItems" :key="item.label" :to="item.to"
          class="flex items-center gap-4 p-3 rounded-xl transition-all font-bold text-sm"
          :class="[
            ui.sidebarCollapsed ? 'justify-center' : 'justify-start',
            $route.name === item.to.name ? 'bg-blue-500/10 text-blue-600' : 'text-slate-400 hover:bg-slate-50'
          ]"
        >
          <component :is="item.icon" :size="24" :weight="$route.name === item.to.name ? 'fill' : 'bold'" />
          <span v-if="!ui.sidebarCollapsed" class="truncate">{{ item.label }}</span>
        </router-link>
      </nav>

      <div class="p-4 border-t" :class="ui.isDark ? 'border-slate-800' : 'border-slate-50'">
        <button @click="$emit('logout')" class="w-full flex items-center gap-4 p-3 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all" :class="ui.sidebarCollapsed ? 'justify-center' : 'justify-start'">
          <PhSignOut :size="22" weight="bold" />
          <span v-if="!ui.sidebarCollapsed" class="text-[10px] font-black uppercase tracking-widest">Sair</span>
        </button>
      </div>

    </aside>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useUIStore } from "../store/useUIStore";
import { useAuthStore } from "@/Kubix/core/stores/authStore";
import { useWorkSpaceStore } from "@/Kubix/core/stores/workspaceStore";
import { useContextStore } from "@/Kubix/core/stores/contextStore";
import { PhHouse, PhUser, PhSignOut, PhMegaphone, PhCompass, PhLayout, PhHouseLine, PhBriefcase } from "@phosphor-icons/vue";

const ui = useUIStore();
const auth = useAuthStore();
const workspace = useWorkSpaceStore();
const context = useContextStore();

defineEmits(['logout']);

const currentId = computed(() => workspace.isActive ? workspace.current.id : 'user_0');

const activeBranding = computed(() => {
  if (workspace.isActive) {
    return { gradient: workspace.branding.gradient, primary: workspace.branding.primary, icon: PhBriefcase };
  }
  return { gradient: "linear-gradient(135deg, #3b82f6, #06b6d4)", primary: "#3b82f6", icon: PhUser };
});

const handleSwitch = (id) => {
  const selected = auth.availableContexts.find(c => c.id === id);
  if (selected) {
    workspace.setWorkspace(selected);
    context.setMode(selected.type);
    if (window.innerWidth < 1024) ui.sidebarCollapsed = true;
  }
};

const menuItems = computed(() => {


  // if (workspace.isActive) {
  //   return [
  //     { label: "Dashboard", to: { name: "business.dashboard" }, icon: PhLayout },
  //     ...workspace.solutions.map(s => ({
  //       label: s.name, to: { path: s.path }, icon: s.slug === 'alugapp' ? PhHouseLine : PhMegaphone
  //     }))
  //   ];
  // }


  return [
    { label: "Mural", to: { name: "social.mural" }, icon: PhHouse },
    // { label: "Radar", to: { name: "social.radar" }, icon: PhCompass },
    // { label: "Meu Perfil", to: { name: "social.profile" }, icon: PhUser },
  ];
});
</script>