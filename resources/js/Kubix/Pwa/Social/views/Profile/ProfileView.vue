<template>
    <div
        class="min-h-screen bg-slate-50 md:bg-white md:shadow-2xl md:shadow-slate-200"
    >
        <div
            class="max-w-[480px] md:max-w-[1024px] mx-auto relative pb-24 bg-white"
        >
            <div class="relative">
                <ProfileHero
                    :coverImage="userData.cover"
                    :avatarImage="userData.avatar"
                    :title="userData.name"
                    :subtitle="userData.description"
                />
                <button
                    @click="openEditForm('profile')"
                    class="absolute top-4 right-4 p-2 bg-black/20 hover:bg-black/40 backdrop-blur-md text-white rounded-full transition-all active:scale-90"
                >
                    <PhPencilSimple size="18" weight="bold" />
                </button>
            </div>

            <div class="h-8"></div>

            <div
                class="mt-8 sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100"
            >
                <div class="px-6 pt-4">
                    <h3
                        class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400"
                    >
                        Sua atividade
                    </h3>
                </div>

                <div
                    class="flex overflow-x-auto justify-evenly scrollbar-hide px-2"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.id"
                        @click="activeTab = tab.id"
                        class="flex flex-col items-center py-4 px-4 min-w-[80px] relative transition-all group"
                        :class="
                            activeTab === tab.id
                                ? 'text-blue-600'
                                : 'text-slate-400'
                        "
                    >
                        <component
                            :is="tab.icon"
                            size="22"
                            :weight="activeTab === tab.id ? 'fill' : 'light'"
                            class="transition-transform group-active:scale-90"
                        />

                        <span
                            class="text-[9px] font-black uppercase tracking-widest mt-1.5"
                        >
                            {{ tab.label }}
                        </span>

                        <div
                            v-if="activeTab === tab.id"
                            class="absolute bottom-0 inset-x-4 h-0.5 bg-blue-600 rounded-full"
                        ></div>
                    </button>
                </div>
            </div>

            <div class="p-4 md:p-6 min-h-[300px]">
                <div v-if="currentActivity.length > 0" class="space-y-3">
                    <div
                        v-for="item in currentActivity"
                        :key="item.id"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 active:scale-[0.98] transition-all"
                    >
                        <img
                            :src="item.businessImg"
                            class="w-12 h-12 rounded-xl object-cover shadow-sm border border-white"
                        />

                        <div class="flex-1">
                            <h5
                                class="text-sm font-black text-slate-900 leading-tight"
                            >
                                {{ item.businessName }}
                            </h5>
                            <p
                                class="text-xs text-slate-500 line-clamp-1 mt-0.5"
                            >
                                {{ item.content }}
                            </p>
                        </div>

                        <span
                            class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter"
                        >
                            {{ item.date }}
                        </span>
                    </div>
                </div>

                <div
                    v-else
                    class="flex flex-col items-center justify-center py-16 text-center"
                >
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100"
                    >
                        <PhGhost size="32" weight="thin" class="opacity-20" />
                    </div>
                    <p
                        class="text-xs font-black uppercase tracking-widest text-slate-400"
                    >
                        Nada por aqui ainda...
                    </p>
                </div>
            </div>

            <div class="px-4 md:px-6 mt-4 space-y-3">
                <button
                    @click="goToWorkspace"
                    class="w-full flex items-center justify-between p-5 rounded-2xl bg-slate-200 text-black shadow-xl shadow-slate-200 active:scale-95 transition-all"
                >
                    <div class="flex items-center gap-4">
                        <div class="p-2 bg-white/10 rounded-lg">
                            <PhStorefront
                                size="20"
                                weight="duotone"
                                class="text-400"
                            />
                        </div>
                        <span
                            class="text-sm font-black tracking-tight uppercase"
                        >
                            Meu Negócio
                        </span>
                    </div>
                    <PhArrowRight size="18" weight="bold" class="opacity-50" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import ProfileHero from "@/Kubix/Pwa/shared/Ui/Parts/ProfileHero.vue";

import {
    PhHeart,
    PhChatCircleText,
    PhStar,
    PhPencilSimple,
    PhStorefront,
    PhArrowRight,
    PhLockKey,
    PhGhost,
    PhThumbsUp,
} from "@phosphor-icons/vue";

// ---------------- STATE ----------------
const activeTab = ref("curtidas");
const router = useRouter();

const userData = ref({
    name: "Amdress Stark",
    description: "Fullstack Architect & Systems Engineer",
    avatar: "https://randomuser.me/api/portraits/men/32.jpg",
    cover: "https://images.unsplash.com/photo-1623341214825-9f4f963727da?w=1200",
});

// ---------------- TABS CONFIG ----------------
const tabs = [
    { id: "curtidas", label: "Curtidas", icon: PhThumbsUp },
    { id: "comentarios", label: "Comentários", icon: PhChatCircleText },
    { id: "avaliacoes", label: "Avaliações", icon: PhStar },
    { id: "favoritos", label: "Favoritos", icon: PhHeart },
];

// ---------------- DATA ----------------
const activityData = ref({
    curtidas: [
        {
            id: 1,
            businessName: "Nova Terra Empanada",
            businessImg:
                "https://images.unsplash.com/photo-1623156346149-d5bc8bd28f44?w=100",
            content: "Você curtiu el anuncio de Empanada de Queijo",
            date: "Hoje",
        },
        {
            id: 2,
            businessName: "Studio Nails",
            businessImg:
                "https://images.unsplash.com/photo-1604654894610-df63bc536371?w=100",
            content: "Você curtiu a promoção de Manicure",
            date: "Ontem",
        },
    ],
    comentarios: [
        {
            id: 3,
            businessName: "Juan Pinchos",
            businessImg:
                "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100",
            content: '"As mejores empanadas do bairro!"',
            date: "2 dias",
        },
    ],
    avaliacoes: [],
    favoritos: [],
});

// ---------------- COMPUTED ----------------
const currentActivity = computed(() => {
    return activityData.value[activeTab.value] || [];
});



// ---------------- ACTIONS ----------------
const openEditForm = (type) => {
    console.log("Editando:", type);
};


const goToWorkspace = () => {
    router.push({ name: "workspace.admin.businessList" });
};


</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Forzar que el sticky funcione en overflow */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
