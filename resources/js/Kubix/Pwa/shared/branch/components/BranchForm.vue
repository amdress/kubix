<template>
  <div class="w-full h-full flex flex-col animate-in fade-in duration-500 min-h-0">
    <form @submit.prevent="handleSubmit" class="flex flex-col h-full space-y-4">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-grow min-h-0">

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm flex flex-col overflow-y-auto custom-scrollbar">
          <div class="space-y-6">
            <div class="border-b border-slate-50 pb-4">
              <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800 italic">
                01. Configuração de Rede
              </h3>
              <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold">
                Acesso: <span :class="isSuperAdmin ? 'text-lime-500' : 'text-blue-500'">
                  {{ isSuperAdmin ? 'Global Root' : 'Manager Branch' }}
                </span>
              </p>
            </div>

            <div class="space-y-4">
              <BaseInput 
                v-model="form.name" 
                label="Nome da Unidade" 
                placeholder="Ex: Sede Batel" 
                :error="touched.name ? fieldErrors.name : ''"
                @blur="touched.name = true"
                required 
              />

              <div class="grid grid-cols-2 gap-4">
                <BaseInput 
                  v-model="form.hierarchy_level" 
                  type="select" 
                  label="Nível" 
                  :options="filteredHierarchyOptions" 
                  :error="touched.hierarchy_level ? fieldErrors.hierarchy_level : ''"
                  @change="touched.hierarchy_level = true"
                  required 
                />

                <BaseInput 
                  v-model="form.type" 
                  type="select" 
                  label="Operação" 
                  :options="typeOptions" 
                  :error="touched.type ? fieldErrors.type : ''"
                  @change="touched.type = true"
                  required 
                />
              </div>

              <BaseInput
                v-model="form.parent_id"
                type="select"
                label="Unidade Superior (Pai)"
                :options="filteredParentOptions"
                :disabled="!isSuperAdmin"
                :placeholder="isSuperAdmin ? 'Unidade Raiz' : ''"
                :error="touched.parent_id ? fieldErrors.parent_id : ''"
                @change="touched.parent_id = true"
                required
              />
            </div>

            <div class="pt-4 border-t border-slate-50 grid grid-cols-2 gap-4">
              <BaseInput 
                v-model="form.email" 
                label="E-mail" 
                placeholder="adm@kubix.com" 
                :error="touched.email ? fieldErrors.email : ''"
                @blur="touched.email = true"
                required
              />
              <BaseInput 
                v-model="form.phone" 
                type="phone" 
                label="WhatsApp" 
                placeholder="(41) 99999-9999" 
              />
            </div>
          </div>
        </div>

        <div class="bg-slate-950 p-6 rounded-[32px] shadow-2xl border border-slate-800 flex flex-col min-h-0 overflow-y-auto custom-scrollbar">
          <div class="border-b border-white/10 pb-4 mb-4">
            <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-white italic">02. Identidade Visual</h3>
          </div>

          <div class="flex flex-col gap-4 flex-grow">
            <div class="bg-white/[0.03] rounded-2xl p-4 border border-white/5 flex items-center gap-6">
              <input 
                type="color" 
                v-model="form.branding.primary_color"
                class="w-12 h-12 rounded-xl bg-transparent cursor-pointer border-2 border-slate-800 p-1"
              />
              <div class="flex flex-col">
                <span class="text-[8px] font-black uppercase text-slate-500 tracking-widest">Primary Color</span>
                <span class="text-xl font-black text-white tracking-tighter font-mono">{{ form.branding.primary_color.toUpperCase() }}</span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-grow">
              <div 
                class="group relative rounded-[24px] border-2 border-dashed transition-all duration-500 overflow-hidden min-h-[160px] flex items-center justify-center bg-white/[0.02]"
                :class="logoPreview ? 'border-lime-400/50 bg-slate-900/50' : 'border-white/10 hover:border-lime-400'"
                @click="$refs.logoInput.click()"
              >
                <img v-if="logoPreview" :src="logoPreview" class="absolute inset-0 w-full h-full object-contain p-6 z-10" />
                <div v-else class="text-center transition-all duration-500">
                  <i class="fa-solid fa-fingerprint text-2xl text-white/20 mb-2 group-hover:text-lime-400 transition-colors"></i>
                  <p class="text-white text-[8px] font-black uppercase tracking-widest">Logo Principal</p>
                </div>
                <input type="file" ref="logoInput" @change="e => handleImage(e, 'logo')" class="hidden" accept="image/*" />
                <button v-if="logoPreview" @click.stop="clearImage('logo')" class="absolute top-2 right-2 z-20 w-6 h-6 rounded-full bg-red-500 text-white text-[10px]"><i class="fa-solid fa-xmark"></i></button>
              </div>

              <div 
                class="group relative rounded-[24px] border-2 border-dashed transition-all duration-500 overflow-hidden min-h-[160px] flex items-center justify-center bg-white/[0.02]"
                :class="watermarkPreview ? 'border-blue-400/50 bg-slate-900/50' : 'border-white/10 hover:border-blue-400'"
                @click="$refs.watermarkInput.click()"
              >
                <img v-if="watermarkPreview" :src="watermarkPreview" class="absolute inset-0 w-full h-full object-contain p-6 z-10 opacity-50" />
                <div v-else class="text-center transition-all duration-500">
                  <i class="fa-solid fa-droplet text-2xl text-white/20 mb-2 group-hover:text-blue-400 transition-colors"></i>
                  <p class="text-white text-[8px] font-black uppercase tracking-widest">Watermark (Marca d'água)</p>
                </div>
                <input type="file" ref="watermarkInput" @change="e => handleImage(e, 'watermark')" class="hidden" accept="image/*" />
                <button v-if="watermarkPreview" @click.stop="clearImage('watermark')" class="absolute top-2 right-2 z-20 w-6 h-6 rounded-full bg-red-500 text-white text-[10px]"><i class="fa-solid fa-xmark"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="shrink-0">
        <button 
          type="submit"
          :disabled="!isFormValid"
          class="w-full group font-black uppercase text-[10px] tracking-[0.4em] py-6 rounded-2xl transition-all duration-300 flex items-center justify-center gap-2"
          :class="isFormValid
            ? 'bg-lime-400 hover:bg-lime-500 text-black shadow-xl'
            : 'bg-slate-800 text-slate-600 cursor-not-allowed shadow-none'"
        >
          <span>{{ isFormValid ? 'Prosseguir para Localização ➔' : 'Complete os campos obrigatórios' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import BaseInput from '@/Kubix/common/Ui/BaseInput.vue';
import { useAuthStore } from '@/Kubix/core/stores/auth';

const emit = defineEmits(['completed']);
const props = defineProps({
  initialData: { type: Object, default: () => ({}) },
  existingBranches: { type: Array, default: () => [] }
});

const branchId = computed(() => route.params.id || null)

const auth = useAuthStore();
const user = computed(() => auth.user);

const logoPreview = ref(null);
const watermarkPreview = ref(null);

const form = ref({
  name: '', 
  hierarchy_level: '', 
  parent_id: null, 
  type: '', 
  email: '', 
  phone: '',
  branding: { primary_color: '#6366f1', logo: null, watermark: null }
});

const touched = ref({
  name: false,
  hierarchy_level: false,
  type: false,
  email: false,
  parent_id: false
});

// ERRORES CALCULADOS EN TIEMPO REAL
const fieldErrors = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return {
    name: form.value.name.trim().length < 3 ? 'Mínimo 3 caracteres' : '',
    hierarchy_level: !form.value.hierarchy_level ? 'Selecione um nível' : '',
    type: !form.value.type ? 'Selecione a operação' : '',
    email: !emailRegex.test(form.value.email) ? 'E-mail inválido' : '',
    parent_id: (!form.value.parent_id && !isSuperAdmin.value) ? 'Selecione a unidade superior' : ''
  };
});

// BOTÓN VÁLIDO
const isFormValid = computed(() => {
  return !Object.values(fieldErrors.value).some(error => error !== '');
});

const typeOptions = [
  { value: 'physical', label: 'Unidade Física' },
  { value: 'digital', label: 'Digital/SaaS' }
];

const hierarchyOptions = [
  { value: 'country', label: 'País' },
  { value: 'state', label: 'Estado' },
  { value: 'city', label: 'Cidade' },
  { value: 'neighborhood', label: 'Bairro' }
];

const isSuperAdmin = computed(() => !user.value.branch_id && user.value.role === 'superadmin');

onMounted(() => {
  if (props.initialData?.name) {
    Object.assign(form.value, JSON.parse(JSON.stringify(props.initialData)));
  }
  if (!isSuperAdmin.value && user.value.branch_id) {
    form.value.parent_id = user.value.branch_id;
  }
});

const filteredHierarchyOptions = computed(() => {
  if (isSuperAdmin.value) return hierarchyOptions;
  const levels = { country: 1, state: 2, city: 3, neighborhood: 4 };
  const currentLevel = levels[user.value.branch_level] || 3;
  return hierarchyOptions.filter(opt => levels[opt.value] > currentLevel);
});

const filteredParentOptions = computed(() => {
  if (isSuperAdmin.value)
    return props.existingBranches.map(b => ({ value: b.id, label: b.name }));
  return [{ value: user.value.branch_id, label: user.value.branch_name }];
});

const handleImage = (e, type) => {
  const file = e.target.files[0];
  if (!file) return;
  form.value.branding[type] = file;
  const reader = new FileReader();
  reader.onload = (ev) => {
    if (type === 'logo') logoPreview.value = ev.target.result;
    if (type === 'watermark') watermarkPreview.value = ev.target.result;
  };
  reader.readAsDataURL(file);
};

const clearImage = (type) => {
  if (type === 'logo') logoPreview.value = null;
  if (type === 'watermark') watermarkPreview.value = null;
  form.value.branding[type] = null;
};

const handleSubmit = () => {
  // Marcar todos como tocados al intentar enviar
  Object.keys(touched.value).forEach(k => touched.value[k] = true);
  if (isFormValid.value) emit('completed', { ...form.value });
};
</script>