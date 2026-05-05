<template>
  <div class="w-full h-full flex flex-col justify-center items-center animate-in fade-in duration-500 min-h-0">
    <form @submit.prevent="handleSubmit" class="flex flex-col h-full w-full max-w-4xl space-y-4">
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 flex-grow min-h-0">
        
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm flex flex-col overflow-y-auto custom-scrollbar">
          <div class="space-y-6">
            <div class="border-b border-slate-50 pb-4">
              <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-800 italic">03. Identidade do Gestor</h3>
              <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold">
                Vinculação: <span class="text-lime-600 italic">Branch Manager</span>
              </p>
            </div>

            <div class="space-y-4">
              <BaseInput 
                v-model="form.name" 
                label="Nome Completo" 
                placeholder="Ex: Pedro Almodóvar" 
                :error="touched.name ? fieldErrors.name : ''"
                @blur="touched.name = true"
                required 
              />
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <BaseInput 
                  v-model="form.email" 
                  type="email" 
                  label="E-mail Pessoal" 
                  placeholder="gestor@email.com" 
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

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <BaseInput 
                  v-model="form.cpf" 
                  label="CPF (Obrigatório)" 
                  mask="###.###.###-##" 
                  placeholder="000.000.000-00" 
                  :error="touched.cpf ? fieldErrors.cpf : ''"
                  @blur="touched.cpf = true"
                  required 
                />
                <BaseInput 
                  v-model="form.password" 
                  type="password" 
                  label="Senha de Acesso" 
                  placeholder="••••••••" 
                  :error="touched.password ? fieldErrors.password : ''"
                  @blur="touched.password = true"
                  required 
                />
              </div>
            </div>

            <div v-if="showQuestion" class="mt-4 p-5 bg-slate-50 rounded-[24px] border border-dashed border-slate-200 flex items-center justify-between group transition-all hover:border-lime-400/50">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center shadow-sm text-slate-400 group-hover:text-lime-500">
                  <i class="fa-solid fa-location-dot text-xs"></i>
                </div>
                <div>
                  <h4 class="text-[9px] font-black uppercase text-slate-800 tracking-wider">{{ questionTitle }}</h4>
                  <p class="text-[8px] text-slate-400 font-bold uppercase">Cadastrar residência do gestor?</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="extraAction" class="sr-only peer">
                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-lime-500"></div>
              </label>
            </div>
          </div>
        </div>

        <div class="bg-slate-950 p-6 rounded-[32px] shadow-2xl border border-slate-800 flex flex-col min-h-0">
          <div class="border-b border-white/10 pb-4 mb-4">
            <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-white italic">04. Mídia & Documentos</h3>
          </div>
          
          <div class="flex flex-col gap-6 items-center justify-center flex-grow">
            <div class="relative group">
              <div 
                class="w-40 h-40 rounded-[40px] border-4 border-slate-800 bg-slate-900 flex items-center justify-center overflow-hidden transition-all duration-500 group-hover:border-lime-400/50 shadow-2xl cursor-pointer"
                @click="$refs.avatarInput.click()"
              >
                <img v-if="previews.avatar" :src="previews.avatar" class="w-full h-full object-cover" />
                <div v-else class="text-center">
                  <i class="fa-solid fa-user-astronaut text-4xl text-slate-800 mb-2 group-hover:text-lime-400 transition-colors"></i>
                  <p class="text-[7px] font-black text-slate-600 uppercase tracking-[0.2em]">Foto de Perfil</p>
                </div>
              </div>
              <input type="file" ref="avatarInput" class="hidden" accept="image/*" @change="e => handleFile(e, 'avatar')" />
            </div>

            <div 
              class="w-full max-w-sm group relative rounded-[24px] border-2 border-dashed transition-all duration-500 overflow-hidden py-8 flex flex-col items-center justify-center bg-white/[0.02] cursor-pointer"
              :class="previews.document ? 'border-lime-400/50 bg-slate-900/50' : 'border-white/10 hover:border-lime-400'"
              @click="$refs.docInput.click()"
            >
              <div v-if="!previews.document" class="text-center">
                <i class="fa-solid fa-id-card text-2xl text-white/20 mb-2 group-hover:text-lime-400 transition-colors"></i>
                <p class="text-white text-[8px] font-black uppercase tracking-widest">Documento de Identidade</p>
                <p class="text-slate-500 text-[7px] mt-1 font-bold italic uppercase">(Frente e Verso)</p>
              </div>
              <div v-else class="flex flex-col items-center gap-2 text-center p-4">
                <div class="w-10 h-10 bg-lime-500 text-black rounded-xl flex items-center justify-center shadow-lg">
                  <i class="fa-solid fa-file-shield text-lg"></i>
                </div>
                <span class="text-[9px] font-black text-lime-400 uppercase tracking-widest break-all">
                  {{ form.media.document_file?.name }}
                </span>
                <button @click.stop="clearFile" class="text-[8px] text-red-400 font-black uppercase hover:text-red-300 mt-2">[ Remover ]</button>
              </div>
              <input type="file" ref="docInput" class="hidden" @change="e => handleFile(e, 'document')" />
            </div>
          </div>
        </div>
      </div>

      <div class="shrink-0">
        <button 
          type="submit"
          :disabled="!isFormValid"
          class="w-full font-black uppercase text-[10px] tracking-[0.4em] py-6 rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3 duration-300"
          :class="isFormValid 
            ? 'bg-lime-400 text-black hover:bg-lime-500' 
            : 'bg-slate-800 text-slate-600 cursor-not-allowed shadow-none'"
        >
          <span>{{ isFormValid ? 'Prosseguir para Finalização ➔' : 'Complete os campos obrigatórios' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue';
import BaseInput from '@/Kubix/common/Ui/BaseInput.vue';

const props = defineProps({
  initialData: { type: Object, default: () => ({}) },
  showQuestion: { type: Boolean, default: true },
  questionTitle: { type: String, default: 'Endereço Pessoal' }
});

const emit = defineEmits(['completed']);

const previews = reactive({
  avatar: null,
  document: false
});

const extraAction = ref(false); 

const form = reactive({
  name: props.initialData.name || '',
  email: props.initialData.email || '',
  phone: props.initialData.phone || '',
  cpf: props.initialData.cpf || '',
  password: '',
  media: {
    avatar_file: null,
    document_file: null
  }
});

// Control de campos interactuados (Dirty)
const touched = ref({
  name: false,
  email: false,
  cpf: false,
  password: false
});

// Errores calculados en tiempo real
const fieldErrors = computed(() => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const cleanCpf = form.cpf.replace(/\D/g, '');
  
  return {
    name: form.name.trim().length < 3 ? 'O nome deve ter pelo menos 3 caracteres' : '',
    email: !emailRegex.test(form.email) ? 'Insira um e-mail válido' : '',
    cpf: cleanCpf.length !== 11 ? 'CPF deve conter 11 dígitos' : '',
    password: form.password.length < 6 ? 'A senha deve ter pelo menos 6 caracteres' : ''
  };
});

// Botón habilitado solo si todos los errores están vacíos
const isFormValid = computed(() => {
  return !Object.values(fieldErrors.value).some(error => error !== '');
});

const handleFile = (event, type) => {
  const file = event.target.files[0];
  if (!file) return;
  if (type === 'avatar') {
    form.media.avatar_file = file;
    const reader = new FileReader();
    reader.onload = (e) => previews.avatar = e.target.result;
    reader.readAsDataURL(file);
  } else {
    form.media.document_file = file;
    previews.document = true;
  }
};

const clearFile = () => {
  form.media.document_file = null;
  previews.document = false;
};

const handleSubmit = () => {
  if (!isFormValid.value) return;
  
  const payload = { 
    ...form, 
    has_manager_address: extraAction.value
  };
  emit('completed', payload);
};
</script>