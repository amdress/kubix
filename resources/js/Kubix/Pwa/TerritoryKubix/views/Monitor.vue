<template>
    <div class="monitor-container space-y-6 p-1">
        <!-- GRID DE MÉTRICAS (Manejado por el Adapter) -->
        <section
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 items-stretch"
        >
            <MonitorAdapter
                v-for="(entity, index) in metrics"
                :key="entity.id"
                :entity="entity"
                class="h-full animate-slide-up"
                :style="{ animationDelay: `${index * 100}ms` }"
            />
        </section>

        <!-- CUERPO PRINCIPAL: MAPA + SIDEBAR -->
        <section
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 transition-all duration-500"
            :class="sidebarOpen ? '' : 'lg:grid-cols-1'"
        >
            <!-- CONTENEDOR DE VISTA (MAPA/DATA) -->
            <div
                class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl overflow-hidden flex flex-col min-h-[550px] shadow-2xl"
                :class="!sidebarOpen && 'lg:col-span-3'"
            >
                <!-- HEADER DE LA VISTA -->
                <header
                    class="px-4 py-3 border-b border-slate-800 flex items-center justify-between bg-slate-950/50 backdrop-blur-md"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"
                        ></div>
                        <h2
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400"
                        >
                            Live Territory:
                            <span class="text-cyan-400">{{
                                adminStore.currentLabel || "Global"
                            }}</span>
                        </h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <div
                            class="flex bg-slate-900 p-1 rounded-lg border border-slate-800"
                        >
                            <button
                                @click="viewMode = 'map'"
                                :class="
                                    viewMode === 'map'
                                        ? 'bg-slate-800 text-cyan-400 shadow-inner'
                                        : 'text-slate-500'
                                "
                                class="px-4 py-1 rounded-md text-[10px] font-black uppercase transition-all"
                            >
                                Map
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="
                                    viewMode === 'list'
                                        ? 'bg-slate-800 text-cyan-400 shadow-inner'
                                        : 'text-slate-500'
                                "
                                class="px-4 py-1 rounded-md text-[10px] font-black uppercase transition-all"
                            >
                                Data
                            </button>
                        </div>

                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            class="p-2 hover:bg-slate-800 rounded-lg text-slate-500 transition-colors hidden lg:block"
                        >
                            <PhCaretRight v-if="sidebarOpen" :size="20" />
                            <PhCaretLeft v-else :size="20" />
                        </button>
                    </div>
                </header>

                <!-- CONTENIDO DINÁMICO -->
                <div class="flex-1 relative bg-slate-950">
                    <transition name="fade-fast" mode="out-in">
                        <div
                            v-if="viewMode === 'map'"
                            key="map"
                            class="w-full h-full"
                        >
                            <TerritoryMap :items="mapData" />
                        </div>
                        <div
                            v-else
                            key="list"
                            class="p-8 flex flex-col items-center justify-center h-full opacity-30"
                        >
                            <span
                                class="text-[10px] font-black uppercase tracking-widest"
                                >Integrating Data Tables...</span
                            >
                        </div>
                    </transition>
                </div>
            </div>

            <!-- SIDEBAR (HEALTH & ACTIVITY) -->
            <Transition name="slide-right">
                <aside v-if="sidebarOpen" class="space-y-4">
                    <!-- Card: Health -->
                    <div
                        class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg"
                    >
                        <h3
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-6"
                        >
                            Territory Health
                        </h3>
                        <div class="space-y-5">
                            <div
                                v-for="metric in branchHealth"
                                :key="metric.label"
                                class="group"
                            >
                                <div
                                    class="flex justify-between text-[10px] font-bold mb-2 uppercase tracking-tighter"
                                >
                                    <span
                                        class="text-slate-400 group-hover:text-slate-200 transition-colors"
                                        >{{ metric.label }}</span
                                    >
                                    <span class="text-cyan-400">{{
                                        metric.value
                                    }}</span>
                                </div>
                                <div
                                    class="w-full h-1 bg-slate-800 rounded-full overflow-hidden"
                                >
                                    <div
                                        class="h-full transition-all duration-1000"
                                        :class="metric.barColor"
                                        :style="{
                                            width: metric.percentage + '%',
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Activity -->
                    <div
                        class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg"
                    >
                        <h3
                            class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-6"
                        >
                            Live Feed
                        </h3>
                        <div
                            class="space-y-4 max-h-64 overflow-y-auto custom-scrollbar pr-2"
                        >
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="flex gap-4 p-3 rounded-lg hover:bg-slate-800/50 transition-all border border-transparent hover:border-slate-800"
                            >
                                <component
                                    :is="activity.icon"
                                    :size="20"
                                    :class="activity.iconColor"
                                />
                                <div class="space-y-1">
                                    <p
                                        class="text-xs font-bold text-slate-200 leading-tight"
                                    >
                                        {{ activity.title }}
                                    </p>
                                    <p
                                        class="text-[10px] text-slate-500 font-medium"
                                    >
                                        {{ activity.time }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </Transition>
        </section>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { PhCaretLeft, PhCaretRight } from "@phosphor-icons/vue";

// Core
import { useAdminStore } from "@/Kubix/core/stores/adminStore";
import { useMonitorData } from "@/Kubix/core/orchestrators/territory/useMonitorData.js";

// Única vía de renderizado para métricas
import MonitorAdapter from "@/Kubix/Pwa/shared/Ui/Adapters/MonitorAdapter.vue";
import TerritoryMap from "@/Kubix/Pwa/shared/Ui/maps/worldMap.vue";

const adminStore = useAdminStore();
const { metrics, branchHealth, recentActivities, mapData } = useMonitorData();

const sidebarOpen = ref(false);
const viewMode = ref("map");
</script>

<style scoped>
@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-slide-up {
    animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.fade-fast-enter-active,
.fade-fast-leave-active {
    transition: opacity 0.15s ease;
}
.fade-fast-enter-from,
.fade-fast-leave-to {
    opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-right-enter-from,
.slide-right-leave-to {
    opacity: 0;
    transform: translateX(40px);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #334155;
    border-radius: 10px;
}
</style>
