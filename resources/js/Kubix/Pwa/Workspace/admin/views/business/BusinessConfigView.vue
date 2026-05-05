<template>
    <div class="min-h-screen bg-white pb-20">
        <div class="px-6 pt-10 pb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tighter">
                Configuração do Negócio
            </h1>
            <p
                class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1"
            >
                Gestão de Ecossistema KUBIX
            </p>
        </div>

        <div v-for="(section, index) in configMenu" :key="index" class="mb-8">
            <div class="px-6 mb-2">
                <h3
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400"
                >
                    {{ section.title }}
                </h3>
            </div>

            <div class="border-y border-slate-50">
                <button
                    v-for="item in section.items"
                    :key="item.id"
                    @click="handleNavigation(item.action)"
                    class="w-full flex items-center justify-between px-6 py-5 hover:bg-slate-50 active:bg-slate-100 transition-all group border-b border-slate-50 last:border-0"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="w-15 h-15 rounded-xl flex items-center justify-center transition-colors"
                            :class="
                                item.active
                                    ? 'bg-blue-50 text-blue-600'
                                    : 'bg-slate-50 text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600'
                            "
                        >
                            <component
                                :is="item.icon"
                                size="22"
                                weight="duotone"
                            />
                        </div>

                        <div class="flex flex-col text-left">
                            <span
                                class="text-md font-black text-slate-800 tracking-tight"
                            >
                                {{ item.label }}
                            </span>
                            <span
                                v-if="item.subtitle"
                                class="text-[10px] font-medium text-slate-400 leading-none mt-1"
                            >
                                {{ item.subtitle }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <span
                            v-if="item.badge"
                            class="px-2 py-0.5 bg-blue-100 text-[9px] font-black text-blue-700 rounded-full uppercase"
                        >
                            {{ item.badge }}
                        </span>
                        <PhCaretRight
                            size="18"
                            weight="bold"
                            class="text-slate-200 group-hover:text-blue-400 transition-colors"
                        />
                    </div>
                </button>
            </div>
        </div>

        <div class="px-6 mt-4">
            <button 
                @click="goToSocial()"
                class="w-full py-4 text-[10px] font-black uppercase tracking-[0.3em] text-red-400 hover:text-red-600 transition-colors"
            >
                Sair da conta empresarial
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, shallowRef } from "vue";
import { useRouter } from "vue-router";
import {
    PhPalette,
    PhImageSquare,
    PhIdentificationCard,
    PhMapPin,
    PhClock,
    PhMegaphone,
    PhCreditCard,
    PhShareNetwork,
    PhCaretRight,
    PhUsersThree,
    PhLockKey,
    PhAppWindow,
} from "@phosphor-icons/vue";

const router = useRouter();

// ESTRUCTURA DE LA LISTA BASADA EN TU SOLICITUD
const configMenu = shallowRef([
    {
        title: "Apariência & Branding",
        items: [
            {
                id: "splash",
                label: "SplashScreen",
                subtitle: "Tela de carregamento PWA",
                icon: PhAppWindow,
                action: "splash",
            },
            {
                id: "identity",
                label: "Nome & Banner",
                subtitle: "Logo y fotos de perfil",
                icon: PhIdentificationCard,
                action: "identity",
            },
            {
                id: "visual",
                label: "Apariência",
                subtitle: "Cores e temas do sistema",
                icon: PhPalette,
                action: "appearance",
            },
        ],
    },
    {
        title: "Operação & Unidades",
        items: [
            {
                id: "units",
                label: "Unidades do Negócio",
                subtitle: "Batel, Capão, etc.",
                icon: PhMapPin,
                action: "units",
                badge: "3 Ativas",
            },
            {
                id: "hours",
                label: "Horários de Trabalho",
                subtitle: "Abertura e fechamento",
                icon: PhClock,
                action: "hours",
            },
            {
                id: "team",
                label: "Gerenciamiento de Unidades",
                subtitle: "Equipe e permissões",
                icon: PhUsersThree,
                action: "team",
            },
        ],
    },
    {
        title: "Marketing & Social",
        items: [
            {
                id: "social",
                label: "Redes Sociais",
                subtitle: "Instagram, Facebook, WhatsApp",
                icon: PhShareNetwork,
                action: "social",
            },
            {
                id: "ads",
                label: "Gerenciamiento de Publicidade",
                subtitle: "Campanhas e anúncios",
                icon: PhMegaphone,
                action: "ads",
            },
            {
                id: "payments",
                label: "Pagamentos de Publicidade",
                subtitle: "Faturamento e cartões",
                icon: PhCreditCard,
                action: "payments",
            },
        ],
    },
    {
        title: "Segurança",
        items: [
            {
                id: "privacy",
                label: "Privacidade da Conta",
                subtitle: "Visibilidade e bloqueios",
                icon: PhLockKey,
                action: "privacy",
            },
        ],
    },
]);

const handleNavigation = (action) => {
    console.log("Navegando para:", action);
    //   router.push({ name: `admin.config.${action}` });
};

const goToSocial = () => {
    router.push({name:'social.mural'})
}
</script>

<style scoped>
/* Transición suave para los botones */
button {
    -webkit-tap-highlight-color: transparent;
}
</style>
