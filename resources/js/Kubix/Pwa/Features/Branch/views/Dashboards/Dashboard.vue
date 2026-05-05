<template>
    <div :class="isDark ? 'dark' : ''">
        <div
            class="min-h-screen bg-zinc-100 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 p-8 font-sans transition-colors duration-300"
        >
            <!-- Dark Mode Toggle -->
            <div class="fixed top-15 right-6 z-50">
                <button
                    @click="toggleDarkMode"
                    class="w-12 h-12 rounded-xl bg-zinc-300 dark:bg-zinc-700 hover:bg-zinc-400 dark:hover:bg-zinc-600 flex items-center justify-center shadow-lg transition-all border border-zinc-400 dark:border-zinc-600"
                >
                    <span class="text-xl">{{ isDark ? "🌙" : "☀️" }}</span>
                </button>
            </div>

            <div class="max-w-[2500px] mx-auto space-y-8">
                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div
                        v-for="(kpi, i) in kpiMetrics"
                        :key="i"
                        class="bg-white/50 dark:bg-zinc-900/50 backdrop-blur border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div
                                class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :class="kpi.iconBg"
                            >
                                <span class="text-2xl">{{ kpi.icon }}</span>
                            </div>
                            <span
                                class="text-xs font-bold px-2 py-1 rounded-full"
                                :class="kpi.trendBg"
                            >
                                {{ kpi.trend }}
                            </span>
                        </div>

                        <p
                            class="text-xs uppercase font-mono tracking-widest text-zinc-500"
                        >
                            {{ kpi.label }}
                        </p>
                        <p
                            class="text-3xl font-black text-zinc-900 dark:text-white"
                        >
                            {{ kpi.value }}
                        </p>
                        <p class="text-xs text-zinc-500">
                            {{ kpi.subtitle }}
                        </p>

                        <div
                            class="mt-4 h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden"
                        >
                            <div
                                class="h-full"
                                :class="kpi.progressBg"
                                :style="{ width: kpi.progress + '%' }"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Main -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Neighborhoods -->
                    <section class="lg:col-span-8 space-y-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-black">
                                Neighborhoods
                            </h2>

                            <input
                                v-model="search"
                                placeholder="Search neighborhoods..."
                                class="px-3 py-2 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700"
                            />
                        </div>

                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4"
                        >
                            <div
                                v-for="n in paginatedNeighborhoods"
                                :key="n.id"
                                class="bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 flex flex-col justify-between"
                            >
                                <h3 class="font-black text-lg mb-1">
                                    🏘️ {{ n.name }}
                                </h3>

                                <p class="text-xs text-zinc-500 mb-2">
                                    Manager: {{ n.manager }}
                                </p>

                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <Metric label="Users" :value="n.users" />
                                    <Metric
                                        label="Companies"
                                        :value="n.companies"
                                    />
                                    <Metric
                                        label="Accounts"
                                        :value="n.accounts"
                                    />
                                </div>

                                <div class="flex flex-wrap gap-1 mb-3">
                                    <span
                                        v-for="(s, idx) in n.solutions"
                                        :key="idx"
                                        class="px-2 py-0.5 text-xs font-bold rounded-md"
                                        :class="
                                            s.active
                                                ? 'bg-green-500/10 text-green-600'
                                                : 'bg-red-500/10 text-red-600'
                                        "
                                    >
                                        {{ s.active ? "🟢" : "🔴" }} {{ s.name }}
                                    </span>
                                </div>

                                <router-link
                                    :to="{
                                        name: 'neighborhood.dashboard',
                                        params: { neighborhood: n.slug },
                                    }"
                                    class="mt-auto w-full text-center py-2 rounded-xl bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 font-black"
                                >
                                    View Neighborhood →
                                </router-link>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="flex justify-between items-center text-sm text-zinc-500"
                        >
                            <button @click="prevPage">Previous</button>
                            <span>Page {{ page }} / {{ totalPages }}</span>
                            <button @click="nextPage">Next</button>
                        </div>
                    </section>

                    <!-- Aside -->
                    <aside class="lg:col-span-4 space-y-6">
                        <!-- Solutions -->
                        <div
                            class="bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6"
                        >
                            <h3 class="font-black mb-4">
                                Solutions Coverage
                            </h3>

                            <div
                                v-for="s in solutions"
                                :key="s.id"
                                class="flex justify-between text-sm mb-2"
                            >
                                <span>{{ s.name }}</span>
                                <span class="font-mono"
                                    >{{ s.coverage }}/{{ totalNeighborhoods
                                    }}</span
                                >
                            </div>
                        </div>

                        <!-- Activity -->
                        <div
                            class="bg-white/50 dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6"
                        >
                            <h3 class="font-black mb-4">Recent Activity</h3>

                            <div
                                v-for="a in activities"
                                :key="a.id"
                                class="text-sm border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-3 last:border-0"
                            >
                                <p class="font-bold">{{ a.title }}</p>
                                <p class="text-xs text-zinc-500">
                                    {{ a.description }}
                                </p>
                                <span class="text-[10px] font-mono">
                                    {{ a.time }}
                                </span>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

