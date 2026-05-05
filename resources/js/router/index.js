/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Router Core (EXTENSIBLE BYPASS MODE)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Registrar rutas principales de la PWA
 * - Definir punto de extensión para futuros guards
 *
 * ESTADO:
 * - Guards desactivados (flujo libre)
 * - Pipeline preparado (no acoplado)
 *
 * DISEÑO:
 * - Middleware desacoplado del router core
 * - Activación progresiva sin refactor
 *
 * UBICACIÓN:
 * - /Kubix/core/router/index.js
 *
 * ════════════════════════════════════════════════════════════════
 */

import { createRouter, createWebHistory } from 'vue-router'
import pwaRoutes from '@/Kubix/Pwa/routes'

// 🔹 FUTURO: importar pipeline de guards
// import { runGuards } from './pipeline'

const router = createRouter({
  history: createWebHistory(),
  routes: pwaRoutes,

  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0 }
  }
})

/**
 * 🔹 EXTENSION POINT — Navigation Pipeline
 * Aquí se conectará el sistema de guards sin tocar el core.
 */
router.beforeEach(async (to, from, next) => {
  // 🚧 BYPASS MODE
  return next()

  // 🔒 FUTURO (activar cuando esté listo)
  // try {
  //   await runGuards(to, from)
  //   next()
  // } catch (e) {
  //   next(e.redirect || '/')
  // }
})

export default router