<template>
    <div
        class="max-w-[480px] md:max-w-[1024px] mx-auto min-h-screen bg-slate-50/30 md:bg-white md:shadow-2xl md:shadow-slate-200 pb-12 relative"
    >
        <button
            @click="goToConfig"
            class="absolute top-4 right-4 z-[300] p-3 bg-white/20 backdrop-blur-md hover:bg-white/40 rounded-full transition-all active:scale-90 border border-white/20 shadow-lg"
        >
            <PhGearSix
                :size="28"
                weight="light"
                class="text-white md:text-slate-800"
            />
        </button>

        <ProfileHero
            :coverImage="biz.cover"
            :avatarImage="biz.logo"
            :title="biz.name"
            :subtitle="biz.category"
        />

        <div class="h-8"></div>
        <div class="px-4 md:px-8 mt-10">
            <div class="flex flex-col gap-2 mb-6">
                <h3
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-2"
                >
                    Gestão de Unidades
                </h3>
                <header
                    class="bg-white p-4 rounded-[2rem] border border-slate-100 flex items-center justify-between shadow-sm"
                >
                    <div
                        class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100"
                    >
                        <select
                            v-model="selectedUnit"
                            class="text-[11px] font-black uppercase tracking-wider bg-transparent border-none p-0 pr-6 focus:ring-0 appearance-none cursor-pointer text-slate-700"
                        >
                            <option value="all">Geral (Todas)</option>
                            <option
                                v-for="unit in units"
                                :key="unit.id"
                                :value="unit.id"
                            >
                                {{ unit.name }}
                            </option>
                        </select>
                    </div>

                    <button
                        @click="isOpen = !isOpen"
                        class="flex items-center gap-2 px-4 py-2 rounded-2xl transition-all font-black text-[11px] uppercase tracking-tighter"
                        :class="
                            isOpen
                                ? 'bg-emerald-50 text-emerald-600'
                                : 'bg-rose-50 text-rose-600'
                        "
                    >
                        <div
                            class="w-2 h-2 rounded-full animate-pulse"
                            :class="isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                        ></div>
                        {{ isOpen ? "Aberto" : "Fechado" }}
                    </button>
                </header>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-8">
                    <section
                        class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col min-h-[420px]"
                    >
                        <div
                            class="flex justify-between items-center mb-8 gap-4 flex-wrap"
                        >
                            <div
                                class="bg-slate-100 p-1 rounded-2xl flex gap-1"
                            >
                                <button
                                    v-for="type in [
                                        { id: 'gauge', label: 'Views Profile' },
                                        {
                                            id: 'sparkline',
                                            label: 'Performance',
                                        },
                                    ]"
                                    :key="type.id"
                                    @click="activeChart = type.id"
                                    :class="
                                        activeChart === type.id
                                            ? 'bg-white shadow-sm text-blue-600'
                                            : 'text-slate-400'
                                    "
                                    class="px-5 py-2 rounded-xl text-[9px] font-black uppercase transition-all"
                                >
                                    {{ type.label }}
                                </button>
                            </div>

                            <div
                                class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-100"
                            >
                                <select
                                    v-model="currentTimeFrame"
                                    class="text-[10px] font-black text-slate-500 uppercase italic bg-transparent border-none p-0 pr-4 focus:ring-0 appearance-none cursor-pointer"
                                >
                                    <option
                                        v-for="t in timeFrames"
                                        :key="t.val"
                                        :value="t.val"
                                    >
                                        {{ t.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div
                            class="flex-1 flex items-center justify-center relative overflow-hidden"
                        >
                            <KubeGauge
                                v-if="activeChart === 'gauge'"
                                :value="currentDisplayData.score"
                                class="w-full h-64 scale-110 transition-all duration-700"
                            />
                            <KubeSparkline
                                v-else
                                :data="currentDisplayData.chart"
                                class="w-full h-64 transition-all duration-700"
                            />
                        </div>
                    </section>
                </div>

                <div
                    class="md:col-span-4 grid grid-cols-2 md:grid-cols-1 gap-4"
                >
                    <div
                        v-for="stat in currentDisplayData.stats"
                        :key="stat.label"
                        class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:border-blue-200 transition-all group"
                    >
                        <p
                            class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 group-hover:text-blue-500 transition-colors"
                        >
                            {{ stat.label }}
                        </p>
                        <div class="flex items-end justify-between">
                            <h4
                                class="text-3xl font-black text-slate-900 tracking-tighter leading-none"
                            >
                                {{ stat.value }}
                            </h4>
                            <span
                                :class="
                                    stat.isWA
                                        ? 'text-emerald-600 bg-emerald-50'
                                        : 'text-blue-600 bg-blue-50'
                                "
                                class="text-[8px] px-2 py-1 rounded-lg font-black uppercase tracking-tighter"
                            >
                                {{
                                    stat.isWA ? "WhatsApp" : `↑${stat.growth}%`
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12">
            <div class="px-8 flex items-center justify-between mb-4">
                <h2
                    class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]"
                >
                    Sugestões de Fornecedores
                </h2>
                <button
                    class="text-[10px] font-black text-blue-600 uppercase tracking-tighter border-b-2 border-blue-100 pb-0.5"
                >
                    Ver Todos
                </button>
            </div>
            <div class="bg-white py-6 border-y border-slate-50">
                <BusinessStatus :brands="b2bPartners" @select="toGo" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import { useRouter } from "vue-router";
import KubeGauge from "@/Kubix/Pwa/shared/Ui/stats/KubeGauge.vue";
import KubeSparkline from "@/Kubix/Pwa/shared/Ui/stats/KubeSparkline.vue";
import BusinessStatus from "@/Kubix/Pwa/shared/Ui/status/businessStatus.vue";
import ProfileHero from "@/Kubix/Pwa/shared/Ui/Parts/ProfileHero.vue";
import { PhGearSix } from "@phosphor-icons/vue";

const router = useRouter();

// --- ESTADO UI ---
const activeChart = ref("gauge");
const selectedUnit = ref("all");
const currentTimeFrame = ref(7);
const isOpen = ref(true);

const business = reactive({ name: "Juan Pinchos", category: "Gastronomia" });

const units = [
    { id: "batel", name: "Unidade Batel" },
    { id: "capao", name: "Unidade Capão Raso" },
];

const timeFrames = [
    { label: "7 Dias", val: 7 },
    { label: "15 Dias", val: 15 },
    { label: "30 Dias", val: 30 },
];

// --- DATA PARA EL BUSINESS STATUS (B2B PARTNERS) ---
const b2bPartners = ref([
    {
        id: "dist-1",
        name: "Master Distrib",
        logo: "https://logo.clearbit.com/nestle.com",
        status: 1,
    },
    {
        id: "pack-2",
        name: "EcoPack BR",
        logo: "https://logo.clearbit.com/tetrapak.com",
        status: 1,
    },
    {
        id: "gelo-3",
        name: "Gelo Express",
        logo: "https://logo.clearbit.com/unilever.com",
        status: 2,
    },
    {
        id: "limp-4",
        name: "Clean Corp",
        logo: "https://logo.clearbit.com/clorox.com",
        status: 1,
    },
    {
        id: "soft-5",
        name: "Kubix HQ",
        logo: "https://logo.clearbit.com/sap.com",
        status: 3,
    },
]);

const biz = {
    name: "Nova Terra Empanada",
    category: "Cozinha Artesanal",
    isOpen: true,
    rating: 4.9,
    phone: "+5511999999999",
    reviews: 312,
    responseTime: "Responde rápido",
    logo: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=300",
    cover: "https://images.unsplash.com/photo-1623341214825-9f4f963727da?w=1200",
    services: ["Pix", "Entrega", "Wi-Fi", "Pet-Friendly"],
    social: {
        instagram: "https://instagram.com",
        facebook: "https://facebook.com",
        x: "https://x.com",
    },
    testimonials: [
        {
            name: "Carlos M.",
            rating: 5,
            comment:
                "Melhor empanada da região, atendimento rápido e sabor incrível.",
            avatar: "https://i.pravatar.cc/100?img=1",
        },
        {
            name: "Ana Paula",
            rating: 5,
            comment: "Lugar super agradável, recomendo demais!",
            avatar: "https://i.pravatar.cc/100?img=2",
        },
        {
            name: "João R.",
            rating: 4,
            comment: "Muito bom, só demorou um pouco no horário de pico.",
            avatar: "https://i.pravatar.cc/100?img=3",
        },
    ],
    fullSchedule: [
        { label: "Segunda", time: "Fechado" },
        { label: "Terça", time: "08:00 - 22:00" },
        { label: "Quarta", time: "08:00 - 22:00" },
        { label: "Quinta", time: "08:00 - 22:00" },
        { label: "Sexta", time: "08:00 - 23:00" },
        { label: "Sábado", time: "11:00 - 23:00" },
        { label: "Domingo", time: "11:00 - 17:00" },
    ],
};

// Función de redirección
const toGo = (partner) => {
    console.log("Navegando a:", partner.name);
    router.push({
        name: "workspace.public.businessSplash",
        // params: { id: partner.id },
    });
};

const goToConfig = () => {
    router.push({
        name: "workspace.admin.businessConfig",
    });
};

// --- MOCK DATABASE ---
const rawData = {
    all: {
        7: {
            score: 92,
            chart: [30, 45, 32, 60, 55, 80, 95],
            stats: [
                { label: "Views", value: "12k", growth: 12 },
                { label: "WhatsApp", value: "450", isWA: true },
                { label: "Favs", value: "120", growth: 5 },
                { label: "Likes", value: "2k", growth: 8 },
            ],
        },
        30: {
            score: 88,
            chart: [100, 120, 90, 150, 200, 180, 250],
            stats: [
                { label: "Views", value: "55k", growth: 18 },
                { label: "WhatsApp", value: "1.8k", isWA: true },
                { label: "Favs", value: "600", growth: 12 },
                { label: "Likes", value: "9k", growth: 15 },
            ],
        },
    },
    batel: {
        7: {
            score: 98,
            chart: [10, 20, 15, 30, 25, 40, 50],
            stats: [
                { label: "Views", value: "4k", growth: 8 },
                { label: "WhatsApp", value: "120", isWA: true },
                { label: "Favs", value: "45", growth: 2 },
                { label: "Likes", value: "800", growth: 5 },
            ],
        },
    },
};

// Lógica de filtrado de datos
const currentDisplayData = computed(() => {
    const unitData = rawData[selectedUnit.value] || rawData.all;
    return unitData[currentTimeFrame.value] || unitData[7] || unitData[30];
});
</script>

<style scoped>
/* Transiciones suaves para los gráficos */
.transition-all {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
