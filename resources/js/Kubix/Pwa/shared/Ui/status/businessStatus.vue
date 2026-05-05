<template>
    <div class="relative group/container w-full bg-transparent overflow-hidden">
        <button
            @click="scroll('left')"
            class="nav-btn left-2"
            aria-label="Anterior"
        >
            <PhCaretLeft :size="16" weight="bold" />
        </button>
        <button
            @click="scroll('right')"
            class="nav-btn right-2"
            aria-label="Próximo"
        >
            <PhCaretRight :size="16" weight="bold" />
        </button>

        <div ref="scrollContainer" class="stories-viewport scrollbar-hide">
            <div
                v-for="(brand, i) in brands"
                :key="i"
                @click="$emit('select', brand)"
                class="story-business group"
            >
                <div :class="['ring-status', getStatusColor(brand.status)]">
                    <div class="inner-avatar">
                        <img
                            :src="brand.logo"
                            :alt="brand.name"
                            @error="
                                (e) =>
                                    (e.target.src = `https://ui-avatars.com/api/?name=${brand.name}&background=f1f5f9&color=64748b`)
                            "
                            class="business-logo"
                        />
                    </div>

                    <div
                        v-if="brand.status !== 3"
                        :class="['status-dot', getDotColor(brand.status)]"
                    ></div>
                </div>

                <span class="business-name">{{ brand.name }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { PhCaretLeft, PhCaretRight } from "@phosphor-icons/vue";

// Definición de Props y Emits
defineProps({
    brands: {
        type: Array,
        required: true,
    },
});

defineEmits(["select"]);

const scrollContainer = ref(null);

// Lógica de Scroll
const scroll = (direction) => {
    if (scrollContainer.value) {
        const amount = direction === "left" ? -280 : 280;
        scrollContainer.value.scrollBy({ left: amount, behavior: "smooth" });
    }
};

// Helpers de Estilo
const getStatusColor = (status) => {
    switch (status) {
        case 1:
            return "border-emerald-500 shadow-lg shadow-emerald-500/10"; // Abierto
        case 2:
            return "border-amber-500 shadow-lg shadow-amber-500/10"; // Cerrando/Ocupado
        default:
            return "border-slate-200 opacity-50 grayscale"; // Cerrado
    }
};

const getDotColor = (status) => {
    return status === 1 ? "bg-emerald-500 animate-pulse" : "bg-amber-500";
};
</script>

<style scoped>
.stories-viewport {
    @apply flex gap-4 overflow-x-auto px-6 py-2 scroll-smooth select-none;
    -webkit-overflow-scrolling: touch;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.story-business {
    @apply flex flex-col items-center gap-2 min-w-[72px] shrink-0 cursor-pointer;
}

/* El Ring KUBIX */
.ring-status {
    @apply relative w-[68px] h-[68px] rounded-[1.6rem] border-[2.5px] p-1 
         transition-all duration-300 active:scale-90 group-hover:scale-105;
}

.inner-avatar {
    @apply w-full h-full rounded-[1.2rem] bg-white overflow-hidden flex items-center justify-center shadow-inner;
}

.business-logo {
    @apply w-full h-full object-contain p-2 transition-transform duration-500 group-hover:rotate-6;
}

/* Punto de Pulso */
.status-dot {
    @apply absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full border-[3px] border-white z-20;
}

.business-name {
    @apply text-[9px] font-black tracking-tighter text-center truncate w-full 
         uppercase text-slate-400 group-hover:text-slate-900 transition-colors;
}

/* Botones de Navegación */
.nav-btn {
    @apply absolute top-[28px] z-30 w-7 h-7 rounded-full 
         bg-white/90 shadow-lg border border-slate-100 flex items-center justify-center
         opacity-0 group-hover/container:opacity-100 transition-opacity
         hover:bg-slate-900 hover:text-white hidden md:flex;
}
</style>