/* ------------------ Dark Mode ------------------ */
const isDark = ref(false);
const toggleDarkMode = () => (isDark.value = !isDark.value);

/* ------------------ KPIs ------------------ */
const kpiMetrics = ref([
    {
        icon: "🏘️",
        label: "Active Neighborhoods",
        value: "8 / 12",
        subtitle: "Branch coverage",
        trend: "+12%",
        iconBg: "bg-cyan-500/10",
        trendBg: "bg-cyan-500/10 text-cyan-600",
        progress: 66,
        progressBg: "bg-gradient-to-r from-cyan-500 to-blue-500",
    },
    {
        icon: "👥",
        label: "Users",
        value: "3,482",
        subtitle: "+41 this week",
        trend: "+4.1%",
        iconBg: "bg-violet-500/10",
        trendBg: "bg-violet-500/10 text-violet-600",
        progress: 72,
        progressBg: "bg-gradient-to-r from-violet-500 to-purple-500",
    },
    {
        icon: "🏢",
        label: "Active Companies",
        value: "94",
        subtitle: "Across neighborhoods",
        trend: "+6%",
        iconBg: "bg-green-500/10",
        trendBg: "bg-green-500/10 text-green-600",
        progress: 78,
        progressBg: "bg-gradient-to-r from-green-500 to-emerald-500",
    },
    {
        icon: "💰",
        label: "Branch Revenue",
        value: "$48.2k",
        subtitle: "MRR",
        trend: "+9.8%",
        iconBg: "bg-fuchsia-500/10",
        trendBg: "bg-fuchsia-500/10 text-fuchsia-600",
        progress: 81,
        progressBg: "bg-gradient-to-r from-fuchsia-500 to-pink-500",
    },
    {
        icon: "⚠️",
        label: "Neighborhoods at Risk",
        value: "2",
        subtitle: "Low activity",
        trend: "-1",
        iconBg: "bg-red-500/10",
        trendBg: "bg-red-500/10 text-red-600",
        progress: 20,
        progressBg: "bg-gradient-to-r from-red-500 to-orange-500",
    },
]);

/* ------------------ Neighborhoods ------------------ */
const neighborhoods = ref([
    {
        id: 1,
        name: "Armando Mendes",
        slug: "downtown",
        manager: "Ana Ruiz",
        users: 412,
        companies: 18,
        accounts: 620,
        solutions: [
            { name: "Pagos", active: true },
            { name: "AgendaPro", active: true },
        ],
    },
    {
        id: 2,
        name: "Japim",
        slug: "uptown",
        manager: "Luis Gomez",
        users: 280,
        companies: 12,
        accounts: 410,
        solutions: [
            { name: "Pagos", active: false },
            { name: "Contratos", active: true },
        ],
    },
]);

const search = ref("");
const page = ref(1);
const perPage = 6;

const filtered = computed(() =>
    neighborhoods.value.filter((n) =>
        n.name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);

const totalPages = computed(() =>
    Math.ceil(filtered.value.length / perPage),
);

const paginatedNeighborhoods = computed(() =>
    filtered.value.slice(
        (page.value - 1) * perPage,
        page.value * perPage,
    ),
);

const nextPage = () => page.value < totalPages.value && page.value++;
const prevPage = () => page.value > 1 && page.value--;

/* ------------------ Solutions ------------------ */
const totalNeighborhoods = neighborhoods.value.length;

const solutions = ref([
    { id: 1, name: "Pagos", coverage: 1 },
    { id: 2, name: "AgendaPro", coverage: 1 },
    { id: 3, name: "Contratos", coverage: 1 },
]);

/* ------------------ Activity ------------------ */
const activities = ref([
    {
        id: 1,
        title: "Pagos activated",
        description: "Downtown enabled Pagos",
        time: "10 min ago",
    },
    {
        id: 2,
        title: "Low activity warning",
        description: "Uptown users dropped 12%",
        time: "1 hour ago",
    },
]);
</script>
