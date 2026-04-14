/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Router Index (vCLEAN - BYPASS MODE)
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Navegación directa sin obstrucciones.
 * ESTADO: Middlewares desactivados temporalmente para desarrollo de flujo.
 * ════════════════════════════════════════════════════════════════
 */

import { createRouter, createWebHistory } from 'vue-router'

// RUTAS (Tu archivo de rutas PWA)
import pwaRoutes from '@/Kubix/Pwa/routes'

const router = createRouter({
  history: createWebHistory(),
  routes: pwaRoutes,
})

/**
 * PIPELINE SIMPLIFICADO
 * Actualmente en modo "Puerta Abierta" para facilitar el testeo de los nuevos Stores.
 */
router.beforeEach((to, from, next) => {
  // Por ahora, no hay guardias, no hay peajes.
  // Solo fluidez para validar la inyección de datos en los componentes.
  
  next()
})

/**
 * SCROLL BEHAVIOR (Opcional pero recomendado para PWA)
 * Siempre vuelve al inicio al cambiar de ruta, excepto en navegación "atrás".
 */
router.afterEach((to, from) => {
  window.scrollTo(0, 0)
})

export default router