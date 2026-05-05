<template>
    <div
        class="max-w-[480px] md:max-w-[1024px] mt-2 mx-auto min-h-screen bg-white shadow-2xl shadow-slate-200"
    >
        <!-- HEADER -->
        <ProfileHero
            :coverImage="biz.cover"
            :avatarImage="biz.logo"
            :title="biz.name"
            :subtitle="biz.category"
            :isDark="ui.isDark"
        />

        <div class="h-16"></div>

        <!-- STATUS + RATING -->
        <section class="px-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-5">
                    <div
                        class="h-2 w-2 rounded-full animate-pulse"
                        :class="biz.isOpen ? 'bg-emerald-500' : 'bg-red-500'"
                    ></div>
                    <span
                        class="text-[11px] font-black uppercase tracking-wider"
                        :class="
                            biz.isOpen ? 'text-emerald-500' : 'text-red-500'
                        "
                    >
                        {{ biz.isOpen ? "Aberto agora" : "Fechado" }}
                    </span>
                </div>

                <div
                    class="flex items-center gap-1.5 border-l border-slate-300 dark:border-slate-800 pl-4"
                >
                    <PhStar weight="fill" class="text-amber-400" :size="14" />
                    <span class="text-sm font-black">{{ biz.rating }}</span>
                    <span class="text-[10px] font-bold opacity-40"
                        >({{ biz.reviews }})</span
                    >
                </div>
            </div>

            <div
                class="flex items-center gap-1.5 text-blue-500 bg-blue-500/10 px-2 py-1 rounded-lg"
            >
                <PhTimer :size="12" weight="bold" />
                <span
                    class="text-[9px] font-black uppercase tracking-tighter"
                    >{{ biz.responseTime }}</span
                >
            </div>
        </section>

        <!-- ACTIONS -->
        <section class="mt-8 px-6 grid grid-cols-4 gap-3">
            <button class="action-card">
                <PhBookOpen
                    :size="22"
                    weight="duotone"
                    class="text-indigo-500"
                />
                <span>Servicio</span>
            </button>
            <button class="action-card">
                <PhMapPin :size="22" weight="duotone" class="text-rose-500" />
                <span>Mapa</span>
            </button>
            <button class="action-card">
                <PhShareNetwork
                    :size="22"
                    weight="duotone"
                    class="text-sky-500"
                />
                <span>Partilhar</span>
            </button>
            <button class="action-card">
                <PhHeart :size="22" weight="duotone" class="text-sky-500" />
                <span>Favorito</span>
            </button>
        </section>
        <section class="p-5">
            <button
                @click="goToDashboard()"
                class="w-full py-5 rounded-[0.2rem] bg-slate-300 text-black text-xs font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 active:scale-[0.98] transition-transform"
            >
                <PhSquaresFour
                    :size="22"
                    weight="duotone"
                    class="text-sky-500"
                />
                Dashboard
            </button>
        </section>

        <!-- DEPOIMENTOS -->
        <StatsTestimonials :items="biz.testimonials" />

        <!-- FACILIDADES -->
        <section class="mt-6 px-6">
            <p class="text-[10px] font-black uppercase opacity-40 mb-3">
                Facilidades
            </p>
            <div class="flex flex-wrap gap-2">
                <span
                    v-for="svc in biz.services"
                    :key="svc"
                    class="text-[10px] font-bold px-3 py-1.5 rounded-full border"
                >
                    {{ svc }}
                </span>
            </div>
        </section>

        <!-- HORARIOS -->
        <section
            class="mt-8 mx-6 rounded-2xl overflow-hidden bg-white shadow-sm border border-slate-200 dark:border-slate-800"
        >
            <button
                @click="isHoursOpen = !isHoursOpen"
                class="w-full p-4 flex items-center justify-between"
            >
                <div class="flex items-center gap-3">
                    <PhClock :size="18" />
                    <div>
                        <p class="text-xs font-black uppercase opacity-40">
                            Horários
                        </p>
                        <p class="text-sm font-bold">Hoje: 08:00 — 22:00</p>
                    </div>
                </div>
                <PhCaretDown
                    :size="18"
                    :class="{ 'rotate-180': isHoursOpen }"
                />
            </button>

            <div v-if="isHoursOpen" class="px-5 pb-4 space-y-2 pt-4">
                <div
                    v-for="day in biz.fullSchedule"
                    :key="day.label"
                    class="flex justify-between text-xs"
                >
                    <span class="opacity-60">{{ day.label }}</span>
                    <span class="font-bold">{{ day.time }}</span>
                </div>
            </div>
        </section>

        <!-- SOCIAL -->
        <footer
            class="mt-10 px-6 pb-0 pt-8 border-t border-slate-100 bg-gray-200 backdrop-blur-sm"
        >
            <div class="flex justify-center items-center gap-5">
                <button
                    @click="openLink(biz.social.instagram)"
                    class="w-12 h-12 rounded-full flex items-center justify-center text-white shadow-lg shadow-pink-500/20 active:scale-90 transition-all bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7]"
                >
                    <PhInstagramLogo size="24" weight="bold" />
                </button>

                <button
                    @click="openLink(biz.social.facebook)"
                    class="w-12 h-12 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-600/20 active:scale-90 transition-all bg-[#1877F2]"
                >
                    <PhFacebookLogo size="24" weight="fill" />
                </button>

                <button
                    @click="openLink(biz.social.tiktok)"
                    class="w-12 h-12 rounded-full flex items-center justify-center bg-black text-white shadow-lg shadow-slate-900/20 active:scale-90 transition-all border-b-2 border-r-2 border-[#ff0050] relative"
                >
                    <div
                        class="absolute inset-0 rounded-full border-t-2 border-l-2 border-[#00f2ea] opacity-70"
                    ></div>
                    <PhTiktokLogo
                        size="24"
                        weight="bold"
                        class="relative z-10"
                    />
                </button>

                <button
                    @click="openLink(biz.social.x)"
                    class="w-12 h-12 rounded-full flex items-center justify-center bg-[#000000] text-white shadow-lg shadow-slate-500/10 active:scale-90 transition-all border border-slate-800"
                >
                    <PhXLogo size="20" weight="bold" />
                </button>
            </div>

            <p
                class="text-center text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] mt-8 opacity-50"
            >
                Conectado via KUBIX Network
            </p>
        </footer>

        <!-- WHATS -->
        <FabWhatsapp
            :phone="biz.phone"
            :message="`Olá ${biz.name},vi seu anuncio na kubixApp, gostaria de fazer um pedido`"
            badge
        />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { useUIStore } from "@/Kubix/Pwa/Layouts/store/uiStore";
