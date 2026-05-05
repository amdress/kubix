<template>
    <div class="min-h-screen bg-white pb-24">
        <div class="relative h-[50vh] md:h-[60vh] bg-slate-100">
            <div
                class="w-full h-full flex transition-transform duration-500 ease-out"
                :style="{ transform: `translateX(-${currentImage * 100}%)` }"
            >
                <img
                    v-for="(img, i) in product.images"
                    :key="i"
                    :src="img"
                    class="w-full h-full object-cover flex-shrink-0 cursor-zoom-in"
                    @click="openViewer"
                />
            </div>

            <div
                class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-black/40 to-transparent"
            ></div>

            <button
                @click="$router.back()"
                class="absolute top-5 left-5 w-10 h-10 flex items-center justify-center bg-white/20 hover:bg-white/40 backdrop-blur-md text-white rounded-full transition-all"
            >
                <PhCaretLeft size="24" weight="bold" />
            </button>

            <div
                class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-1.5"
            >
                <div
                    v-for="(_, i) in product.images"
                    :key="i"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="
                        i === currentImage
                            ? 'w-6 bg-white'
                            : 'w-1.5 bg-white/50'
                    "
                ></div>
            </div>
        </div>

        <div class="px-6 py-8 space-y-8">
            <div class="flex justify-between items-start">
                <div class="space-y-1">
                    <h1
                        class="text-2xl font-black tracking-tight text-slate-900"
                    >
                        {{ product.title }}
                    </h1>
                    <div class="flex items-center gap-2 text-slate-500">
                        <PhMapPin
                            size="14"
                            weight="fill"
                            class="text-blue-500"
                        />
                        <span
                            class="text-xs font-bold uppercase tracking-widest"
                            >{{ product.neighborhood }}</span
                        >
                    </div>
                </div>
                <p class="text-2xl font-black text-blue-600">
                    R$ {{ product.price.toLocaleString() }}
                </p>
            </div>

            <div
                @click="goToSellerProfile"
                class="group flex items-center gap-4 p-4 rounded-3xl bg-slate-50 border border-slate-100 active:bg-slate-100 transition-colors cursor-pointer"
            >
                <div class="relative">
                    <img
                        :src="product.seller.avatar"
                        class="w-14 h-14 rounded-2xl object-cover"
                    />
                    <div
                        class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full"
                    ></div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-black text-slate-900">
                        {{ product.seller.name }}
                    </p>
                    <div
                        class="flex items-center gap-2 text-[11px] font-bold text-slate-400"
                    >
                        <span class="flex items-center gap-0.5 text-amber-500">
                            <PhStar weight="fill" size="12" />
                            {{ product.seller.rating }}
                        </span>
                        <span>•</span>
                        <span>{{ product.seller.sales }} vendas</span>
                    </div>
                </div>
                <PhCaretRight
                    size="20"
                    class="text-slate-300 group-hover:text-blue-500 transition-colors"
                />
            </div>

            <div class="space-y-3">
                <h2
                    class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400"
                >
                    Descrição
                </h2>
                <p
                    class="text-sm text-slate-600 leading-relaxed whitespace-pre-line"
                >
                    {{ product.description }}
                </p>
            </div>
        </div>

        <FabWhatsapp
            :phone="product.phone"
            :message="`Olá, vi seu anúncio de ${product.title} no Market de Kubix Gostaria de saber mais detalhes?`"
        />

        <Transition name="fade">
            <div
                v-if="viewerOpen"
                class="fixed inset-0 bg-black z-[9999] flex flex-col"
            >
                <div class="p-6 flex justify-between items-center text-white">
                    <span class="text-xs font-bold opacity-60"
                        >{{ currentImage + 1 }} /
                        {{ product.images.length }}</span
                    >
                    <button
                        @click="viewerOpen = false"
                        class="w-10 h-10 flex items-center justify-center bg-white/10 rounded-full"
                    >
                        <PhX size="20" weight="bold" />
                    </button>
                </div>

                <div
                    class="flex-1 relative flex items-center justify-center overflow-hidden"
                >
                    <img
                        :src="product.images[currentImage]"
                        class="max-w-full max-h-full object-contain transition-all duration-300"
                    />

                    <div
                        @click="prevImage"
                        class="absolute left-0 inset-y-0 w-24 cursor-pointer"
                    ></div>
                    <div
                        @click="nextImage"
                        class="absolute right-0 inset-y-0 w-24 cursor-pointer"
                    ></div>
                </div>

                <div class="p-8 flex justify-center gap-3 overflow-x-auto">
                    <img
                        v-for="(img, i) in product.images"
                        :key="i"
                        :src="img"
                        @click="currentImage = i"
                        class="w-14 h-14 object-cover rounded-xl border-2 transition-all"
                        :class="
                            i === currentImage
                                ? 'border-blue-500 scale-110'
                                : 'border-transparent opacity-40'
                        "
                    />
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import FabWhatsapp from "../../../shared/Ui/Whatsapp/FabWhatsapp.vue";  
import {
    PhCaretLeft,
    PhCaretRight,
    PhStar,
    PhMapPin,
    PhWhatsappLogo,
    PhX,
} from "@phosphor-icons/vue";

const currentImage = ref(0);
const viewerOpen = ref(false);
const router = useRouter();

// Props simuladas para el ejemplo
const product = {
    title: "Yamaha MT-03 2024",
    price: 28500,
    neighborhood: "Capão Raso, Curitiba",
    phone: "5541999999999",
    images: [
        "https://images.unsplash.com/photo-1609630875171-b1321377ee65",
        "https://images.unsplash.com/photo-1558981806-ec527fa84c39",
        "https://images.unsplash.com/photo-1517520287167-4bbf64a00d66",
    ],
    description: `Único dono, apenas 5.000km rodados.
Revisões feitas na concessionária.
Acompanha manual e chave reserva.
Aceito troca por menor valor.`,
    seller: {
        id: "123",
        name: "João Silva",
        avatar: "https://i.pravatar.cc/150?img=12",
        rating: 4.8,
        sales: 23,
    },
};

const nextImage = () => {
    if (currentImage.value < product.images.length - 1) currentImage.value++;
    else currentImage.value = 0; // Loop opcional
};

const prevImage = () => {
    if (currentImage.value > 0) currentImage.value--;
    else currentImage.value = product.images.length - 1;
};

const openViewer = () => {
    viewerOpen.value = true;
};


const goToSellerProfile = (product) => {
    router.push({
        name: "social.profile",
        params: { id: product.id },
    });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Para esconder scrollbar en thumbnails si hay muchas */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
