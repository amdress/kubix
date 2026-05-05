<template>
  <div class="flex items-center gap-3 w-full py-4 relative z-50" @click.stop>
    
    <!-- ICONO INDICADOR ESTRUCTURA -->
    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-900 border border-slate-800 text-cyan-500 shadow-[inset_0_0_10px_rgba(6,182,212,0.1)]">
      <PhTreeStructure :size="20" weight="duotone" />
    </div>

    <!-- NAV DINÁMICO: EFECTO TÚNEL -->
    <nav class="flex items-center gap-1.5 p-1.5 bg-slate-950/50 backdrop-blur-md border border-slate-800/50 rounded-2xl shadow-2xl overflow-hidden">
      
      <TransitionGroup name="tunnel">
        <template v-for="level in 5" :key="level">
          
          <!-- Lógica de visualización: primer nivel o si el anterior tiene selección -->
          <div v-if="level === 1 || adminStore.selections[level - 2]" class="flex items-center gap-1.5">
            
            <div v-if="level > 1" class="text-slate-800 animate-pulse">
              <PhCaretRight :size="10" weight="bold" />
            </div>

            <!-- BOTÓN DE NIVEL -->
            <button
              :ref="el => { if (el) buttonRefs[level-1] = el }"
              @click.stop="openLevel(level - 1)"
              :class="[
                'relative flex flex-col items-start px-4 py-2 rounded-xl transition-all duration-500 ease-out border overflow-hidden',
                
                // --- FOCO: El nivel más profundo con selección ---
                isCurrentFoco(level - 1) 
                  ? 'min-w-[160px] bg-slate-800 border-cyan-500/50 shadow-[0_0_20px_rgba(6,182,212,0.15)] z-10 scale-105' 
                  : '',

                // --- CONTEXTO: Niveles superiores ya pasados ---
                isContext(level - 1)
                  ? 'min-w-[60px] max-w-[100px] bg-slate-900/40 border-slate-800 opacity-60 hover:opacity-100 hover:scale-100 scale-95'
                  : '',

                // --- VACÍO: Invitación a seleccionar ---
                !adminStore.selections[level - 1] 
                  ? 'min-w-[130px] border-dashed border-slate-700 bg-transparent hover:bg-slate-900/40' 
                  : ''
              ]"
            >
              <!-- Label (Se oculta en modo contexto para limpiar) -->
              <span 
                v-if="!isContext(level - 1)"
                class="text-[8px] uppercase tracking-[0.2em] font-black mb-0.5 text-slate-500 transition-opacity duration-300"
              >
                Nivel {{ level }}
              </span>

              <!-- Nombre del Territorio -->
              <span :class="[
                'text-xs font-bold truncate w-full text-left transition-all duration-300',
                isCurrentFoco(level - 1) ? 'text-cyan-400' : 'text-slate-200',
                isContext(level - 1) ? 'text-[10px] text-center text-slate-500' : ''
              ]">
                {{ getLevelName(level - 1) }}
              </span>

              <!-- Shimmer animado para el foco activo -->
              <div v-if="isCurrentFoco(level - 1)" class="absolute inset-0 bg-gradient-to-r from-transparent via-cyan-500/5 to-transparent -translate-x-full animate-[shimmer_3s_infinite]"></div>
            </button>

          </div>
        </template>
      </TransitionGroup>
    </nav>

    <!-- PANEL DE SELECCIÓN (FLOTANTE) -->
    <Teleport to="body">
      <transition name="fade-slide">
        <div
          v-if="activeIndex !== null"
          :style="panelStyle"
          class="fixed bg-slate-900/95 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] p-2 flex flex-col gap-1 min-w-[240px] max-h-[300px] overflow-y-auto z-[9999]"
          @click.stop
        >
          <div class="px-3 py-2 border-b border-slate-800 mb-1">
            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">
              Opciones disponibles
            </span>
          </div>

          <button
            v-for="option in adminStore.getOptionsByLevel(activeIndex)"
            :key="option.path"
            @click.stop="selectOption(option)"
            class="group flex items-center justify-between px-3 py-2.5 text-xs text-slate-300 hover:bg-cyan-500/10 hover:text-cyan-400 rounded-lg transition-all"
          >
            <div class="flex flex-col items-start">
              <span class="font-bold tracking-tight">{{ option.name }}</span>
              <span class="text-[8px] text-slate-600 group-hover:text-cyan-600 font-mono">
                PATH: {{ option.path }}
              </span>
            </div>
            <div v-if="isSelected(option)" class="w-1.5 h-1.5 rounded-full bg-cyan-500 shadow-[0_0_8px_#06b6d4]"></div>
          </button>

          <div v-if="!adminStore.getOptionsByLevel(activeIndex).length" class="p-6 text-center">
            <span class="text-xs text-slate-600 italic">No hay sub-territorios</span>
          </div>
        </div>
      </transition>
    </Teleport>

  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onUnmounted } from "vue";
