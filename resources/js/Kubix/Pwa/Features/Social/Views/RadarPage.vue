<template>
  <div class="flex flex-col min-h-full bg-slate-100 font-sans">
    
    <div class="sticky top-0 z-30 bg-white px-4 py-4 shadow-sm border-b border-slate-200">
      <div class="relative mb-4">
        <ph-magnifying-glass :size="20" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="O que você precisa no bairro?"
          class="w-full pl-12 pr-4 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-slate-900/10 text-sm font-bold"
        />
      </div>

      <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
        <button 
          v-for="cat in categories" :key="cat.id"
          @click="selectedCategory = cat.id"
          :class="[
            'px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border shrink-0',
            selectedCategory === cat.id 
              ? 'bg-slate-900 text-white border-slate-900 shadow-md scale-105' 
              : 'bg-white text-slate-500 border-slate-100'
          ]"
        >
          {{ cat.label }}
        </button>
      </div>
    </div>

    <div class="px-4 py-6 space-y-6">
      <div 
        v-for="shop in filteredShops" :key="shop.id"
        @click="goToProfile(shop.id)"
        class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100 flex flex-col relative active:scale-[0.98] transition-all cursor-pointer"
      >
        <button 
          @click.stop="toggleLike(shop)"
          class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/90 backdrop-blur-sm flex items-center justify-center shadow-sm active:scale-75 transition-all"
        >
          <ph-heart 
            :size="22" 
            :weight="shop.isLiked ? 'fill' : 'regular'" 
            :class="shop.isLiked ? 'text-red-500' : 'text-slate-300'"
          />
        </button>

        <div class="relative h-48">
          <img :src="shop.image" class="w-full h-full object-cover" loading="lazy" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
          <div class="absolute bottom-4 left-4">
             <span class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">
                {{ shop.categoryLabel }}
             </span>
          </div>
        </div>

        <div class="p-5">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <h3 class="font-black text-slate-900 uppercase tracking-tight text-lg leading-tight">{{ shop.name }}</h3>
              <div class="flex items-center gap-1 text-amber-400 mt-1">
                <ph-star v-for="i in 5" :key="i" :size="12" :weight="i <= Math.floor(shop.rating) ? 'fill' : 'light'" />
                <span class="text-[11px] font-black text-slate-400 ml-1">{{ shop.rating }}</span>
              </div>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-900">{{ shop.distance }}m</p>
                <p class="text-[10px] text-slate-400 font-medium truncate w-24">{{ shop.address }}</p>
            </div>
          </div>

          <div class="mt-5 pt-4 border-t border-slate-50 flex items-center justify-between">
            <span class="text-[11px] font-bold text-slate-400">{{ shop.likes + (shop.isLiked ? 1 : 0) }} curtidas</span>
            
            <div 
              class="px-6 py-3 rounded-2xl text-white text-[10px] font-black uppercase tracking-widest shadow-xl transition-all"
              :style="{ backgroundColor: shop.primaryColor || '#0f172a' }"
            >
              Ver Perfil
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="h-28"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { PhMagnifyingGlass, PhStar, PhHeart } from "@phosphor-icons/vue"
import { dbFakeCompanies } from '../dbFake/dbFakeCompanies'

const router = useRouter()
const searchQuery = ref('')
const selectedCategory = ref('all')
const shops = ref([])

const categories = [
  { id: 'all', label: 'Tudo' },
  { id: 'comida', label: 'Comida' },
  { id: 'saude', label: 'Saúde' },
  { id: 'mercado', label: 'Mercados' },
  { id: 'servicos', label: 'Serviços' },
  { id: 'automotivo', label: 'Carros' }
]

onMounted(() => {
  shops.value = dbFakeCompanies
})

const filteredShops = computed(() => {
  return shops.value.filter(shop => {
    const search = searchQuery.value.toLowerCase()
    const matchesName = shop.name.toLowerCase().includes(search) || (shop.categoryLabel && shop.categoryLabel.toLowerCase().includes(search))
    const matchesCategory = selectedCategory.value === 'all' || shop.category === selectedCategory.value
    return matchesName && matchesCategory
  })
})

const toggleLike = (shop) => shop.isLiked = !shop.isLiked

const goToProfile = (id) => {
  // El nombre debe coincidir EXACTAMENTE con el del routes.js
  router.push({ 
    name: 'app.radar.profile', 
    params: { id: id.toString() } 
  })
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>