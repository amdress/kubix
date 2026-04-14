/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Device Detection (ULTRA-ROBUST)
 * ════════════════════════════════════════════════════════════════
 * * BREAKPOINTS (UI Strategy):
 * - Mobile/Tablet (Vertical): 0px - 768px  -> isMobile: true
 * - Tablet (Horizontal)/Laptop: > 768px    -> isMobile: false
 * * UBICACIÓN: /Kubix/core/utils/device/device.js
 */

import { ref, computed, onMounted, onUnmounted } from 'vue'

// CONFIGURACIÓN DE RESOLUCIÓN
// 768 es el punto de inflexión donde termina el mundo touch-first vertical
const MOBILE_BREAKPOINT = 768 
const isSSR = typeof window === 'undefined'

// ESTADO GLOBAL (SINGLETON)
const windowWidth = ref(isSSR ? 1200 : window.innerWidth)
let listenerCount = 0

/**
 * Lógica pura de evaluación
 */
const checkIsMobile = (width) => width <= MOBILE_BREAKPOINT

/**
 * Evaluación estática (Uso en Stores y Scripts)
 */
export function isMobile() {
  if (isSSR) return false
  // Forzamos lectura fresca de window.innerWidth
  return checkIsMobile(window.innerWidth)
}

/**
 * Composable reactivo (Uso en Componentes)
 */
export function useDevice() {
  const isMobileDevice = computed(() => checkIsMobile(windowWidth.value))

  const onResize = () => {
    // Solo actualizamos si el ancho realmente cambió (ignora saltos de scrollbar)
    if (windowWidth.value !== window.innerWidth) {
      windowWidth.value = window.innerWidth
    }
  }

  onMounted(() => {
    if (listenerCount === 0 && !isSSR) {
      window.addEventListener('resize', onResize, { passive: true })
      // Sincronización inmediata al montar
      onResize()
    }
    listenerCount++
  })

  onUnmounted(() => {
    listenerCount--
    if (listenerCount === 0 && !isSSR) {
      window.removeEventListener('resize', onResize)
    }
  })

  return {
    isMobile: isMobileDevice,
    windowWidth: computed(() => windowWidth.value)
  }
}