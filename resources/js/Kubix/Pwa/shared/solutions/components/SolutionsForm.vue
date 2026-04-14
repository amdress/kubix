<template>
  <div class="w-full h-full flex flex-col animate-in fade-in duration-500 min-h-0">
    <div class="flex flex-col h-full space-y-4">
      
      <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
        <div class="border-b border-slate-50 pb-4 mb-4">
          <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800 italic">05. Marketplace de Soluções</h3>
          <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold">
            Selecione os módulos que estarão disponíveis nesta unidade
          </p>
        </div>

        <div class="flex gap-2">
          <button 
            v-for="cat in ['todos', 'core', 'vertical', 'addon']" 
            :key="cat"
            @click="filter = cat"
            class="px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest transition-all"
            :class="filter === cat ? 'bg-slate-900 text-white shadow-lg' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
          >
            {{ cat }}
          </button>
        </div>
      </div>

      <div class="flex-grow overflow-y-auto custom-scrollbar pr-2">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div 
            v-for="sol in filteredSolutions" 
            :key="sol.id"
            @click="toggleSolution(sol.id)"
            class="group relative p-5 rounded-[24px] border-2 transition-all duration-300 cursor-pointer overflow-hidden"
            :class="selectedSolutions.includes(sol.id) 
              ? 'border-lime-400 bg-white shadow-xl shadow-lime-500/10' 
              : 'border-slate-100 bg-white hover:border-slate-300'"
          >
            <div class="absolute top-4 right-4 text-[7px] font-black uppercase px-2 py-1 rounded-md"
                 :class="sol.type === 'core' ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500'">
              {{ sol.type }}
            </div>

            <div class="flex flex-col h-full">
              <div class="w-10 h-10 rounded-xl mb-4 flex items-center justify-center transition-all duration-500"
                   :class="selectedSolutions.includes(sol.id) ? 'bg-lime-400 text-black' : 'bg-slate-50 text-slate-400 group-hover:bg-slate-100'">
                <i :class="getIcon(sol.slug)"></i>
              </div>

              <h4 class="text-[12px] font-black text-slate-800 uppercase tracking-tight">{{ sol.name }}</h4>
              <p class="text-[10px] text-slate-400 mt-2 flex-grow">{{ sol.description }}</p>

              <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between">
                <span class="text-[8px] font-bold text-slate-300 uppercase tracking-widest flex items-center gap-1">
                  <i class="fa-solid fa-code-branch"></i> {{ sol.base_git_branch }}
                </span>
                <div 
                  class="w-5 h-5 rounded-full flex items-center justify-center transition-all"
                  :class="selectedSolutions.includes(sol.id) ? 'bg-lime-500 text-white' : 'bg-slate-100 text-transparent'"
                >
                  <i class="fa-solid fa-check text-[10px]"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="shrink-0 pt-4">
        <button 
          @click="handleSubmit"
          :disabled="!isSelectionValid"
          class="w-full font-black uppercase text-[10px] tracking-[0.4em] py-6 rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3"
          :class="isSelectionValid 
            ? 'bg-lime-400 text-black hover:bg-lime-500' 
            : 'bg-slate-800 text-slate-600 cursor-not-allowed'"
        >
          <span>{{ isSelectionValid ? 'Finalizar e Instalar Instância ➔' : 'Selecione ao menos uma solução' }}</span>
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  // Las soluciones vienen del backend (puedes cargarlas en el parent Wizard)
  availableSolutions: {
    type: Array,
    default: () => [
      { id: 1, name: 'Libera Juros', slug: 'libera-juros', type: 'vertical', description: 'Cálculos revisionales y gestión de deudas bancarias.', base_git_branch: 'main' },
      { id: 2, name: 'Imobiliária Pro', slug: 'imob-pro', type: 'vertical', description: 'Gestión completa de alquileres y ventas de inmuebles.', base_git_branch: 'stable' },
      { id: 3, name: 'Core Finance', slug: 'core-fin', type: 'core', description: 'Módulo financiero base, flujo de caja y facturación.', base_git_branch: 'main' },
    ]
  }
});

const emit = defineEmits(['completed']);

const filter = ref('todos');
const selectedSolutions = ref([]);

const filteredSolutions = computed(() => {
  if (filter.value === 'todos') return props.availableSolutions;
  return props.availableSolutions.filter(s => s.type === filter.value);
});

const isSelectionValid = computed(() => {
  return selectedSolutions.value.length > 0;
});

const toggleSolution = (id) => {
  const index = selectedSolutions.value.indexOf(id);
  if (index > -1) {
    selectedSolutions.value.splice(index, 1);
  } else {
    selectedSolutions.value.push(id);
  }
};

const getIcon = (slug) => {
  const icons = {
    'libera-juros': 'fa-solid fa-scale-balanced',
    'imob-pro': 'fa-solid fa-building',
    'core-fin': 'fa-solid fa-vault'
  };
  return icons[slug] || 'fa-solid fa-box';
};

const handleSubmit = () => {
  if (!isSelectionValid.value) return;
  
  // Enviamos los IDs seleccionados
  emit('completed', {
    solution_ids: selectedSolutions.value
  });
};
</script>