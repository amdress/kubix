<template>
    <div
        class="max-w-[480px] md:max-w-[1024px] mx-auto min-h-screen bg-white shadow-2xl shadow-slate-200"
    >
        <div class="w-full p-2">
            <header
                class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 px-5 py-6"
            >
                <div class="flex items-center justify-between mb-6">
                    <h1
                        class="text-2xl font-black uppercase tracking-tighter text-slate-900"
                    >
                        O Bairro Agora
                    </h1>
                    <div class="flex gap-2">
                        <span
                            class="flex items-center gap-2 bg-red-50 text-red-500 px-3 py-1.5 rounded-full text-[10px] font-black border border-red-100"
                        >
                            <div
                                class="w-2 h-2 bg-red-500 rounded-full animate-ping"
                            ></div>
                            {{ liveCount }} LIVES
                        </span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button
                        v-for="filter in ['Agora', 'Hoje', 'Fim de Semana']"
                        :key="filter"
                        @click="activeFilter = filter"
                        class="flex-1 py-3 text-[10px] font-black uppercase tracking-widest rounded-2xl transition-all"
                        :class="
                            activeFilter === filter
                                ? 'bg-slate-900 text-white'
                                : 'bg-slate-50 text-slate-400'
                        "
                    >
                        {{ filter }}
                    </button>
                </div>
            </header>

            <section class="p-4 space-y-6">
                <div class="flex items-center justify-between px-2">
                    <h2
                        class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-300"
                    >
                        Próximos Eventos em Batel
                    </h2>
                </div>

                <EventCard
                    v-for="event in fakeEvents"
                    :key="event.id"
                    :data="event"
                    @select="goToEvent"
                />
            </section>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import EventCard from "../../components/cards/EventCard.vue";

const router = useRouter();
const activeFilter = ref("Agora");

const fakeEvents = ref([
    {
        id: 1,
        type: "LIVE", // Layout de 420px de alto, tipo Reel
        title: "Música ao Vivo: Noite do Jazz",
        businessName: "Batel Wine Bar",
        avatar: "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100",
        distance: "400m",
        image: "https://images.unsplash.com/photo-1511192336575-5a79af67a629?w=800",
        businessId: "wine-01",
    },
    {
        id: 2,
        type: "STANDARD", // Layout horizontal con imagen a la izquierda
        title: "Workshop de Cerâmica",
        place: "Ateliê do Bairro",
        distance: "1.2km",
        timeLabel: "Sábado às 14:00",
        image: "https://images.unsplash.com/photo-1525946401-081987513904?w=400",
        businessId: "art-02",
    },
    {
        id: 3,
        type: "FLASH", // Layout de ticket naranja, sin imagen
        title: "Últimas 5 Vagas: Yoga no Parque",
        businessName: "Studio Zen",
        timeLabel: "Começa em 20min",
        businessId: "yoga-03",
    },
]);

const liveCount = computed(
    () => fakeEvents.value.filter((e) => e.type === "LIVE").length,
);

const goToEvent = (event) => {
    // Redirigimos al mismo splash que el mural para mantener consistencia
    router.push({
        name: "workspace.public.businessSplash",
        // params: { id: event.businessId },
    });
};
</script>
