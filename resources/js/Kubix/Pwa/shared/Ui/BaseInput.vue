<template>
  <div class="w-full group/field" :style="{ '--p-color': brand?.primaryColor || '#3b82f6' }">
    <div class="relative flex flex-col">
      
      <div class="relative flex items-center transition-all duration-300">
        
        <div 
          v-if="showLeftIcon" 
          class="absolute left-4 flex items-center justify-center pointer-events-none transition-colors duration-300"
          :class="[error ? 'text-red-400' : 'text-slate-500 group-focus-within/field:text-[var(--p-color)]']"
        >
          <component :is="leftIconComponent" :size="20" weight="bold" />
        </div>

        <template v-if="type !== 'select'">
          <input
            :id="computedId"
            :type="inputType"
            :value="isMasked ? displayValue : modelValue"
            @input="handleInputProxy"
            @blur="isFocused = false; $emit('blur', $event)"
            @focus="isFocused = true; $emit('focus', $event)"
            :placeholder="isFocused ? placeholder : ''"
            :disabled="disabled"
            :readonly="readonly"
            :class="[
              inputClasses,
              showLeftIcon ? 'pl-12' : 'pl-5',
              showRightIcon ? 'pr-12' : 'pr-5'
            ]"
            class="peer w-full outline-none transition-all duration-300"
          />
        </template>

        <template v-else>
          <select
            :id="computedId"
            :value="modelValue"
            @change="handleSelectChange"
            :disabled="disabled"
            :class="[inputClasses, showLeftIcon ? 'pl-12' : 'pl-5']"
            class="peer w-full outline-none appearance-none cursor-pointer pr-12 text-white/90"
          >
            <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value" class="bg-slate-900 text-white">
              {{ opt.label }}
            </option>
          </select>
        </template>

        <label 
          v-if="label"
          :for="computedId"
          class="absolute left-0 top-1/2 -translate-y-1/2 pointer-events-none transition-all duration-300 text-slate-500 font-bold uppercase tracking-[0.2em] text-[10px]"
          :class="[
            showLeftIcon ? 'ml-12' : 'ml-5',
            (modelValue || isFocused) ? '-translate-y-11 !ml-1 !text-[9px] text-[var(--p-color)]' : ''
          ]"
        >
          {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>

        <div class="absolute right-2 flex items-center gap-1">
          <button 
            v-if="type === 'password' && modelValue" 
            type="button" @click="togglePasswordVisibility" 
            class="p-2 text-slate-500 hover:text-white active:scale-90 transition-all"
          >
            <PhEye v-if="!passwordVisible" :size="20" weight="bold" />
            <PhEyeSlash v-else :size="20" weight="bold" />
          </button>
          
          <div v-if="loading" class="p-2 animate-spin text-[var(--p-color)]">
            <PhCircleNotch :size="20" weight="bold" />
          </div>

          <button 
            v-if="modelValue && clearable && !disabled && !loading && !readonly" 
            type="button" @click="clearInput" 
            class="p-2 text-slate-500 hover:text-red-500 transition-colors"
          >
            <PhXCircle :size="20" weight="fill" />
          </button>

          <PhCaretDown v-if="type === 'select'" :size="16" class="mr-2 text-slate-500 pointer-events-none" />
        </div>
      </div>
    </div>

    <div class="min-h-[20px] mt-1 px-1">
      <transition name="slide-fade">
        <div v-if="type === 'password' && modelValue && showPasswordStrength" class="mb-2">
          <div class="flex items-center gap-2">
            <div class="flex-1 h-1 bg-slate-800 rounded-full overflow-hidden flex gap-0.5">
              <div 
                v-for="n in 5" :key="n"
                class="h-full flex-1 transition-all duration-500"
                :class="n <= passwordStrength ? passwordStrengthColor : 'bg-slate-700'"
              ></div>
            </div>
            <span class="text-[8px] font-black uppercase tracking-tighter" :class="passwordStrengthTextColor">
              {{ passwordStrengthText }}
            </span>
          </div>
        </div>
      </transition>

      <transition name="slide-fade">
        <p v-if="error" class="text-[9px] text-red-500 font-black uppercase tracking-widest flex items-center gap-1.5 italic">
          <PhWarningCircle weight="fill" /> {{ error }}
        </p>
        <p v-else-if="success" class="text-[9px] text-green-500 font-black uppercase tracking-widest flex items-center gap-1.5 italic">
          <PhCheckCircle weight="fill" /> {{ success }}
        </p>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { 
  PhEnvelope, PhLock, PhPhone, PhIdentificationCard, 
  PhBuildings, PhMapPin, PhLink, PhHash, PhEye, PhEyeSlash, 
  PhXCircle, PhCircleNotch, PhCaretDown, PhWarningCircle, PhCheckCircle 
} from "@phosphor-icons/vue";

const props = defineProps({
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  label: String,
  placeholder: String,
  error: String,
  success: String,
  required: Boolean,
  disabled: Boolean,
  readonly: Boolean,
  loading: Boolean,
  clearable: { type: Boolean, default: true },
  showPasswordStrength: { type: Boolean, default: false },
  icon: Object,
  options: { type: Array, default: () => [] },
  brand: { type: Object, default: () => ({ primaryColor: '#3b82f6' }) }
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'change']);

const isFocused = ref(false);
const passwordVisible = ref(false);
const displayValue = ref('');

const inputType = computed(() => {
  if (props.type === 'password') return passwordVisible.value ? 'text' : 'password';
  return ['phone', 'cpf', 'cnpj', 'cep', 'currency', 'rg'].includes(props.type) ? 'text' : props.type;
});

const isMasked = computed(() => ['phone', 'cpf', 'cnpj', 'cep', 'currency', 'rg'].includes(props.type));

const iconMap = {
  email: PhEnvelope,
  password: PhLock,
  phone: PhPhone,
  cpf: PhIdentificationCard,
  rg: PhIdentificationCard,
  cnpj: PhBuildings,
  cep: PhMapPin,
  url: PhLink,
  number: PhHash
};

const leftIconComponent = computed(() => props.icon || iconMap[props.type]);
const showLeftIcon = computed(() => !!leftIconComponent.value);
const showRightIcon = computed(() => true);
const computedId = computed(() => `kubix-${Math.random().toString(36).slice(2, 7)}`);

const inputClasses = computed(() => {
  const base = 'h-14 rounded-2xl text-[15px] font-bold tracking-tight transition-all duration-500 border-2';
  if (props.disabled) return `${base} bg-slate-900/20 border-slate-800 text-slate-600 cursor-not-allowed`;
  if (props.error) return `${base} bg-red-500/5 border-red-500/50 text-red-200 shadow-[0_0_20px_-5px_rgba(239,68,68,0.2)]`;
  
  return isFocused.value 
    ? `${base} bg-white/10 border-[var(--p-color)] text-white shadow-[0_0_25px_-10px_var(--p-color)]`
    : `${base} bg-white/5 border-white/10 text-white/90 hover:border-white/20`;
});

// --- Lógica de Máscaras (Consistente con tu proyecto) ---
const applyMask = (val, type) => {
  if (!val) return '';
  const cleaned = String(val).replace(/\D/g, '');
  switch(type) {
    case 'phone': return cleaned.replace(/(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3').substring(0, 15);
    case 'cpf': return cleaned.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4').substring(0, 14);
    case 'cnpj': return cleaned.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5').substring(0, 18);
    case 'cep': return cleaned.replace(/(\d{5})(\d{3})/, '$1-$2').substring(0, 9);
    case 'currency': return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(parseFloat(cleaned || 0) / 100);
    default: return val;
  }
};

const handleInputProxy = (e) => {
  let val = e.target.value;
  if (isMasked.value) {
    val = val.replace(/\D/g, '');
    displayValue.value = applyMask(val, props.type);
  }
  emit('update:modelValue', val);
};

const handleSelectChange = (e) => { emit('update:modelValue', e.target.value); emit('change', e.target.value); };
const togglePasswordVisibility = () => passwordVisible.value = !passwordVisible.value;
const clearInput = () => { emit('update:modelValue', ''); displayValue.value = ''; };

// --- Password Strength ---
const passwordStrength = computed(() => {
  const p = String(props.modelValue || '');
  if (!p || p.length < 4) return 0;
  let s = 1;
  if (p.length > 8) s++;
  if (/[A-Z]/.test(p)) s++;
  if (/[0-9]/.test(p)) s++;
  if (/[^A-Za-z0-9]/.test(p)) s++;
  return s;
});

const passwordStrengthText = computed(() => ['Muito Fraca', 'Fraca', 'Média', 'Boa', 'Forte', 'Excelente'][passwordStrength.value]);
const passwordStrengthColor = computed(() => ['bg-red-600', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'][passwordStrength.value]);
const passwordStrengthTextColor = computed(() => ['text-red-600', 'text-red-500', 'text-orange-500', 'text-yellow-500', 'text-green-500', 'text-green-700'][passwordStrength.value]);

watch(() => props.modelValue, (nv) => {
  if (isMasked.value) displayValue.value = applyMask(nv, props.type);
}, { immediate: true });
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active { transition: all 0.3s ease; }
.slide-fade-enter-from, .slide-fade-leave-to { opacity: 0; transform: translateY(-5px); }

input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus {
  -webkit-text-fill-color: white;
  -webkit-box-shadow: 0 0 0px 1000px rgba(15, 23, 42, 0.9) inset;
  transition: background-color 5000s ease-in-out 0s;
}

select { background-image: none; }
</style>