/**
 * ════════════════════════════════════════════════════════════════
 * 🌐 KUBIX — API Entry Point
 * ════════════════════════════════════════════════════════════════
 *
 * UBICACIÓN:
 * /Kubix/core/api/index.js
 *
 * RESPONSABILIDAD:
 * Punto único de acceso a la capa HTTP.
 *
 * EXPORTA:
 * - api        → Cliente HTTP listo para usar
 * - setupApi   → Inicializa interceptores
 * - API_SETTINGS → Configuración base
 *
 * ════════════════════════════════════════════════════════════════
 */

export { default as api } from './client'
export { setupApi } from './setup'
export { API_SETTINGS } from './settings'