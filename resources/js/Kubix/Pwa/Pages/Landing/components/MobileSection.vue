<template>
    <section
        class="py-40 px-6 relative overflow-hidden border-y border-white/5 bg-slate-950"
    >
        <div
            class="absolute inset-0 pointer-events-none transition-colors duration-1000"
            :style="{
                background: `radial-gradient(circle at 30% 50%, ${brandColor}0D 0%, transparent 70%)`,
            }"
        />

        <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-20 items-center">
            <div class="flex justify-center relative">
                <div
                    class="absolute w-64 h-64 rounded-full blur-[80px] opacity-20 transition-colors duration-1000"
                    :style="{ backgroundColor: brandColor }"
                />

                <div class="relative w-64 z-10">
                    <div
                        class="w-full aspect-[9/19] rounded-[3rem] border-2 border-white/10 bg-[#0a0c14] overflow-hidden transition-shadow duration-700"
                        :style="{ boxShadow: `0 0 80px -20px ${brandColor}66` }"
                    >
                        <div
                            class="w-full h-full flex flex-col items-center justify-center bg-[#0f172a] relative overflow-hidden"
                        >
                            <div
                                v-for="i in 6"
                                :key="i"
                                class="absolute rounded-sm opacity-10"
                                :style="mockCubeStyle(i)"
                            />
                            <img
                                src="@/assets/images/shared/logo_transparent.png"
                                alt="Kubix"
                                class="w-16 h-16 object-contain mb-4 animate-pulse"
                            />
                            <p
                                class="text-white font-black text-lg tracking-[0.3em]"
                            >
                                KUBIX
                            </p>
                            <p
                                class="text-slate-500 text-[8px] tracking-widest mt-1 uppercase"
                            >
                                by Drunk-Code
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="absolute left-[-20px] top-1/4 px-4 py-3 rounded-2xl bg-[#0f172a]/90 backdrop-blur-xl border border-white/10 shadow-xl animate-float"
                >
                    <p
                        class="text-[8px] text-slate-500 uppercase tracking-widest"
                    >
                        {{ t("landing.mobile.badges.territory") }}
                    </p>
                    <p class="text-xs font-black text-white uppercase">
                        {{ displayLocation }}
                    </p>
                </div>

                <div
                    class="absolute right-[-20px] bottom-1/4 px-4 py-3 rounded-2xl bg-[#0f172a]/90 backdrop-blur-xl border border-white/10 shadow-xl animate-float-delayed"
                >
                    <p
                        class="text-[8px] text-slate-500 uppercase tracking-widest"
                    >
                        {{ t("landing.mobile.badges.status") }}
                    </p>
                    <p class="text-xs font-black text-emerald-400">
                        +{{ MARKETING_STATS.companies }}
                        {{ t("landing.mobile.badges.active") }}
                    </p>
                </div>
            </div>

            <div>
                <span
                    class="text-[10px] font-black uppercase tracking-[0.5em] mb-4 block"
                    :style="{ color: brandColor }"
                >
                    {{ t("landing.mobile.tag") }}
                </span>

                <h2
                    class="text-5xl md:text-6xl font-black leading-none tracking-tighter text-white mb-8"
                >
                    <span class="block mb-2 uppercase"
                        >{{ displayLocation }}
                        {{ t("landing.mobile.connector") }}</span
                    >
                    <span class="text-slate-600 font-thin italic">{{
                        t("landing.mobile.title_2")
                    }}</span>
                </h2>

                <p
                    class="text-slate-400 text-lg font-light leading-relaxed mb-12"
                >
                    {{ t("landing.mobile.subtitle") }}
                </p>

                <div class="space-y-6">
                    <div
                        v-for="(step, index) in steps"
                        :key="index"
                        class="flex items-center gap-5 group"
                    >
                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center border border-white/10 bg-white/5 group-hover:border-blue-500/30 transition-all shrink-0"
                            :style="{ '--hover-bg': `${brandColor}20` }"
                        >
                            <component
                                :is="step.icon"
                                size="18"
                                :style="{ color: brandColor }"
                            />
                        </div>
                        <div>
                            <p
                                class="text-sm font-black text-white uppercase tracking-wider"
                            >
                                {{
                                    t(`landing.mobile.steps.${step.key}.label`)
                                }}
                            </p>
                            <p
                                class="text-xs text-slate-500 font-light leading-snug"
                            >
                                {{ t(`landing.mobile.steps.${step.key}.desc`) }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <button
                        class="px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all duration-300 hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center gap-3 text-white"
                        :style="{
                            backgroundColor: brandColor,
                            boxShadow: `0 10px 30px -10px ${brandColor}80`,
                        }"
                    >
                        <PhRocketLaunch size="18" weight="fill" />
                        {{ t("landing.mobile.cta_main") }}
                    </button>

                    <button
                        class="px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs border border-white/10 bg-white/5 text-slate-300 hover:bg-white/10 transition-all flex items-center justify-center gap-3"
                    >
                        <PhShareNetwork size="18" />
                        {{ t("landing.mobile.cta_share") }}
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, markRaw } from "vue";
import { useI18n } from "vue-i18n";
import { useContextStore } from "@/Kubix/core/stores/contextStore";
import { MARKETING_STATS } from "../config/marketingStats";
import {
    PhShareNetwork,
    PhChartLine,
    PhCalendar,
    PhRocketLaunch,
    PhStorefront,
} from "@phosphor-icons/vue";

const { t } = useI18n({ useScope: 'global' })
const contextStore = useContextStore();

const brandColor = computed(
    () => contextStore.currentBranding?.primaryColor || "#3b82f6",
);

// Computada para manejar el nombre del lugar de forma segura y en mayúsculas
const displayLocation = computed(() => {
    return (
        contextStore.currentName || t("landing.mobile.default_place")
    ).toUpperCase();
});

const mockCubeStyle = (i) => ({
    left: `${(i * 15) % 90}%`,
    bottom: `-10px`,
    width: `${6 + (i % 3) * 4}px`,
    height: `${6 + (i % 3) * 4}px`,
    background: brandColor.value,
    animation: `floatUp ${3 + (i % 2)}s ${i * 0.5}s infinite ease-in-out`,
});

const steps = [
    { icon: markRaw(PhChartLine), key: "manage" },
    { icon: markRaw(PhCalendar), key: "plan" },
    { icon: markRaw(PhRocketLaunch), key: "expand" },
    { icon: markRaw(PhStorefront), key: "reach" },
];
</script>

<style scoped>
@keyframes floatUp {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 0.15;
    }
    90% {
        opacity: 0.08;
    }
    100% {
        transform: translateY(-300px) rotate(360deg);
        opacity: 0;
    }
}
@keyframes dotBounce {
    0%,
    80%,
    100% {
        transform: scale(0);
        opacity: 0.3;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}
@keyframes float {
    0%,
    100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
}
.animate-float {
    animation: float 4s ease-in-out infinite;
}
.animate-float-delayed {
    animation: float 4s ease-in-out infinite 2s;
}
.group:hover div:first-child {
    background-color: var(--hover-bg);
}
</style>
