<template>
  <div class="space-y-6 p-2">

    <!-- HEADER -->
    <header class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-black uppercase tracking-tighter">
          Empresas
        </h1>
        <p class="text-[10px] font-bold uppercase opacity-40 tracking-widest">
          Tus negocios
        </p>
      </div>
    </header>

    <!-- LOADING -->
    <div v-if="loading" class="text-center py-10 text-slate-400">
      Cargando...
    </div>

    <!-- EMPTY -->
    <div
      v-else-if="businesses.length === 0"
      class="text-center py-20"
    >
      <p class="text-sm text-slate-400 font-semibold">
        No tienes empresas
      </p>
    </div>

    <!-- GRID -->
    <div
      v-else
      class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4"
    >
      <BusinessCard
        v-for="biz in businesses"
        :key="biz.id"
        :business="biz"
        @click="goToDetail(biz)"
      />
    </div>

  </div>

  <!-- FAB -->
  <button
    @click="goToCreate"
    class="fixed bottom-20 right-6 z-[95] w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-lg hover:bg-blue-500 active:scale-90 transition"
  >
    +
  </button>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BusinessCard from '../../components/BusinessCard.vue'

const router = useRouter()

const businesses = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    // 🔥 simula API
    await new Promise(r => setTimeout(r, 500))

    const response = [
      {
        id: 1,
        name: 'Bar do João',
        category: 'Bar',
        cover: 'https://images.unsplash.com/photo-1552566626-52f8b828add9',
        stats: { views: 1200, clicks: 320 }
      },
      null, // 👈 esto antes te rompía todo
      undefined
    ]

    // 🔥 limpieza REAL (esto es clave)
    businesses.value = response.filter(Boolean)

  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const goToCreate = () => {
  router.push({ name: 'workspace.admin.onboarding' })
}

const goToDetail = (biz) => {
  if (!biz?.id) return
  router.push({ name: 'workspace.public.businessSplash', params: { id: biz.id } })  
}
</script>