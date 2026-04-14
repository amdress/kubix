/**
 * ════════════════════════════════════════════════════════════════
 * 🌐 KUBIX — API Client
 * ════════════════════════════════════════════════════════════════
 *
 * UBICACIÓN:
 * /Kubix/core/api/client.js
 *
 * RESPONSABILIDAD:
 * Crear la instancia base de Axios utilizando la configuración
 * definida en settings.js.
 *
 * FILOSOFÍA:
 * - Minimalista: Solo construye el cliente HTTP.
 * - Desacoplado: No conoce interceptors ni lógica de negocio.
 * - Base limpia: Punto de partida para setup.js.
 *
 * CONTIENE:
 * - Instancia Axios configurada con:
 *   → baseURL
 *   → timeout
 *   → headers base
 *   → configuración Sanctum
 *
 * NO CONTIENE:
 * - Interceptors
 * - Stores
 * - Lógica de negocio
 * - Logging
 *
 * ════════════════════════════════════════════════════════════════
 */

import axios from 'axios'
import { API_SETTINGS } from './settings'

// ────────────────────────────────────────────────────────────────
// 🚀 INSTANCIA BASE
// ────────────────────────────────────────────────────────────────
const apiClient = axios.create({
  baseURL: API_SETTINGS.baseURL,
  timeout: API_SETTINGS.timeouts.STANDARD,

  headers: API_SETTINGS.headers,

  // 🔐 Laravel Sanctum
  withCredentials: API_SETTINGS.sanctum.withCredentials,
  xsrfCookieName: API_SETTINGS.sanctum.xsrfCookieName,
  xsrfHeaderName: API_SETTINGS.sanctum.xsrfHeaderName,
})

export default apiClient