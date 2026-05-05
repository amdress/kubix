/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — Plugins Registry (ORCHESTRATION CORE)
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD:
 * - Crear servicios (loading, alert, maps, charts)
 * - Exponer registry global
 * - Inyectar en Vue (provide)
 *
 * REGLAS:
 * - NO usar app.use()
 * - Servicios desacoplados (pure JS)
 * - Acceso vía inject('kubix')
 * ════════════════════════════════════════════════════════════════
 */

import { createLoadingService } from './loading.plugin'
import { createSweetAlertService } from './sweetalert.plugin'
import { createLeafletService } from './leaflet.plugin'
import { createEChartsService } from './echarts.plugin'

/**
 * 🧱 REGISTRY FACTORY
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
 * ⚙️ BOOTSTRAP
 */
export function setupPlugins(app) {
  console.group('%c⚙️ KUBIX PLUGINS', 'color:#a78bfa;font-weight:bold')

  const kubix = createRegistry()

  // 🔗 Vue Injection
  app.provide('kubix', kubix)

  // 🌍 Global (debug opcional)
  if (import.meta.env.DEV) {
    window.$kubix = kubix
    console.log('✔ Plugins:', Object.keys(kubix))
  }

  console.groupEnd()

  return kubix
}

/**
 * 🧩 HELPER SIMPLE (opcional pero útil)
 */
import { inject } from 'vue'

export function useKubix() {
  const kubix = inject('kubix')

  if (!kubix) {
    throw new Error('[KUBIX] Plugins no inicializados')
  }

  return kubix
}