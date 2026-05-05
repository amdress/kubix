<template>
    <div class="space-y-8 mt-10">
        <header class="flex flex-col md:flex-row justify-between items-end gap-6">
            <div class="flex items-center gap-5">
                <div class="bg-lime-400 p-3 rounded-2xl shadow-[0_0_20px_rgba(163,230,53,0.3)]">
                    <i class="fa-solid fa-earth-americas text-black text-xl"></i>
                </div>

                <div>
                    <nav class="text-[9px] text-slate-500 uppercase tracking-[0.2em] mb-1.5 flex gap-2 font-bold">
                        <span 
                            v-for="(item, i) in breadcrumb" 
                            :key="i" 
                            @click="goLevel(i)"
                            class="hover:text-lime-400 cursor-pointer transition-colors"
                        >
                            {{ i > 0 ? '/ ' + item : item }}
                        </span>
                    </nav>

                    <h1 class="text-3xl font-black tracking-tighter leading-none" 
                        :class="uiStore.isDark ? 'text-white' : 'text-slate-900'">
                        <span class="opacity-30 text-xs font-mono align-middle mr-2">KUBYX /</span>
                        <span class="text-lime-400">{{ currentLocationTitle }}</span>
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex gap-3">
                    <div v-if="availableStates.length > 0" class="group relative">
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1.5 ml-1 group-hover:text-lime-400 transition-colors">Estado</label>
                        <div class="relative">
                            <select :value="dashboard.context.state" @change="handleStateChange($event.target.value)" class="custom-select">
                                <option :value="null">-- Todos --</option>
                                <option v-for="s in availableStates" :key="s.code" :value="s.code">{{ s.name }}</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-icon"></i>
                        </div>
                    </div>
                    <div v-if="dashboard.context.state && availableCities.length > 0" class="group relative">
                        <label class="block text-[9px] font-black text-slate-500 uppercase tracking-[0.2em] mb-1.5 ml-1 group-hover:text-lime-400">Ciudad</label>
                        <div class="relative">
                            <select :value="dashboard.context.city" @change="handleCityChange($event.target.value)" class="custom-select">
                                <option :value="null">-- Todas --</option>
                                <option v-for="city in availableCities" :key="city" :value="city">{{ city }}</option>
                            </select>
                            <i class="fa-solid fa-chevron-down select-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <KpiGrid :data="kpis" :isDark="uiStore.isDark" />

        <div class="grid grid-cols-12 gap-6">
            <div
                class="col-span-12 lg:col-span-8 p-6 rounded-3xl border relative flex flex-col min-h-[550px] transition-colors duration-500"
                :class="uiStore.isDark ? 'bg-slate-900/30 border-slate-800/50 shadow-2xl shadow-black/20' : 'bg-white border-slate-200 shadow-sm'"
            >
                <div class="flex justify-between items-center mb-6">
                    <div class="flex flex-col">
                        <h3 class="text-[10px] uppercase tracking-[0.3em] opacity-40 font-black italic">
                            <span class="text-lime-400">{{ currentLocationTitle }}</span> Infrastructure Matrix
                        </h3>
                    </div>
                    <div v-if="isLoadingGeo" class="flex items-center gap-2">
                        <div class="w-1.5 h-1.5 bg-lime-400 rounded-full animate-pulse"></div>
                        <span class="text-[9px] uppercase tracking-widest opacity-40 font-bold">Syncing Map...</span>
                    </div>
                </div>

                <div class="flex-grow w-full relative">
                    <WorldMap :points="mapPoints" :areas="mapAreas" @select="handleMapSelect" />
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
                <DistributionCard 
                    :items="distribution"
                    :title="distributionTitle"
                    :isDark="uiStore.isDark"
                    @select="drillDown"
                />

                <div class="p-5 rounded-3xl border transition-colors duration-500" 
                     :class="uiStore.isDark ? 'bg-slate-900/50 border-slate-800' : 'bg-white border-slate-200'">
                    <h4 class="text-[10px] uppercase tracking-widest font-black mb-4 opacity-50 italic text-lime-400">Active Solutions ROI</h4>
                    <div class="space-y-4">
                        <div v-for="sol in activeSolutions" :key="sol.name" class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-lime-400/10 flex items-center justify-center">
                                    <i :class="sol.icon" class="text-lime-400 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold" :class="uiStore.isDark ? 'text-white' : 'text-slate-800'">{{ sol.name }}</p>
                                    <p class="text-[9px] opacity-50">{{ sol.partners }} Partners</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-mono font-bold text-lime-400">{{ sol.conversion }}%</p>
                                <p class="text-[8px] opacity-40">Conv.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/useUIStore"; 
