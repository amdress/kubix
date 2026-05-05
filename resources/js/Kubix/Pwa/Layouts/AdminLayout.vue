<template>
    <div
        :class="[
            'flex h-[100dvh] w-full overflow-hidden transition-colors duration-500',
            isDark
                ? 'bg-slate-950 text-slate-100'
                : 'bg-slate-50 text-slate-900',
        ]"
    >
        <!-- SIDEBAR -->
        <slot name="sidebar">
            <AdminSidebar />
        </slot>

        <!-- MAIN COLUMN -->
        <div class="flex-1 flex flex-col min-w-0 h-full relative">
            <!-- HEADER -->
            <slot name="header" v-if="showHeader">
                <AdminHeader />
            </slot>

            <!-- TERRITORY NAV -->
            <div
                v-if="showTerritoryNav"
                class="bg-slate-950/95 backdrop-blur-md border-b border-slate-800"
            >
                <div class="px-6">
                    <TerritoryNav />
                </div>
            </div>

            <!-- CONTENT -->
            <main class="flex-1 overflow-y-auto scrollbar-hide">
                <div class="kubix-main">
                    <router-view />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useUIStore } from "./store/uiStore";

import AdminSidebar from "./components/admin/AdminSidebar.vue";
import AdminHeader from "./components/admin/AdminHeader.vue";
import TerritoryNav from "./components/admin/TerritoryNav.vue";

const ui = useUIStore();
const route = useRoute();

const isDark = computed(() => ui.isDark);

// Ya lo tenías
const showHeader = computed(() => route.meta.showHeader !== false);

// 🔥 NUEVO
const showTerritoryNav = computed(() => route.meta.usesTerritoryNav === true);
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
