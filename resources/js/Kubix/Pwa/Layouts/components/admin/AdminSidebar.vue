<template>
    <div class="sidebar-root">
        <Transition name="fade">
            <div
                v-if="!ui.sidebarCollapsed"
                class="fixed inset-0 bg-black/60 z-[950] lg:hidden backdrop-blur-sm"
                @click="ui.sidebarCollapsed = true"
            ></div>
        </Transition>

        <aside
            :class="[
                'h-screen flex flex-col transition-all duration-300 border-r z-[1000]',
                'fixed lg:relative top-0 left-0',
                ui.sidebarCollapsed
                    ? '-translate-x-full lg:translate-x-0 lg:w-15'
                    : 'translate-x-0 w-60',
                'bg-slate-950 border-slate-800 text-white',
            ]"
        >
            <div class="px-3 py-4 flex-shrink-0 border-b border-slate-900/50">
                <ProfileCard
                    :collapsed="ui.sidebarCollapsed"
                    :user="currentUser"
                />
            </div>

            <nav
                class="flex-1 overflow-y-auto px-2 py-4 space-y-1.5 custom-scroll"
            >
                <template v-for="item in menuItems" :key="item.label">
                    <router-link
                        v-if="item.to"
                        :to="item.to"
                        @click="
                            () => {
                                activeBlock = null;
                                closeOnMobile();
                            }
                        "
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group"
                        :class="[
                            ui.sidebarCollapsed
                                ? 'justify-start'
                                : 'justify-start',
                            $route.name === item.to?.name
                                ? 'bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 shadow-[0_0_15px_rgba(8,145,178,0.1)]'
                                : 'text-slate-500 hover:text-slate-200 hover:bg-slate-900',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            :size="22"
                            :weight="
                                item.to && $route.name === item.to.name
                                    ? 'duotone'
                                    : 'bold'
                            "
                            class="shrink-0"
                        />
                        <span
                            v-if="!ui.sidebarCollapsed"
                            class="truncate font-bold text-[10px] uppercase tracking-[0.15em] whitespace-nowrap"
                        >
                            {{ item.label }}
                        </span>
                    </router-link>

                    <div v-else class="flex flex-col">
                        <button
                            @click="toggleBlock(item.label)"
                            class="flex items-center justify-between w-full px-3 py-2.5 rounded-xl transition-all group hover:bg-slate-900 text-slate-500"
                            :class="[
                                ui.sidebarCollapsed
                                    ? 'justify-center'
                                    : 'justify-start',
                                activeBlock === item.label
                                    ? 'text-slate-200'
                                    : '',
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <component
                                    :is="item.icon"
                                    :size="22"
                                    weight="bold"
                                />
                                <span
                                    v-if="!ui.sidebarCollapsed"
                                    class="truncate font-bold text-[10px] uppercase tracking-[0.15em] whitespace-nowrap"
                                >
                                    {{ item.label }}
                                </span>
                            </div>
                            <PhCaretDown
                                v-if="!ui.sidebarCollapsed"
                                :size="12"
                                class="transition-transform duration-300"
                                :class="{
                                    'rotate-180 text-cyan-500':
                                        activeBlock === item.label,
                                }"
                            />
                        </button>

                        <div
                            v-if="
                                activeBlock === item.label &&
                                !ui.sidebarCollapsed
                            "
                            class="mt-1 ml-9 border-l border-slate-800 space-y-1"
                        >
                            <router-link
                                v-for="child in item.children"
                                :key="child.label"
                                :to="child.to"
                                @click="closeOnMobile"
                                class="block px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:text-cyan-400 transition-colors"
                            >
                                {{ child.label }}
                            </router-link>
                        </div>
                    </div>
                </template>
            </nav>

            <div
                class="px-2 py-4 border-t border-slate-900 flex-shrink-0 bg-slate-950/50"
            >
                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-500 hover:text-red-400 hover:bg-red-950/20 transition-all active:scale-95"
                    :class="
                        ui.sidebarCollapsed ? 'justify-center' : 'justify-start'
                    "
                >
                    <PhSignOut :size="22" weight="bold" />
                    <span
                        v-if="!ui.sidebarCollapsed"
                        class="text-[10px] font-black uppercase tracking-widest"
                        >Logout_System</span
                    >
                </button>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { computed, ref } from "vue"; // Importamos ref
