<template>
  <div class="max-w-[480px] md:max-w-[1024px] mx-auto min-h-screen bg-white shadow-2xl shadow-slate-200">

    <!-- HEADER -->
    <header class="sticky top-0 z-50 bg-slate-50/90 backdrop-blur-md border-b border-slate-200">

      <!-- CATEGORIES -->
      <section class="flex gap-1 overflow-x-auto p-2  scrollbar-hide snap-x">
        <button 
          v-for="cat in categories" 
          :key="cat.id" 
          @click="activeCategory = cat.id"
          class="flex flex-col items-center gap-2 shrink-0 px-2 snap-start active:scale-90 transition-all"
        >
          <div :class="[
            'w-12 h-12 rounded-2xl flex items-center justify-center border transition-all duration-300',
            activeCategory === cat.id 
              ? 'bg-blue-600 border-blue-500 shadow-lg shadow-blue-500/30' 
              : 'bg-white border-slate-200'
          ]">
            <component 
              :is="cat.icon" 
              :size="20" 
              :weight="activeCategory === cat.id ? 'fill' : 'bold'" 
              :class="activeCategory === cat.id ? 'text-white' : 'text-slate-500'" 
            />
          </div>

          <span :class="[
            'text-[10px] font-black uppercase tracking-tighter',
            activeCategory === cat.id ? 'text-blue-600' : 'opacity-40 text-slate-600'
          ]">
            {{ cat.name }}
          </span>
        </button>
      </section>

      <!-- SEARCH -->
      <div class="px-5 pb-2">
        <div class="relative group">
          <PhMagnifyingGlass class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Buscar no bairro..."
            class="w-full h-11 pl-11 pr-4 rounded-2xl bg-white border border-slate-200 focus:ring-2 focus:ring-blue-600 text-sm shadow-sm outline-none"
          />
        </div>
      </div>

    </header>

    <!-- RESULTS HEADER -->
    <div class="flex items-center justify-between mb-1 p-4 mt-3">
      <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
        {{ activeCategoryName }}
      </span>
      <span class="text-[10px] font-bold text-slate-300">
        {{ filteredBusinesses.length }} locais
      </span>
    </div>

    <!-- LIST -->
    <div class="px-3 space-y-2 pb-10 p-2">
      <div 
        v-for="biz in filteredBusinesses" 
        :key="biz.id"
        @click="goToBusiness"
        class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-200 shadow-sm active:scale-[0.98] transition-all cursor-pointer"
      >
        <!-- LOGO -->
        <div class="relative shrink-0">
          <div class="w-14 h-14 rounded-xl overflow-hidden border border-slate-100">
            <img :src="biz.logo" class="w-full h-full object-contain p-1" />
          </div>

          <div :class="[
            'absolute -top-1 -right-1 w-4 h-4 rounded-full border-2 border-white',
            biz.isOpen ? 'bg-emerald-500' : 'bg-slate-300'
          ]"></div>
        </div>

        <!-- INFO -->
        <div class="flex-1 min-w-0">
          <h3 class="text-[14px] font-black uppercase truncate">
            {{ biz.name }}
          </h3>

          <p class="text-[11px] font-bold text-blue-600/80 uppercase">
            {{ biz.categoryName }} • {{ biz.distance }}
          </p>

          <div class="flex items-center gap-1.5 mt-1">
            <PhStar :size="10" weight="fill" class="text-amber-500" />
            <span class="text-[10px] font-black opacity-60">{{ biz.rating }}</span>

            <span class="text-[10px] opacity-20 px-1">|</span>

            <span :class="[
              'text-[9px] font-black uppercase',
              biz.isOpen ? 'text-emerald-600' : 'text-slate-400'
            ]">
              {{ biz.isOpen ? 'Aberto' : 'Fechado' }}
            </span>
          </div>
        </div>

        <PhCaretRight :size="18" class="text-slate-300" />
      </div>

      <!-- EMPTY -->
      <div v-if="filteredBusinesses.length === 0" class="flex flex-col items-center py-20 opacity-20">
        <PhMagnifyingGlass :size="48" />
        <p class="text-sm font-black uppercase mt-4">Nenhum resultado</p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  PhSquaresFour,
  PhStorefront,
  PhFirstAid,
  PhPizza,
  PhHammer,
  PhCar,
  PhMagnifyingGlass,
  PhStar,
  PhCaretRight,
  PhSparkle,
  PhHouse,
  PhGraduationCap,
  PhGameController,
  PhPawPrint
} from "@phosphor-icons/vue"

/**
 * STATE
 */
const activeCategory = ref(0)
const searchQuery = ref('')
const router = useRouter()

/**
 * ✅ CATEGORÍAS PRINCIPALES (MACRO)
 */
const categories = [
  { id: 0, name: 'Todos', icon: PhSquaresFour },

  { id: 1, name: 'Comida', icon: PhPizza },
  { id: 2, name: 'Salud', icon: PhFirstAid },
  { id: 3, name: 'Belleza', icon: PhSparkle },
  { id: 4, name: 'Servicios', icon: PhHammer },
  { id: 5, name: 'Hogar', icon: PhHouse },
  { id: 6, name: 'Auto', icon: PhCar },
  { id: 7, name: 'Tiendas', icon: PhStorefront },
  { id: 8, name: 'Educación', icon: PhGraduationCap },
  { id: 9, name: 'Ocio', icon: PhGameController },
  { id: 10, name: 'Mascotas', icon: PhPawPrint }
]


const goToBusiness = () => {
  router.push({
    name: 'workspace.public.businessSplash'
  })
}
/**
 * MOCK (con subcategoría interna)
 */
const businesses = [
  {
    id: 1,
    name: 'Starbucks Batel',
    logo: 'https://logo.clearbit.com/starbucks.com',
    categoryId: 1,
    categoryName: 'Cafetería',
    distance: '1.2km',
    rating: '4.6',
    isOpen: true
  },
  {
    id: 2,
    name: 'Madero Steak House',
    logo: 'https://logo.clearbit.com/madero.com.br',
    categoryId: 1,
    categoryName: 'Restaurante',
    distance: '2.1km',
    rating: '4.7',
    isOpen: true
  },
  {
    id: 3,
    name: 'Hospital Central',
    logo: 'https://logo.clearbit.com/redcross.org',
    categoryId: 2,
    categoryName: 'Hospital',
    distance: '800m',
    rating: '4.5',
    isOpen: true
  },
  {
    id: 4,
    name: 'Auto Mecânica Silva',
    logo: 'https://logo.clearbit.com/bosch.com',
    categoryId: 4,
    categoryName: 'Mecánica',
    distance: '600m',
    rating: '4.2',
    isOpen: false
  }
]

/**
 * COMPUTED
 */
const activeCategoryName = computed(() => {
  return categories.find(c => c.id === activeCategory.value)?.name || 'Todos'
})

const filteredBusinesses = computed(() => {
  return businesses.filter(biz => {
    const matchCategory =
      activeCategory.value === 0 ||
      biz.categoryId === activeCategory.value

    const matchSearch =
      biz.name.toLowerCase().includes(searchQuery.value.toLowerCase())

    return matchCategory && matchSearch
  })
})
</script>

<style scoped>
.scrollbar-hide {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
</style>