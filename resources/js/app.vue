<template>
  <div id="app-container">

    <!-- OFFLINE BANNER -->
    <!-- <div v-if="offline.isOffline" class="offline-banner">
      🔴 Sin conexión a internet
    </div> -->

    <!-- SPLASH -->
    <KubixSplash v-if="contextStore.showSplash" />

    <template v-else>

      <!-- ROUTER -->
      <router-view />

      <!-- PWA BUTTON -->
      <!-- <button
        v-if="pwa.canInstall"
        @click="pwa.install"
        class="pwa-install-btn"
      >
        📥 Instalar
      </button> -->

    </template>

  </div>
</template>

<script setup>
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import KubixSplash from '@/Kubix/core/components/feedBack/KubixSplash.vue'
// import { useInstall, useOffline } from '@/Kubix/core/pwa'

const contextStore = useContextStore()
// const pwa = useInstall()
// const offline = useOffline()
</script>

<style scoped>
#app-container {
  width: 100%;
  height: 100vh;
  overflow: auto;
  background: #f8fafc;
}

.offline-banner {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 200;
  padding: 0.75rem;
  background: #ef4444;
  color: white;
  text-align: center;
  font-weight: bold;
}

.pwa-install-btn {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 100;
  padding: 0.75rem 1.25rem;
  background: #3b82f6;
  color: white;
  border-radius: 0.75rem;
  font-weight: bold;
  cursor: pointer;
}
</style>