<template>
  <div id="app-root" class="antialiased text-slate-900 dark:text-slate-100">
    
    <KubixSplash v-if="contextStore.showSplash" />

    <template v-else>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </template>

  </div>
</template>

<script setup>
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import KubixSplash from '@/Kubix/core/components/feedBack/KubixSplash.vue'

const contextStore = useContextStore()
</script>

<style>
/* Estilos globales base para PWA */
html, body, #app {
  margin: 0;
  padding: 0;
  width: 100%;
  height: 100%;
  /* Evita el scroll elástico en el body para que solo scrollee el Main del Layout */
  overscroll-behavior-y: none; 
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>