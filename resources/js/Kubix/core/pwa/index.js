/**
 * ════════════════════════════════════════════════════════════════
 * 📲 KUBIX — PWA Index (ENTRY POINT)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Punto único de inicialización del sistema PWA
 * - Exportar composables públicos
 * - Registrar Service Worker global
 *
 * NO HACE:
 * - No maneja lógica de UI
 * - No gestiona estado interno
 *
 * UBICACIÓN:
 * /Kubix/core/pwa/index.js
 *
 * ════════════════════════════════════════════════════════════════
 */

export { useInstall } from './useInstall'
export { useOffline } from './useOffline'
export { usePwaManager } from './pwaManager'

// ─────────────────────────────────────────
// SERVICE WORKER REGISTRATION
// ─────────────────────────────────────────
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js').catch(() => {
    console.warn('[PWA] Service Worker no disponible')
  })
}