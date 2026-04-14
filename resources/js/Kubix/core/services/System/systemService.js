/**
 * ════════════════════════════════════════════════════════════════
 * 🧪 KUBIX — System Service
 * ════════════════════════════════════════════════════════════════
 *
 * UBICACIÓN:
 * /Kubix/core/services/system/systemService.js
 *
 * RESPONSABILIDAD:
 * Proveer endpoints técnicos del sistema (health, status, etc).
 *
 * FILOSOFÍA:
 * - Usa el cliente API ya configurado (core/api)
 * - No contiene lógica de UI
 * - No maneja estados globales
 *
 * MÉTODOS:
 * - ping → Verifica conectividad con el backend
 *
 * ════════════════════════════════════════════════════════════════
 */

import { api } from '@/Kubix/core/api'

/**
 * 🧪 Verifica si el backend está disponible
 *
 * @returns {Promise<Object>} Respuesta del backend
 */
async function ping() {
  const response = await api.get('/ping')
  return response.data
}

export default {
  ping,
}