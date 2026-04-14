/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — Plugins Registry (ORCHESTRATION CORE)
 * ════════════════════════════════════════════════════════════════
 * - Construye servicios
 * - Expone registry global
 * - Sin Vue plugin system (NO app.use)
 * ════════════════════════════════════════════════════════════════
 */

import { createLoadingService } from './loading.plugin'
import { createSweetAlertService } from './sweetalert.plugin'
import { createLeafletService } from './leaflet.plugin'
import { createEChartsService } from './echarts.plugin'

/**
 * ⚡ REGISTRY FACTORY
 */
function createRegistry() {
  return {
    loading: createLoadingService(),
    alert: createSweetAlertService(),
    leaflet: createLeafletService(),
    echarts: createEChartsService(),
  }
}

/**
 * ⚙️ BOOTSTRAP CENTRAL
 */
export function setupPlugins(app) {
  console.group('%c⚙️ KUBIX PLUGINS BOOT', 'color:#a78bfa;font-weight:bold')

  const registry = createRegistry()

  // opcional: attach global access
  app.config.globalProperties.$kubix = registry
  app.provide('kubix', registry)

  console.log('✔ Registry ready:', Object.keys(registry))

  console.groupEnd()

  return registry
}

/**
 * 🧩 DEBUG EXPORTS
 */
export {
  createLoadingService,
  createSweetAlertService,
  createLeafletService,
  createEChartsService,
}