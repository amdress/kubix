<template>
  <div class="min-h-screen flex flex-col bg-slate-950 text-white">
    
    <Header 
      v-if="route.meta.showHeader === true" 
      class="fixed top-0 left-0 right-0 z-50"
    />

    <main 
      class="flex-1"
      :class="{ 
        'pt-16': route.meta.showHeader === true,
        'pb-24': route.meta.showFooter === true 
      }"
    >
      <router-view v-slot="{ Component }">
        <transition name="page-fade" mode="out-in" appear>
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <Footer v-if="route.meta.showFooter === true" />
    
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router'
import Header from '@/Kubix/Pwa/Layouts/components/Header.vue'
import Footer from '@/Kubix/Pwa/Layouts/components/Footer.vue'

const route = useRoute()

</script>