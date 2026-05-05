<template>
    <div
        class="p-6 rounded-2xl border flex flex-col h-full"
        :class="isDark ? 'bg-slate-900/40 border-slate-800' : 'bg-white border-slate-200 shadow-sm'"
    >
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xs uppercase tracking-widest opacity-60 font-mono">
                Distribution: {{ title }}
            </h3>
            <span class="text-[10px] font-mono opacity-40">Total: {{ items.length }}</span>
        </div>

        <div class="space-y-3 flex-grow overflow-hidden">
            <TransitionGroup name="list" tag="div" class="space-y-3">
                <div
                    v-for="row in paginatedItems"
                    :key="row.name + (row.id || '')"
                    @click="$emit('select', row)"
                    class="flex justify-between items-center p-3 rounded-xl border cursor-pointer hover:border-lime-400 transition-all group"
                    :class="isDark ? 'border-slate-800 bg-slate-800/30' : 'border-slate-200 bg-slate-50'"
                >
                    <div>
                        <p class="text-xs font-bold group-hover:text-lime-400 transition-colors">{{ row.name }}</p>
                        <p class="text-[9px] uppercase opacity-50">{{ row.sub }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-mono text-lime-400">
                            {{ formatNumber(row.value) }}
                        </p>
                    </div>
                </div>
            </TransitionGroup>

            <div v-if="items.length === 0" class="flex flex-col items-center justify-center h-40 opacity-20">
                <i class="fa-solid fa-inbox text-2xl mb-2"></i>
                <p class="text-[10px] uppercase tracking-wider italic font-mono">No data in this sector</p>
            </div>
        </div>

        <div v-if="totalPages > 1" class="flex items-center justify-between pt-6 mt-6 border-t border-slate-800/50">
            <button 
                @click="prevPage" 
                :disabled="currentPage === 1"
                class="p-2 rounded-lg border border-slate-700 hover:bg-slate-800 disabled:opacity-20 transition-all"
            >
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
            </button>

            <div class="text-center">
                <p class="text-[10px] font-mono opacity-60 uppercase tracking-tighter">
                    Page {{ currentPage }} / {{ totalPages }}
                </p>
                <div class="flex gap-1 justify-center mt-1">
                    <div 
                        v-for="p in totalPages" :key="p"
                        class="h-1 rounded-full transition-all"
                        :class="p === currentPage ? 'w-4 bg-lime-400' : 'w-1 bg-slate-700'"
                    ></div>
                </div>
            </div>

            <button 
                @click="nextPage" 
                :disabled="currentPage === totalPages"
                class="p-2 rounded-lg border border-slate-700 hover:bg-slate-800 disabled:opacity-20 transition-all"
            >
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    items: { type: Array, required: true },
    title: { type: String, default: '' },
    isDark: { type: Boolean, default: true },
    itemsPerPage: { type: Number, default: 5 }
});

defineEmits(['select']);

const currentPage = ref(1);

const totalPages = computed(() => Math.ceil(props.items.length / props.itemsPerPage));

const paginatedItems = computed(() => {
    const start = (currentPage.value - 1) * props.itemsPerPage;
    return props.items.slice(start, start + props.itemsPerPage);
});

function formatNumber(num) {
    if (typeof num !== 'number') return num;
    return new Intl.NumberFormat("en-US").format(num);
}

const nextPage = () => currentPage.value < totalPages.value && currentPage.value++;
const prevPage = () => currentPage.value > 1 && currentPage.value--;

// Resetear página cuando cambian los items (al navegar por el mapa/filtros)
watch(() => props.items, () => {
    currentPage.value = 1;
});
</script>

<style scoped>
.list-enter-active, .list-leave-active { 
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
}
.list-enter-from, .list-leave-to { 
    opacity: 0; 
    transform: translateX(20px); 
}
.list-move { 
    transition: transform 0.4s ease; 
}
</style>