import { useUIStore } from "../../store/uiStore";
import {
    PhMonitor,
    PhSignOut,
    PhGearSix,
    PhMapPinPlus,
    PhTreeStructure,
    PhUsersThree,
    PhWarningOctagon,
    PhStorefront,
    PhIdentificationCard,
    PhTrendUp,
    PhCaretDown,
    PhUsersFour
} from "@phosphor-icons/vue";
import ProfileCard from "../ProfileCard.vue";

const ui = useUIStore();
const activeBlock = ref(null); // Estado para el acordeón

const toggleBlock = (label) => {
    activeBlock.value = activeBlock.value === label ? null : label;
};

const currentUser = {
    name: "Admin User",
    role: "SUPERADMIN",
    avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop",
};

const closeOnMobile = () => {
    if (window.innerWidth < 1024) {
        ui.sidebarCollapsed = true;
    }
};

const handleLogout = () => {
    console.log("Iniciando secuencia de cierre de sesión...");
};

const menuItems = computed(() => {
    const items = [];

    // --- BLOCO 0: ESSENCIAL ---
    items.push({
        label: "Monitor",
        to: { name: "kubix.dashboard" },
        icon: PhMonitor,
    });

    // --- BLOCO 1: INFRAESTRUTURA (Só aparece no contexto GLOBAL) ---
    if (currentUser.role === "SUPERADMIN") {
        items.push({
            label: "Rede Global",
            icon: PhTreeStructure,
            children: [
                { label: "Novo Território", to: { name: "kubix.territoryCreate" }, icon: PhMapPinPlus },
                { label: "Configuração de Nodos", to: { name: "kubix.dashboard" } },
            ],
        });
    }

    // --- BLOCO 2: GESTÃO DE PESSOAS (Contexto Cidade ou Global) ---
    if (["SUPERADMIN", "CITY_MANAGER"].includes(currentUser.role)) {
        items.push({
            label: "Equipe Hub",
            icon: PhIdentificationCard,
            children: [
                { label: "Desempenho Regional", to: { name: "kubix.dashboard" } },
            ],
        });
    }

    // --- BLOCO 3: OPERAÇÃO DE NICHO (O "Coração" do Barrio / Franqueado) ---
    // Este bloco aparece para todos, mas os dados internos mudam conforme o contexto
    items.push({
        icon: PhUsersFour,
        label: "social",
        children: [
            { label: "Mural Social", to: { name: "kubix.dashboard" }, icon: PhUsersThree },
            { label: "Directório Comercial", to: { name: "kubix.dashboard" }, icon: PhStorefront },
            { label: "Promotores Ativos", to: { name: "kubix.dashboard" } },
            { 
                label: "Estratégia & Branding", 
                children: [
                    { label: "Ads & Editais", to: { name: "kubix.dashboard" } },
                    { label: "Identidade (Splash)", to: { name: "kubix.dashboard" } },
                    { label: "Gestão de Promotores", to: { name: "kubix.dashboard" } },

                ]
            }
        ],
    });

    // --- BLOCO 4: SUPORTE E AJUSTES (Sempre visíveis no final) ---
    items.push(
        {
            label: "Suporte & Issues",
            to: { name: "kubix.territorySettings" },
            icon: PhWarningOctagon,
            badge: 3,
        },
        {
            label: "Ajustes",
            to: { name: "kubix.territorySettings" },
            icon: PhGearSix,
        }
    );

    return items;
});
</script>

<style scoped>
/* Scrollbar técnico y minimalista */
.custom-scroll::-webkit-scrollbar {
    width: 3px;
}

.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(30, 41, 59, 0.5);
    border-radius: 10px;
}

.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(8, 145, 178, 0.3);
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
