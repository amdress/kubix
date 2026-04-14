/**
 * ============================================================
 * KUBIX — PWA Index
 * @location Kubix/core/pwa/index.js
 * ============================================================
 */
export { useInstall } from './useInstall'
export { useOffline } from './useOffline'

// Registrar Service Worker
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {
    // Service Worker no disponible
  })
}