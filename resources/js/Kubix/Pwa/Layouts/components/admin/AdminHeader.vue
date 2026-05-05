<template>
    <header
        class="flex flex-col px-6 border-b border-slate-800 sticky top-0 z-[100] bg-slate-950/95 backdrop-blur-md"
    >
        <!-- TOP ROW -->
        <div class="flex items-center justify-between w-full pt-4 pb-3">
            <!-- LEFT -->
            <div class="flex items-center gap-4">
                <button
                    @click.stop="ui.toggleSidebar()"
                    class="p-3 rounded-xl transition-all active:scale-90 hover:bg-slate-800 text-slate-400"
                >
                    <PhList :size="28" weight="bold" />
                </button>

                <!-- En el template de tu Header -->
                <div class="flex flex-col">
                    <h1
                        class="text-3xl font-black tracking-tighter bg-gradient-to-r from-cyan-400 via-gray-400 to-blue-300 bg-clip-text text-transparent leading-none"
                    >
                        <!-- Usamos el getter currentLabel para mostrar el nombre del nodo activo -->
                        Kubyx / {{ adminStore.currentLabel }}
                    </h1>

                    <div class="flex items-center gap-2 mt-1">
                        <!-- Aquí podrías usar el depth para mostrar visualmente el nivel de acceso -->
                        <span
                            class="font-mono text-[10px] text-cyan-500 font-bold uppercase tracking-[0.3em]"
                        >
                            {{ currentUser.role }}
                            <span class="text-slate-700 ml-1"
                                >Level {{
                                    adminStore.selections.filter(
                                        (s) => s !== null,
                                    ).length
                                }}</span
                            >
                        </span>

                        <span class="text-slate-600 text-[10px]">•</span>

                        <span
                            class="font-mono text-[10px] text-slate-500 uppercase tracking-widest"
                        >
                            ID: {{ currentUser.id }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-6">
                <div
                    class="hidden md:flex flex-col items-end border-r border-slate-800 pr-6"
                >
                    <span
                        class="text-[10px] font-mono text-slate-500 uppercase tracking-widest"
                    >
                        System_Time
                    </span>
                    <span class="text-sm font-mono font-bold text-slate-200">
                        {{ currentTime }}
                    </span>
                </div>

                <div
                    class="flex items-center gap-3 bg-slate-900/50 px-4 py-2 rounded-full border border-slate-800 shadow-[0_0_15px_rgba(0,255,255,0.05)]"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="flex h-2 w-2 rounded-full bg-green-500 animate-pulse"
                        ></span>
                        <span
                            class="font-mono text-[10px] text-slate-300 font-bold uppercase tracking-wider"
                        >
                            Sync_OK
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useUIStore } from "../../store/uiStore";
import { useAdminStore } from "@/Kubix/core/stores/adminStore";
import { PhList } from "@phosphor-icons/vue";

const ui = useUIStore();
const adminStore = useAdminStore(); // Acceso a la selección actual y jerarquía

const currentTime = ref(new Date().toLocaleTimeString());

/**
 * SIMULACIÓN DE USUARIO LOGUEADO
 * En una app real, esto vendría de un authStore.
 * Aquí definimos quién es el "dueño" de la sesión actual para el Header.
 */
const currentUser = ref({
    id: "USR-9921",
    name: "Andress",
    role: "CITY_MANAGER", // Cambia a 'CITY_MANAGER' o 'NEIGHBORHOOD_MANAGER' para testear
});

// Reloj del sistema
let timer;
onMounted(() => {
    timer = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString();
    }, 1000);
});

onUnmounted(() => clearInterval(timer));
</script>

<style scoped>
/* Transición suave para los cambios de nombre en el H1 */
h1 {
    transition: all 0.3s ease;
}
</style>