import { useContextStore } from "@/Kubix/core/stores/contextStore";
import { useTerritoryStore } from "@/Kubix/core/stores/territoryStore";
import { useDashboardStore } from "../../state/dashboard";
import { getDashboardData, loadGeoJSON } from "../../fakeDashboardDB";

import WorldMap from "@/Kubix/Pwa/shared/maps/WorldMap.vue";
import KpiGrid from "@/Kubix/Pwa/shared/kpis/KpiGrid.vue";
import DistributionCard from "../../components/DistributionCard.vue";

const uiStore = useUIStore();
const contextStore = useContextStore();
const territoriesStore = useTerritoryStore();
const dashboard = useDashboardStore();

const isLoadingGeo = ref(false);
const mapAreas = ref([]);
const activeSolutions = ref([
    { name: 'Alugapp', partners: 25, conversion: 25, icon: 'fa-solid fa-key' },
    { name: 'Kubix Pay', partners: 12, conversion: 12, icon: 'fa-solid fa-wallet' }
]);

const breadcrumb = computed(() => dashboard.breadcrumb);

/**
 * ✅ CORREGIDO: Usar contextStore.activeCountry en lugar de auth.activeCountry
 * contextStore.activeCountry es donde se sincroniza el país seleccionado en el Header
 */
const activeCountryCode = computed(() => contextStore.activeCountry?.code || "GLOBAL");

const dashboardData = computed(() => {
    return getDashboardData({
        level: dashboard.level,
        country: activeCountryCode.value,
        ...dashboard.context
    });
});

const kpis = computed(() => dashboardData.value.kpis || []);
const mapPoints = computed(() => dashboardData.value.mapPoints || []);
const distribution = computed(() => dashboardData.value.distribution || []);

/**
 * ✅ CORREGIDO: availableStates viene de territoriesStore.regions
 * que se carga cuando cambia el país en el Header
 */
const availableStates = computed(() => {
    if (activeCountryCode.value === "GLOBAL") return [];
    return territoriesStore.regions || [];
});

/**
 * ✅ CORREGIDO: availableCities viene de territoriesStore.cities
 * que se carga cuando seleccionas un estado
 */
const availableCities = computed(() => {
    if (!dashboard.context.state) return [];
    return territoriesStore.cities || [];
});

/**
 * ✅ NUEVO: Cargar GeoJSON según nivel y contexto actual
 */
async function loadGeoJson() {
    isLoadingGeo.value = true;
    try {
        const areas = await loadGeoJSON({
            level: dashboard.level,
            country: activeCountryCode.value,
            ...dashboard.context
        });
        mapAreas.value = areas || [];
    } catch (err) {
        console.error("Error cargando GeoJSON:", err);
    } finally {
        isLoadingGeo.value = false;
    }
}

/**
 * ✅ Manejar cambio de estado
 * Cargar ciudades del estado seleccionado
 */
async function handleStateChange(value) {
    if (!value) { 
        dashboard.reset(); 
        return; 
    }
    const stateObj = availableStates.value.find((s) => s.code === value);
    dashboard.setState(value, stateObj?.name);
    
    // Cargar ciudades del estado
    if (stateObj?.id) {
        await territoriesStore.loadCities(stateObj.id);
    }
}

/**
 * ✅ Manejar cambio de ciudad
 */
function handleCityChange(value) {
    if (!value) return;
    dashboard.setCity(value);
}

/**
 * ✅ Drill down en la distribución
 */
function drillDown(row) {
    const levelMap = { 
        Global: "Country", 
        Country: "State", 
        State: "City", 
        City: "Neighborhood" 
    };
    const nextLevel = levelMap[dashboard.level];

    if (nextLevel === "State") {
        handleStateChange(row.id || row.code);
    } else if (nextLevel === "City") {
        handleCityChange(row.name || row.city);
    } else if (nextLevel === "Neighborhood") {
        dashboard.setNeighborhood(row.name || row.id);
    }
}

