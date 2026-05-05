<template>
    dashboard
    <!-- <div
        v-if="canNavigate"
        class="flex items-center gap-3 w-full border-t border-slate-900 pt-0"
    >
        <div class="flex items-center gap-2 text-slate-500 mr-2">
            <PhTreeStructure :size="20" weight="duotone" />
            <span
                class="text-[10px] font-mono font-bold uppercase tracking-tighter"
                >Hierarchy_Nav</span
            >
        </div>

        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar py-1">
            <nav
                class="flex items-center bg-slate-900/80 border border-slate-800 p-1 rounded-xl shadow-inner"
            >
                <button
                    class="px-4 py-2 rounded-lg text-xs font-black transition-all bg-slate-800 text-white border border-slate-700 shadow-sm active:scale-95"
                >
                    GLOBAL
                </button>

                <span class="px-2 text-slate-700">
                    <PhCaretRight :size="14" weight="bold" />
                </span>

                <button
                    class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-200 transition-all active:scale-95"
                >
                    BRAZIL
                </button>

                <span class="px-2 text-slate-700">
                    <PhCaretRight :size="14" weight="bold" />
                </span>

                <button
                    class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-200 transition-all active:scale-95"
                >
                    PARANÁ
                </button>

                <span class="px-2 text-slate-700">
                    <PhCaretRight :size="14" weight="bold" />
                </span>

                <button
                    class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-slate-200 transition-all active:scale-95"
                >
                    CURITIBA
                </button>

                <span class="px-2 text-slate-800">
                    <PhCaretRight :size="14" weight="bold" />
                </span>

                <button
                    class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 italic border border-dashed border-slate-800 bg-slate-950/50"
                >
                    CHOOSE_NEIGHBORHOOD...
                </button>
            </nav>

            <button
                class="ml-2 p-3 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 hover:bg-cyan-500/20 transition-all active:scale-90 shadow-lg shadow-cyan-500/5"
            >
                <PhMapPinArea :size="20" weight="bold" />
            </button>
        </div>
    </div> -->
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import {
    PhList,
    PhCaretRight,
    PhTreeStructure,
    PhMapPinArea,
} from "@phosphor-icons/vue";

/**
 * MOCK DE ROLES
 * En producción, esto vendría de un AuthStore o ContextStore.
 * Valores posibles: 'SUPERADMIN', 'CITY_MANAGER', 'NEIGHBORHOOD_MANAGER'
 */
const userRole = ref("CITY_MANAGER");

/**
 * LÓGICA DE NAVEGACIÓN
 * Si el rol es de barrio, el selector desaparece para no confundir.
 */
const canNavigate = computed(() => {
    const privilegedRoles = ["SUPERADMIN", "CITY_MANAGER"];
    return privilegedRoles.includes(userRole.value);
});
</script>
