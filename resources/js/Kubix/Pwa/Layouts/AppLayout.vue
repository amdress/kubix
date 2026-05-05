<template>
    <div
        :class="[
            'flex h-[100dvh] w-full overflow-hidden transition-colors duration-500 ',
            isDark
                ? 'bg-slate-950 text-slate-100'
                : 'bg-slate-50 text-slate-900',
        ]"
    >
        <slot name="sidebar">
            <Sidebar />
        </slot>

        <div class="flex-1 flex flex-col min-w-0 h-full relative">
            <slot name="header" v-if="showHeader">
                <Header />
            </slot>

            <main
                class="flex-1 overflow-y-auto scrollbar-hide"
                :class="{ 'pb-24 lg:pb-8': showTabs }"
            >
                <div class="kubix-main">
                    <router-view />
                </div>
            </main>

            <slot name="tabs" v-if="showTabs">
                <MobileTabs
                    class="lg:hidden fixed bottom-0 left-0 right-0 z-[90]"
                    @action:camera="ui.openCamera"
                />
            </slot>
        </div>
   

    <transition name="fade">
        <ScannerOverlay
            v-if="ui.isCameraOpen"
            class="z-[100]"
            @close="ui.closeCamera"
            @result="onScanResult"
        />
    </transition>
   </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/uiStore";

// UI Components
import Sidebar from "./components/Sidebar.vue";
import Header from "./components/Header.vue";
import MobileTabs from "./components/mobile/MobileTabs.vue";
import ScannerOverlay from "./components/scanner/ScannerOverlay.vue";

// Logic
import { resolveQR } from "./components/scanner/qrResolver";

const ui = useUIStore();
const route = useRoute();

// Estado reactivo
const isDark = computed(() => ui.isDark);

// Control por metadatos de ruta (permite ocultar elementos en Login o Mapas)
const showHeader = computed(() => route.meta.showHeader !== false);
const showTabs = computed(() => route.meta.showTabs !== false);

/**
 * 📷 SCAN HANDLER
 */
const onScanResult = async (qr) => {
    try {
        const result = await resolveQR(qr);
        console.log("[KUBIX QR]", result);

        // Aquí puedes manejar la navegación o el feedback
        ui.closeCamera();
    } catch (err) {
        // IMPORTANTE: Feedback en Português para el usuario
        console.error("[QR ERROR]", err);
    }
};
</script>

<style scoped>
/* Ocultar scrollbar pero mantener scroll funcional */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Transición de páginas suave */
.page-fade-enter-active,
.page-fade-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.page-fade-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.page-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Transición simple para el Scanner */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
