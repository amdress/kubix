/**
 * ============================================================
 * KUBIX — PWA Offline
 * @location Kubix/core/pwa/useOffline.js
 * @responsibility Detectar si el usuario tiene conexión a internet.
 * ============================================================
 */
import { ref, onMounted, onUnmounted } from 'vue'

export function useOffline() {

  const isOnline  = ref(navigator.onLine)
  const isOffline = ref(!navigator.onLine)
  let initialized = false

  const onOnline = () => {
    isOnline.value  = true
    isOffline.value = false
    console.log('%c🟢 Kubix: conexión restaurada', 'color: #22d3ee; font-weight: bold;')
  }

  const onOffline = () => {
    isOnline.value  = false
    isOffline.value = true
    console.log('%c🔴 Kubix: sin conexión', 'color: #f87171; font-weight: bold;')
  }

  onMounted(() => {
    if (initialized) return
    initialized = true
    
    window.addEventListener('online',  onOnline)
    window.addEventListener('offline', onOffline)
  })

  onUnmounted(() => {
    window.removeEventListener('online',  onOnline)
    window.removeEventListener('offline', onOffline)
  })

  return {
    isOnline,   // true cuando hay conexión
    isOffline,  // true cuando no hay conexión
  }
}