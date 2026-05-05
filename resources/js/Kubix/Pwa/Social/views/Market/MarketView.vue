<template>
  <div class="max-w-[480px] md:max-w-[1024px] mx-auto min-h-screen bg-white shadow-2xl shadow-slate-200 p-4 space-y-6">

    <!-- HEADER -->
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black uppercase tracking-tighter">
          Market
        </h1>
        <p class="text-[10px] font-bold uppercase opacity-40 tracking-widest">
          Em {{ currentNeighborhood }}
        </p>
      </div>

      <button class="p-2 rounded-xl bg-slate-100 active:scale-95">
        <PhSliders :size="18" />
      </button>
    </header>

    <!-- GRID MÁS ORGÁNICO -->
    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
      <MarketCard 
        v-for="product in products" 
        :key="product.id" 
        :item="product"
        @click="goToDetail(product)"
      />
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { PhSliders } from "@phosphor-icons/vue"
import MarketCard from "../../components/cards/MarketCard.vue"

const router = useRouter()

const currentNeighborhood = ref("Capão Raso")

const products = ref([
  {
    id: 1,
    title: 'Yamaha MT-03',
    price: 28500,
    category: 'Motor',
    neighborhood: 'Capão Raso',
    image: 'https://images.unsplash.com/photo-1609630875171-b1321377ee65'
  },
  {
    id: 2,
    title: 'iPhone 15 Pro',
    price: 6200,
    category: 'Tech',
    neighborhood: 'Capão Raso',
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569'
  }
])

const goToDetail = (product) => {
  router.push({
    name: 'social.market.detail',
    params: { id: product.id }
  })
}
</script>