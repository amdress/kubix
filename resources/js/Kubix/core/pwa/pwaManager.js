/**
 * ════════════════════════════════════════════════════════════════
 * 📲 KUBIX — PWA Manager (SYSTEM LAYER)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Orquestar eventos globales de PWA
 * - Controlar estado de instalación
 * - Centralizar lógica beforeinstallprompt
 *
 * NO HACE:
 * - No UI
 * - No componentes
 *
 * UBICACIÓN:
 * /Kubix/core/pwa/pwaManager.js
 *
 * ════════════════════════════════════════════════════════════════
 */

import { ref } from 'vue'

const canInstall = ref(false)
const isInstalled = ref(false)
let deferredPrompt = null

export function usePwaManager() {

  const init = () => {
    if (typeof window === 'undefined') return

    // Detect install prompt
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault()
      deferredPrompt = e
      canInstall.value = true
    })

    // Detect installed
    window.addEventListener('appinstalled', () => {
      deferredPrompt = null
      canInstall.value = false
      isInstalled.value = true
    })
  }

  const install = async () => {
    if (!deferredPrompt) return false

    deferredPrompt.prompt()

    const result = await deferredPrompt.userChoice

    if (result.outcome === 'accepted') {
      isInstalled.value = true
    }

    deferredPrompt = null
    canInstall.value = false

    return result.outcome === 'accepted'
  }

  return {
    init,
    install,
    canInstall,
    isInstalled
  }
}