/**
 * ✅ Manejar selección en mapa
 */
function handleMapSelect(payload) {
    if (payload.type === "area") drillDown(payload.data);
}

/**
 * ✅ Navegar a un nivel específico
 */
function goLevel(index) {
    const levels = ["Global", "Country", "State", "City", "Neighborhood"];
    dashboard.setLevel(levels[index]);
}

/**
 * ✅ Título dinámico según ubicación actual
 */
const currentLocationTitle = computed(() => {
    const ctx = dashboard.context;
    if (ctx.neighborhood) return ctx.neighborhood.toUpperCase();
    if (ctx.city) return ctx.city.toUpperCase();
    if (ctx.state) {
        const stateName = availableStates.value.find(x => x.code === ctx.state)?.name;
        return (stateName || ctx.state).toUpperCase();
    }
    return (contextStore.activeCountry?.name || "GLOBAL").toUpperCase();
});

/**
 * ✅ Título de la tarjeta de distribución
 */
const distributionTitle = computed(() => {
    const titles = { Global: "Países", Country: "Estados", State: "Ciudades", City: "Barrios" };
    return titles[dashboard.level] || "Ubicaciones";
});

/**
 * ✅ NUEVO: Watch para cambio de país (viene del Header)
 * Cuando el usuario cambia país en el Header, esto se ejecuta automáticamente
 */
watch(
    () => activeCountryCode.value,
    async (newCountryCode) => {
        if (!newCountryCode || newCountryCode === "GLOBAL") {
            dashboard.reset();
            mapAreas.value = [];
            return;
        }

        // Cargar regiones del nuevo país
        await territoriesStore.loadRegions(newCountryCode);
        
        // Resetear selecciones de estado/ciudad
        dashboard.reset();
        
        // Cargar GeoJSON del nuevo país
        await loadGeoJson();

        // console.log(`📊 Dashboard: País cambiado a ${newCountryCode}`);
    }
);

/**
 * ✅ NUEVO: Watch para cambio de nivel (breadcrumb)
 * Cargar GeoJSON cuando navegas entre niveles
 */
watch(
    [() => dashboard.level, () => dashboard.context],
    async () => {
        await loadGeoJson();
    },
    { deep: true }
);

/**
 * ✅ NUEVO: Watch para cambio de estado
 * Cargar ciudades cuando seleccionas un estado
 */
watch(
    () => dashboard.context.state,
    async (newState) => {
        if (!newState) {
            // Estado deseleccionado
            return;
        }
        
        const stateObj = availableStates.value.find(s => s.code === newState);
        if (stateObj?.id) {
            await territoriesStore.loadCities(stateObj.id);
        }
    }
);

/**
 * ✅ MEJORADO: onMounted con lógica correcta
 * 1. Inicializar territorios si no están listos
 * 2. Cargar regiones del país activo
 * 3. Cargar GeoJSON
 */
onMounted(async () => {
    // Inicializar territorios si no están listos
    if (!territoriesStore.isReady) {
        await territoriesStore.init();
    }

    // Si hay un país activo diferente de GLOBAL, cargar sus regiones
    const currentCode = activeCountryCode.value;
    if (currentCode && currentCode !== "GLOBAL") {
        await territoriesStore.loadRegions(currentCode);
    }

    // Cargar GeoJSON
    await loadGeoJson();

    // console.log(`📊 Dashboard inicializado para país: ${currentCode}`);
});
</script>

<style scoped>
.custom-select {
    @apply appearance-none bg-slate-900/60 border border-slate-800 text-slate-200 text-[11px] font-bold py-2.5 pl-3 pr-9 rounded-xl focus:border-lime-400/50 focus:ring-1 focus:ring-lime-400/20 outline-none cursor-pointer w-full transition-all;
}
:root:not(.dark) .custom-select {
    @apply bg-slate-100 border-slate-200 text-slate-800 focus:bg-white;
}
.select-icon {
    @apply absolute right-3 top-1/2 -translate-y-1/2 text-[8px] text-slate-600 pointer-events-none group-hover:text-lime-400;
}
</style>