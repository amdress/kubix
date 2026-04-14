<template>
    <div
        class="bg-[#05070a] text-slate-100 overflow-x-hidden font-sans selection:bg-blue-500/30"
    >
        <!-- BACKGROUND GRID GLOBAL -->
        <div class="fixed inset-0 -z-20">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(30,58,138,0.2)_0%,rgba(5,7,10,1)_100%)]"
            ></div>
            <div
                class="absolute inset-0 bg-[linear-gradient(to_right,#80808008_1px,transparent_1px),linear-gradient(to_bottom,#80808008_1px,transparent_1px)] bg-[size:60px_60px]"
            ></div>
        </div>

        <!-- ===================== -->
        <!-- HERO SECTION -->
        <!-- ===================== -->
        <section
            class="relative min-h-screen flex items-center justify-center px-6 overflow-hidden pt-20"
            :style="heroStyle"
        >
            <!-- overlays -->
            <div class="absolute inset-0 bg-black/70"></div>
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/40 to-black"
            ></div>

            <!-- glow -->
            <div
                class="absolute top-[-10%] left-[-5%] w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[140px] animate-pulse"
            ></div>

            <div class="max-w-6xl text-center relative z-10">
                <!-- badge -->
                <div
                    class="inline-flex items-center gap-3 mb-10 px-5 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-xl animate-fade-in shadow-[0_0_20px_rgba(37,99,235,0.2)]"
                >
                    <span class="flex h-2 w-2 relative">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                        ></span>
                    </span>
                    <span
                        class="text-[9px] font-black uppercase tracking-[0.4em] text-blue-200"
                    >
                        Plataforma Operacional v2.4
                    </span>
                </div>

                <!-- title -->
                <h1
                    class="text-6xl md:text-[90px] font-black leading-[0.85] tracking-tighter mb-10"
                >
                    Transforme Dívidas <br />
                    <span
                        class="italic font-extralight text-slate-500 text-5xl md:text-7xl block my-4"
                    >
                        em Oportunidades
                    </span>
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-b from-white via-white to-blue-500"
                    >
                        Libera Juros OS.
                    </span>
                </h1>

                <!-- subtitle -->
                <p
                    class="mt-4 text-lg md:text-2xl text-slate-400 max-w-3xl mx-auto leading-relaxed font-light"
                >
                    A plataforma definitiva para gerir ativos e renegociar
                    dívidas com
                    <span class="text-white font-normal">
                        tecnologia modular
                    </span>.
                    Simples, eficiente e escalável.
                </p>

                <!-- CTA -->
                <div
                    class="mt-16 flex flex-col sm:flex-row gap-8 justify-center items-center"
                >
                    <button
                        aria-label="Começar agora"
                        @click="goToLogin"
                        class="group relative px-14 py-7 bg-blue-600 text-white rounded-full font-black uppercase tracking-widest text-[11px] transition-all hover:scale-105 active:scale-95 shadow-[0_20px_40px_-15px_rgba(37,99,235,0.4)] overflow-hidden"
                    >
                        <span class="relative z-10">Comece Agora</span>
                        <div
                            class="absolute inset-0 bg-blue-400 translate-y-[100%] group-hover:translate-y-0 transition-transform duration-500"
                        ></div>
                    </button>

                    <button
                        aria-label="Ver planos"
                        @click="scrollTo('precos')"
                        class="px-12 py-7 border border-white/10 rounded-full font-black uppercase tracking-[0.2em] text-[10px] backdrop-blur-md hover:bg-white/5 transition-all text-white/70"
                    >
                        Ver Planos
                    </button>
                </div>
            </div>
        </section>

        <!-- ===================== -->
        <!-- NICHOS -->
        <!-- ===================== -->
        <section
            class="py-40 relative px-6 border-y border-white/5 bg-blue-500/[0.01]"
        >
            <div
                class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-20 items-center"
            >
                <div>
                    <h2
                        class="text-5xl font-black mb-10 leading-none text-white tracking-tighter"
                    >
                        Feito para quem <br />
                        <span class="text-blue-500 italic">quer crescer.</span>
                    </h2>

                    <div class="space-y-12">
                        <div
                            v-for="(nicho, i) in nichos"
                            :key="i"
                            class="flex gap-8 group"
                        >
                            <div
                                class="w-16 h-16 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500"
                            >
                                <i :class="nicho.icon"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-2 text-white">
                                    {{ nicho.title }}
                                </h4>
                                <p
                                    class="text-slate-500 leading-relaxed font-light"
                                >
                                    {{ nicho.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative group">
                    <div
                        class="absolute -inset-6 bg-blue-500/20 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-700"
                    ></div>
                    <img
                        :src="dashboardPreview"
                        class="relative rounded-[3rem] border border-white/10 shadow-2xl transition-all duration-1000 group-hover:scale-[1.02]"
                        alt="Kubix Dashboard Preview"
                    />
                </div>
            </div>
        </section>

        <!-- ===================== -->
        <!-- TESTIMONIALS -->
        <!-- ===================== -->
        <section class="py-40 px-6 relative overflow-hidden">
            <div class="max-w-7xl mx-auto">
                <div
                    class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8"
                >
                    <h2
                        class="text-5xl font-black text-white leading-none tracking-tighter"
                    >
                        Validado pela <br />
                        <span class="text-slate-500 italic">
                            comunidade Libera Juros.
                        </span>
                    </h2>

                    <div class="flex gap-3">
                        <div
                            v-for="(_, i) in testimonials"
                            :key="i"
                            class="h-1 rounded-full transition-all"
                            :class="
                                currentSlide === i
                                    ? 'w-12 bg-blue-600'
                                    : 'w-4 bg-white/10'
                            "
                        ></div>
                    </div>
                </div>

                <div
                    ref="slider"
                    class="flex gap-8 overflow-x-auto snap-x snap-mandatory no-scrollbar pb-12"
                    @scroll="handleScroll"
                >
                    <div
                        v-for="(test, i) in testimonials"
                        :key="i"
                        class="min-w-[100%] md:min-w-[500px] snap-center"
                    >
                        <div
                            class="p-10 rounded-[3rem] border border-white/5 bg-white/[0.02] backdrop-blur-2xl"
                        >
                            <p class="text-slate-300 italic text-xl mb-10">
                                “{{ test.text }}”
                            </p>
                            <div class="flex items-center gap-4">
                                <img
                                    :src="test.avatar"
                                    class="w-14 h-14 rounded-xl"
                                />
                                <div>
                                    <p class="font-bold text-white">
                                        {{ test.author }}
                                    </p>
                                    <p
                                        class="text-[10px] uppercase tracking-widest text-blue-400"
                                    >
                                        {{ test.role }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===================== -->
        <!-- PRICING -->
        <!-- ===================== -->
        <section
            id="precos"
            class="py-40 px-6 border-t border-white/5 bg-gradient-to-b from-transparent to-blue-900/10"
        >
            <div class="max-w-4xl mx-auto text-center">
                <h2
                    class="text-6xl font-black text-white mb-6 tracking-tighter"
                >
                    Investimento Inteligente
                </h2>
                <p class="text-slate-500 italic text-xl mb-20">
                    Estrutura profissional sem custos ocultos.
                </p>

                <div class="space-y-6 text-left">
                    <div
                        v-for="(item, i) in pricing"
                        :key="i"
                        class="p-8 rounded-3xl border border-white/5 bg-white/[0.02]"
                    >
                        <h3 class="text-2xl font-bold text-white mb-2">
                            {{ item.title }}
                        </h3>
                        <p class="text-slate-400" v-html="item.description"></p>
                    </div>
                </div>

                <button
                    aria-label="Agendar demonstração"
                    @click="goToLogin"
                    class="mt-20 px-20 py-8 bg-white text-black rounded-full font-black uppercase tracking-[0.3em] text-[11px]"
                >
                    Agendar Demonstração
                </button>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import heroBg from "@/assets/images/hero.png";
import dashboardPreview from "@/assets/images/dashboard-preview.png";

const router = useRouter();
const slider = ref(null);
const currentSlide = ref(0);
let interval = null;

const heroStyle = {
    backgroundImage: `url(${heroBg})`,
    backgroundSize: "cover",
    backgroundPosition: "center",
    backgroundRepeat: "no-repeat",
};

const nichos = [
    {
        icon: "fa-solid fa-rocket",
        title: "Startups e Inovação",
        description: "Estruture sua operação desde o início com tecnologia escalável.",
    },
    {
        icon: "fa-solid fa-building-columns",
        title: "Empresas Consolidadas",
        description: "Controle total de clientes, contratos e negociações.",
    },
];

const testimonials = [
    {
        text: "A Libera Juros transformou nossa operação.",
        author: "Ricardo M.",
        role: "CEO",
        avatar: "https://i.pravatar.cc/150?u=ricardo",
    },
    {
        text: "Plataforma sólida e intuitiva.",
        author: "Juliana T.",
        role: "COO",
        avatar: "https://i.pravatar.cc/150?u=juliana",
    },
    {
        text: "A melhor decisão para nossa escala de ativos.",
        author: "Marcos V.",
        role: "Head de Operações",
        avatar: "https://i.pravatar.cc/150?u=marcos",
    },
];

const pricing = [
    {
        title: "Onboarding",
        description: 'Setup completo por <span class="text-blue-400 font-bold">R$ 3.000</span>.',
    },
    {
        title: "Mensalidade",
        description: 'Acesso total por <span class="text-emerald-400 font-bold">R$ 497/mês</span>.',
    },
];

const goToLogin = () => router.push({ name: "auth.login" });

const scrollTo = (id) => {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: "smooth" });
};

// Manejo de scroll manual y actualización de puntos (dots)
const handleScroll = () => {
    if (!slider.value) return;
    const width = slider.value.offsetWidth;
    currentSlide.value = Math.round(slider.value.scrollLeft / width);
};

const startAutoPlay = () => {
    stopAutoPlay(); // Limpiamos cualquier residuo
    interval = setInterval(() => {
        if (!slider.value) return;
        
        const scrollWidth = slider.value.scrollWidth;
        const currentScroll = slider.value.scrollLeft;
        const clientWidth = slider.value.clientWidth;

        // Si llegó al final, vuelve al inicio, si no, avanza un slide
        if (currentScroll + clientWidth >= scrollWidth - 10) {
            slider.value.scrollTo({ left: 0, behavior: "smooth" });
        } else {
            slider.value.scrollBy({ left: clientWidth, behavior: "smooth" });
        }
    }, 5000);
};

const stopAutoPlay = () => {
    if (interval) clearInterval(interval);
};

onMounted(startAutoPlay);
onUnmounted(stopAutoPlay);
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    scrollbar-width: none;
}
.animate-fade-in {
    animation: fadeIn 1.5s ease-out forwards;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
