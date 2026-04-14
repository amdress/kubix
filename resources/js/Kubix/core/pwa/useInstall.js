/**
 * ════════════════════════════════════════════════════════════════
 * 📲 KUBIX — PWA Install Composable (CLEAN / PRODUCTION)
 * ════════════════════════════════════════════════════════════════
 */

import { ref, onMounted, onUnmounted } from 'vue'

export function useInstall() {
  const canInstall = ref(false)
  const isInstalled = ref(false)

  let deferredPrompt = null
  let initialized = false

  // ─────────────────────────────────────────
  // DETECT INSTALLED
  // ─────────────────────────────────────────
  const checkIfInstalled = () => {
    try {
      isInstalled.value =
        window.matchMedia('(display-mode: standalone)').matches ||
        window.navigator.standalone === true
    } catch {
      isInstalled.value = false
    }
  }

  // ─────────────────────────────────────────
  // BEFORE INSTALL
  // ─────────────────────────────────────────
  const onBeforeInstallPrompt = (event) => {
    event.preventDefault()

    deferredPrompt = event
    canInstall.value = true
  }

  // ─────────────────────────────────────────
  // INSTALLED EVENT
  // ─────────────────────────────────────────
  const onAppInstalled = () => {
    deferredPrompt = null
    canInstall.value = false
    isInstalled.value = true
  }

  // ─────────────────────────────────────────
  // INSTALL ACTION
  // ─────────────────────────────────────────
  const install = async () => {
    if (!deferredPrompt) return false

    try {
      deferredPrompt.prompt()

      const { outcome } = await deferredPrompt.userChoice

      deferredPrompt = null
      canInstall.value = false

      return outcome === 'accepted'
    } catch {
      return false
    }
  }

  // ─────────────────────────────────────────
  // LIFECYCLE
  // ─────────────────────────────────────────
  onMounted(() => {
    if (initialized) return
    initialized = true

    checkIfInstalled()

    if ('onbeforeinstallprompt' in window) {
      window.addEventListener('beforeinstallprompt', onBeforeInstallPrompt)
      window.addEventListener('appinstalled', onAppInstalled)
    }
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