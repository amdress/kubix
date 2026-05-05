/**
 * ════════════════════════════════════════════════════════════════
 * 📲 KUBIX — PWA Install Composable (UI LAYER)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Manejar UI de instalación PWA
 * - Detectar prompt del navegador
 * - Ejecutar instalación desde interacción del usuario
 *
 * DEPENDE DE:
 * - browser beforeinstallprompt event
 *
 * UBICACIÓN:
 * /Kubix/core/pwa/useInstall.js
 *
 * ════════════════════════════════════════════════════════════════
 */

import { ref, onMounted, onUnmounted } from 'vue'

export function useInstall() {
  const canInstall = ref(false)
  const isInstalled = ref(false)

  let deferredPrompt = null
  let initialized = false

  const checkIfInstalled = () => {
    try {
      isInstalled.value =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true
    } catch {
      isInstalled.value = false
    }
  }

  const onBeforeInstallPrompt = (event) => {
    event.preventDefault()
    deferredPrompt = event
    canInstall.value = true
  }

  const onAppInstalled = () => {
    deferredPrompt = null
    canInstall.value = false
    isInstalled.value = true
  }

  const install = async () => {
    if (!deferredPrompt) return false

    deferredPrompt.prompt()

    const { outcome } = await deferredPrompt.userChoice

    deferredPrompt = null
    canInstall.value = false

    return outcome === 'accepted'
  }

  onMounted(() => {
    if (initialized) return
    initialized = true

    checkIfInstalled()

    window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt)
    window.addEventListener('appinstalled', onAppInstalled)
  })

  onUnmounted(() => {
    window.removeEventListener('beforeinstallprompt', onBeforeInstallPrompt)
    window.removeEventListener('appinstalled', onAppInstalled)
  })

  return {
    canInstall,
    isInstalled,
    install,
  }
}