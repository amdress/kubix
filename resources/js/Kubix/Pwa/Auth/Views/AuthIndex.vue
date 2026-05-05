<template>
    <div class="fixed inset-0 w-full h-[100dvh] flex overflow-hidden bg-slate-950 selection:bg-blue-500/30">
        
        <!-- lado izquierdo -->
        <div 
            v-if="!context.isMobile" 
            class="relative hidden lg:flex lg:w-3/5 h-full overflow-hidden border-r border-white/5"
        >
            <img 
                :src="brandSplash" 
                class="absolute inset-0 w-full h-full object-cover opacity-50 scale-105 animate-soft-drift"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/40 via-transparent to-slate-950"></div>
            
            <div class="relative z-10 m-auto text-center">
                <h2 class="text-white text-[5rem] font-black uppercase italic tracking-tighter leading-[0.7] drop-shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
                    {{ neighborhoodName }}
                </h2>
                <div class="mt-8 flex items-center justify-center gap-6 opacity-30">
                    <div class="h-[1px] w-20 bg-white"></div>
                    <p class="text-white text-xl font-bold tracking-[1em] uppercase">
                        {{ cityName }}
                    </p>
                    <div class="h-[1px] w-20 bg-white"></div>
                </div>
            </div>
        </div>

        <!-- lado derecho -->
        <div 
            class="relative flex flex-col h-full w-full overflow-hidden transition-all duration-500"
            :class="[!context.isMobile ? 'lg:w-2/5 bg-slate-950/80 backdrop-blur-3xl lg:px-12' : '']"
        >
            <div v-if="context.isMobile" class="absolute inset-0 z-0">
                <img :src="brandSplash" class="w-full h-full object-cover opacity-60 animate-subtle-drift" />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/60 to-slate-950"></div>
            </div>

            <div class="relative z-10 flex flex-col h-full py-12 px-7 overflow-y-auto scrollbar-hide">
                
                <header class="flex flex-col items-center lg:items-start mb-8 animate-fade-down shrink-0">
                    <img 
                        v-if="brandLogo" 
                        :src="brandLogo" 
                        class="h-10 w-auto object-contain drop-shadow-2xl mb-6" 
                        alt="Logo" 
                    />
                    
                    <div v-if="context.isMobile" class="text-center">
                        <h3 class="text-white text-4xl font-black uppercase italic tracking-tighter leading-none">
                            {{ neighborhoodName }}
                        </h3>
                        <p class="text-[9px] font-bold tracking-[0.4em] uppercase text-white/40 mt-2">
                            {{ cityName }}
                        </p>
                    </div>
                </header>

                <transition name="view-slide" mode="out-in">
                    
                    <!-- WELCOME -->
                    <div v-if="view === 'welcome'" key="welcome" class="flex-1 flex flex-col justify-center text-center">
                        <main>
                            <h1 class="text-white text-2xl font-bold tracking-tight mb-12 drop-shadow-lg">
                                {{ $t('auth.welcome.title') }}
                            </h1>

                            <div class="grid grid-cols-2 gap-4 w-full max-w-[280px] mx-auto">
                                
                                <button @click="view = 'login'" class="aspect-square bg-white rounded-[1.5rem] flex flex-col items-center justify-center gap-2 shadow-2xl active:scale-95 transition-all group">
                                    <div class="p-3 rounded-full bg-slate-50 group-hover:bg-blue-50 transition-colors">
                                        <PhSignIn :size="24" class="text-blue-600" weight="bold" />
                                    </div>
                                    <span class="text-blue-900 font-black text-[10px] uppercase tracking-tighter">
                                        {{ $t('auth.welcome.btn_login') }}
                                    </span>
                                </button>

                                <button @click="view = 'register'" class="aspect-square bg-white rounded-[1.5rem] flex flex-col items-center justify-center gap-2 shadow-2xl active:scale-95 transition-all group">
                                    <div class="p-3 rounded-full bg-slate-50 group-hover:bg-blue-50 transition-colors">
                                        <PhUserPlus :size="24" class="text-blue-600" weight="bold" />
                                    </div>
                                    <span class="text-blue-900 font-black text-[10px] uppercase tracking-tighter leading-none">
                                        {{ $t('auth.welcome.btn_register') }}
                                    </span>
                                </button>
                            </div>

                            <button class="mt-10 flex items-center justify-center gap-2 text-white text-[10px] font-black uppercase tracking-[0.3em] opacity-60 hover:opacity-100 transition-opacity">
                                <PhDotsThreeVertical :size="18" /> {{ $t('auth.welcome.services_link') }}
                            </button>
                        </main>
                    </div>

                    <!-- LOGIN -->
                    <div v-else-if="view === 'login'" key="login" class="flex-1 flex flex-col justify-center">
                        <div class="w-full max-w-sm mx-auto px-2">
                            <div class="mb-10 text-center lg:text-left">
                                <h2 class="text-4xl lg:text-5xl font-black text-white uppercase italic tracking-tighter leading-none">
                                    {{ $t('auth.login.title') }}
                                </h2>
                                <p class="text-[10px] font-bold text-white/30 uppercase tracking-[0.2em] mt-2">
                                    {{ $t('auth.login.subtitle') }}
                                </p>
                            </div>
                            
                            <form @submit.prevent="handleLogin" class="space-y-5">
                                <KubixInput v-model="form.email" :label="$t('auth.login.email_label')" />
                                <KubixInput v-model="form.password" type="password" :label="$t('auth.login.password_label')" />
                                
                                <button 
                                    type="submit" 
                                    :disabled="isLoginInvalid"
                                    class="w-full py-5 rounded-[2rem] font-black text-white text-[11px] uppercase tracking-[0.3em]"
                                    :style="{ backgroundColor: primaryColor }"
                                >
                                    {{ loading ? $t('auth.login.submitting') : $t('auth.login.submit') }}
                                </button>
                            </form>

                            <div class="mt-10 flex justify-between">
                                <button @click="view = 'welcome'" class="text-[10px] text-white/20">
                                    {{ $t('auth.login.back') }}
                                </button>
                                <button @click="view = 'recover'" class="text-[10px] text-blue-400">
                                    {{ $t('auth.login.forgot') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- REGISTER -->
                    <div v-else-if="view === 'register'" key="register" class="flex-1 flex flex-col justify-center">
                        <div class="w-full max-w-sm mx-auto px-2">
                            <form @submit.prevent="handleRegister" class="space-y-4">
                                <KubixInput v-model="form.name" />
                                <KubixInput v-model="form.email" />
                                <KubixInput v-model="form.password" type="password" />
                                <KubixInput v-model="form.password_confirmation" type="password" />
                                
                                <button 
                                    type="submit" 
                                    :disabled="isRegisterInvalid"
                                    class="w-full py-5 rounded-[2rem] font-black text-white"
                                    :style="{ backgroundColor: primaryColor }"
                                >
                                    {{ loading ? '...' : $t('auth.register.submit') }}
                                </button>
                            </form>

                            <button @click="view = 'welcome'" class="mt-6 text-white/20 w-full text-center">
                                {{ $t('auth.register.already_have_account') }}
                            </button>
                        </div>
                    </div>

                </transition>

                <footer class="mt-auto pt-10 text-center">
                    <p class="text-[9px] text-white/20 uppercase">
                        KUBIX • {{ neighborhoodName }}
                    </p>
                </footer>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import { useTerritoryStore } from '@/Kubix/core/stores/territoryStore'
import { PhSignIn, PhUserPlus, PhDotsThreeVertical } from "@phosphor-icons/vue"
import KubixInput from '@/Kubix/shared/Ui/BaseInput.vue'

const { t } = useI18n()
const context = useContextStore()
const territory = useTerritoryStore()

const view = ref('welcome')
const loading = ref(false)

const form = reactive({ 
    name: '',
    email: '', 
    password: '',
    password_confirmation: ''
})

// ✅ TERRITORY (NUEVO)
const cityName = computed(() => territory.geo?.city || 'KUBIX')
const neighborhoodName = computed(() => territory.geo?.neighborhood || '')

const primaryColor = computed(() => {
  if (territory.branding?.neighborhood?.is_active) {
    return territory.branding.neighborhood.primary_color
  }
  return territory.branding?.city?.primary_color || '#6366f1'
})

const brandSplash = computed(() => {
  if (territory.branding?.neighborhood?.is_active) {
    return territory.branding.neighborhood.splash_image
  }
  return territory.branding?.city?.splash_image
})

const brandLogo = computed(() => {
  if (territory.branding?.neighborhood?.is_active) {
    return territory.branding.neighborhood.logo
  }
  return territory.branding?.city?.logo
})

// VALIDACIONES
const isEmailValid = computed(() => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email))

const isLoginInvalid = computed(() => loading.value || !isEmailValid.value || form.password.length < 6)

const isRegisterInvalid = computed(() =>
    loading.value || !isEmailValid.value || form.password.length < 6 ||
    form.password !== form.password_confirmation || !form.name
)

// HANDLERS
const handleLogin = async () => { loading.value = true; setTimeout(()=>loading.value=false,1500) }
const handleRegister = async () => { loading.value = true; setTimeout(()=>loading.value=false,1500) }
</script>