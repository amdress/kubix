/**
 * ════════════════════════════════════════════════════════════════
 * 📡 KUBIX — PWA Offline Detector (NETWORK LAYER)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Detectar estado de conexión del usuario
 * - Exponer estado online/offline reactivo
 * - Escuchar cambios de red del navegador
 *
 * UBICACIÓN:
 * /Kubix/core/pwa/useOffline.js
 *
 * ════════════════════════════════════════════════════════════════
 */

import { ref, onMounted, onUnmounted } from 'vue'

export function useOffline() {

  const isOnline = ref(navigator.onLine)
  const isOffline = ref(!navigator.onLine)

  let initialized = false

  const onOnline = () => {
    isOnline.value = true
    isOffline.value = false
  }

  const onOffline = () => {
    isOnline.value = false
    isOffline.value = true
  }

  onMounted(() => {
    if (initialized) return
    initialized = true

    window.addEventListener('online', onOnline)
    window.addEventListener('offline', onOffline)
  })

  onUnmounted(() => {
    window.removeEventListener('online', onOnline)
    window.removeEventListener('offline', onOffline)
  })

  return {
    isOnline,
    isOffline
  }
}