import ProfileHero from "@/Kubix/Pwa/shared/Ui/Parts/ProfileHero.vue";
import SliderTestimonial from "@/Kubix/Pwa/shared/Ui/Testimonials/SliderTestimonials.vue";
import StatsTestimonials from "@/Kubix/Pwa/shared/Ui/Testimonials/StatsTestimonials.vue";
import FabWhatsapp from "../../../shared/Ui/Whatsapp/FabWhatsapp.vue";
import {
    PhStar,
    PhBookOpen,
    PhMapPin,
    PhShareNetwork,
    PhTimer,
    PhClock,
    PhCaretDown,
    PhWhatsappLogo,
    PhInstagramLogo,
    PhFacebookLogo,
    PhXLogo,
    PhHeart,
    PhTiktokLogo,
} from "@phosphor-icons/vue";

const ui = useUIStore();
const isHoursOpen = ref(false);
const router = useRouter();

const biz = {
    name: "Pudinho da Kathia",
    category: "Cozinha Artesanal",
    isOpen: true,
    rating: 4.9,
    phone: "+559285219651",
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

const goToDashboard = () => {
    router.push({
        name: "workspace.business.dashboard",
    });
};

const openLink = (url) => {
    if (url) window.open(url, "_blank");
};
</script>

<style scoped>
.action-card {
    @apply flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-white border shadow-sm active:scale-95;
}
.social-circle {
    @apply p-3 rounded-full bg-white dark:bg-slate-900 border active:scale-90;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>


data para crear una empresa 

splashScreen 
logo
name 
ubicacion 
redes sociales ( insta face tiktok ,x )
faciliades  ( pix , entrega , petfriendly , wifi)
contacto whats