import { useAdminStore } from "@/Kubix/core/stores/adminStore";
import { PhTreeStructure, PhCaretRight } from "@phosphor-icons/vue";

const adminStore = useAdminStore();
const activeIndex = ref(null);
const buttonRefs = ref([]);
const panelStyle = ref({});

// --- LOGICA DE ESTADOS VISUALES ---

const isCurrentFoco = (index) => {
  const selections = adminStore.selections;
  // Es foco si tiene algo seleccionado Y (es el último nivel o el siguiente está vacío)
  return selections[index] && (index === selections.length - 1 || !selections[index + 1]);
};

const isContext = (index) => {
  // Es contexto si hay una selección activa en un nivel superior al actual
  return adminStore.selections[index] && adminStore.selections[index + 1];
};

const getLevelName = (index) => {
  const selectionId = adminStore.selections[index];
  if (!selectionId) return 'Seleccionar...';
  
  // Construimos el path esperado hasta este nivel
  const currentPath = adminStore.selections.slice(0, index + 1).join('/');
  const match = adminStore.scopes.find(s => s.path === currentPath);
  
  // Si es modo contexto, tal vez quieras solo la inicial o un nombre corto
  return match ? match.name : 'Nivel ' + (index + 1);
};

const isSelected = (option) => {
  const id = option.path.split('/').pop();
  return adminStore.selections.includes(id);
};

// --- ACCIONES ---

const openLevel = async (index) => {
  if (activeIndex.value === index) {
    activeIndex.value = null;
    return;
  }

  activeIndex.value = index;
  await nextTick();

  const btn = buttonRefs.value[index];
  if (btn) {
    const rect = btn.getBoundingClientRect();
    panelStyle.value = {
      top: `${rect.bottom + 12}px`,
      left: `${Math.min(rect.left, window.innerWidth - 260)}px`
    };
  }
};

const selectOption = (option) => {
  const id = option.path.split('/').pop();
  adminStore.updateSelection(activeIndex.value, id);
  activeIndex.value = null;
};

const closePanel = () => { activeIndex.value = null; };

onMounted(() => {
  window.addEventListener("click", closePanel);
  if (adminStore.scopes.length === 0) adminStore.loadMock();
});

onUnmounted(() => window.removeEventListener("click", closePanel));
</script>

<style scoped>
/* Animación del Túnel */
.tunnel-enter-active, 
.tunnel-leave-active { 
  transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); 
}

.tunnel-enter-from { 
  opacity: 0; 
  transform: scale(0.9) translateX(20px); 
  filter: blur(8px);
}

.tunnel-leave-to { 
  opacity: 0; 
  transform: scale(1.1) translateX(-20px); 
  filter: blur(8px);
}

/* Animación del Panel Flotante */
.fade-slide-enter-active { transition: all 0.3s ease-out; }
.fade-slide-leave-active { transition: all 0.2s ease-in; }
.fade-slide-enter-from { opacity: 0; transform: translateY(-10px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-10px); }

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  50% { transform: translateX(100%); }
  100% { transform: translateX(100%); }
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { scrollbar-width: none; }
</style>