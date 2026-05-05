<template>
    <section class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black tracking-tighter uppercase">
                {{ title }}
            </h3>
            <div v-if="items.length > 1" class="flex gap-1">
                <div
                    v-for="(_, i) in items"
                    :key="i"
                    class="h-1 rounded-full transition-all duration-500"
                    :class="
                        i === currentIndex
                            ? 'w-4 bg-blue-600'
                            : 'w-1 bg-slate-200'
                    "
                ></div>
            </div>
        </div>

        <div
            v-if="stats.totalCount > 0"
            class="flex items-center gap-8 mb-10 bg-slate-50 p-6 rounded-[2rem]"
        >
            <div class="text-center">
                <span
                    class="text-5xl font-black leading-none tracking-tighter text-slate-900"
                >
                    {{ stats.average }}
                </span>
                <div class="flex justify-center gap-0.5 mt-2 mb-1">
                    <PhStar
                        v-for="n in 5"
                        :key="n"
                        size="10"
                        weight="fill"
                        :class="
                            n <= Math.round(stats.average)
                                ? 'text-amber-400'
                                : 'text-slate-200'
                        "
                    />
                </div>
                <p
                    class="text-[9px] font-black opacity-30 uppercase tracking-widest"
                >
                    {{ stats.totalCount }} avaliações
                </p>
            </div>

            <div class="flex-1 flex flex-col gap-1.5">
                <div
                    v-for="star in [5, 4, 3, 2, 1]"
                    :key="star"
                    class="flex items-center gap-3"
                >
                    <span class="text-[10px] font-black opacity-40 w-2">{{
                        star
                    }}</span>
                    <div
                        class="h-1.5 flex-1 bg-slate-300 rounded-full overflow-hidden"
                    >
                        <div
                            class="h-full bg-blue-500 rounded-full transition-all duration-1000"
                            :style="{
                                width: `${(stats.distribution[star] / stats.totalCount) * 100}%`,
                            }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="items.length > 0" class="relative min-h-[160px]">
            <transition name="slide-fade" mode="out-in">
                <article :key="currentIndex" class="w-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <img
                                :src="items[currentIndex].avatar"
                                class="h-10 w-10 rounded-2xl object-cover shadow-sm bg-slate-200"
                            />
                            <div>
                                <span class="text-sm font-black block">{{
                                    items[currentIndex].name
                                }}</span>
                                <div class="flex gap-0.5 mt-0.5">
                                    <PhStar
                                        v-for="n in 5"
                                        :key="n"
                                        size="8"
                                        weight="fill"
                                        :class="
                                            n <= items[currentIndex].rating
                                                ? 'text-amber-400'
                                                : 'text-slate-200'
                                        "
                                    />
                                </div>
                            </div>
                        </div>
                        <span
                            class="text-[10px] font-bold opacity-30 uppercase tracking-widest"
                        >
                            {{ items[currentIndex].date || "Recente" }}
                        </span>
                    </div>

                    <p
                        class="text-sm leading-relaxed text-slate-600 italic text-pretty"
                    >
                        "{{ items[currentIndex].comment }}"
                    </p>

                    <div
                        v-if="items[currentIndex].helpfulCount"
                        class="mt-4 flex items-center gap-2"
                    >
                        <div class="h-[1px] w-4 bg-slate-200"></div>
                        <p
                            class="text-[9px] font-black opacity-40 uppercase tracking-tighter"
                        >
                            {{ items[currentIndex].helpfulCount }} pessoas
                            acharam útil
                        </p>
                    </div>
                </article>
            </transition>
        </div>

        <div
            v-else
            class="py-10 text-center border-2 border-dashed border-slate-100 rounded-[2.5rem]"
        >
            <div
                class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
            >
                <PhChatTeardropDots size="32" class="opacity-20" />
            </div>
            <p class="text-sm font-black opacity-60">Ainda não há avaliações</p>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { PhStar, PhChatTeardropDots } from "@phosphor-icons/vue";

const props = defineProps({
    title: { type: String, default: "Notas e avaliações" },
    items: { type: Array, required: true },
    autoPlayInterval: { type: Number, default: 2500 }, // 2.5 segundos
});

const currentIndex = ref(0);
let timer = null;

// Lógica del Slider
const startTimer = () => {
    if (props.items.length > 1) {
        timer = setInterval(() => {
            currentIndex.value = (currentIndex.value + 1) % props.items.length;
        }, props.autoPlayInterval);
    }
};

const stopTimer = () => {
    if (timer) clearInterval(timer);
};

onMounted(startTimer);
onUnmounted(stopTimer);

// Estadísticas Proporcionales
const stats = computed(() => {
    const total = props.items.length;
    if (total === 0)
        return {
            average: 0,
            totalCount: 0,
            distribution: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 },
        };

    const sum = props.items.reduce((acc, t) => acc + t.rating, 0);
    const distribution = props.items.reduce(
        (acc, t) => {
            const r = Math.round(t.rating);
            if (r >= 1 && r <= 5) acc[r] = (acc[r] || 0) + 1;
            return acc;
        },
        { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 },
    );

    return {
        average: (sum / total).toFixed(1),
        totalCount: total,
        distribution,
    };
});
</script>

<style scoped>
.slide-fade-enter-active {
    transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-fade-leave-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
    filter: blur(4px);
}
.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-20px);
    filter: blur(4px);
}
</style>
