<template>
  <div class="min-h-screen flex flex-col bg-slate-950 text-white">
    
    <!-- 🔹 HEADER -->
    <Header 
      v-if="showHeader"
      class="fixed top-0 left-0 right-0 z-50"
    />

    <!-- 🔹 MAIN -->
    <main 
      class="flex-1"
      :class="{
        'pt-16': showHeader,
        'pb-24': showFooter
      }"
    >
      <router-view />
    </main>

    <!-- 🔹 FOOTER -->
    <Footer v-if="showFooter" />
    
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

import Header from '@/Kubix/Pwa/Layouts/components/Header.vue'
import Footer from '@/Kubix/Pwa/Layouts/components/Footer.vue'

const route = useRoute()


const showHeader = computed(() => route.meta.showHeader ?? true)
const showFooter = computed(() => route.meta.showFooter ?? true)


</script>