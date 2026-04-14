/**
 * ════════════════════════════════════════════════════════════════
 * 🧠 KUBIX — API Setup (Orchestrator)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/core/api/setup.js
 * ════════════════════════════════════════════════════════════════
 */

import apiClient from './client'
import { setupRequestInterceptor } from './interceptors/request.interceptor'
import { setupAuthInterceptor } from './interceptors/auth.interceptor'
import { setupLoadingInterceptor } from './interceptors/loading.interceptor'
import { setupResponseInterceptor } from './interceptors/response.interceptor'
import { setupErrorInterceptor } from './interceptors/error.interceptor'

/**
 * Ensambla la instancia de API con sus interceptores siguiendo un orden lógico.
 * * ORDEN DE EJECUCIÓN:
 * 1. Request (Logs/Debug)
 * 2. Auth (Seguridad/Tokens)
 * 3. Loading (Inicio de Spinner)
 * --- Vuelo de la petición ---
 * 4. Response (Logs/Normalización de datos)
 * 5. Error (Reacción/Redirecciones/Alertas)
 * 6. Loading (Fin de Spinner)
 */
export function setupApi({
  getToken,
  onStart,
  onFinish,
  onUnauthorized,
  onGlobalError,
  // Fallbacks opcionales para mayor flexibilidad
  authStore,
  contextStore,
} = {}) {

  // Normalización de Providers
  const _getToken = getToken || (() => authStore?.token || null)
  const _onStart = onStart || (() => contextStore?.setLoading(true))
  const _onFinish = onFinish || (() => contextStore?.setLoading(false))

  // ─────────────────────────────────────────
  // VALIDACIÓN DE CONTRATO
  // ─────────────────────────────────────────
  if (typeof _getToken !== 'function') {
    throw new Error('[KUBIX API] setupApi requiere una función getToken válida.');
  }

  // ─────────────────────────────────────────
  // ENSAMBLAJE DE CAPAS (Pipeline)
  // ─────────────────────────────────────────

  // A. CAPA DE SALIDA (Request)
  setupRequestInterceptor(apiClient)
  setupAuthInterceptor(apiClient, { getToken: _getToken })
  
  // B. CAPA DE UX (Loading)
  // Se registra después para que capture el config final
  setupLoadingInterceptor(apiClient, { 
    onStart: _onStart, 
    onFinish: _onFinish 
  })

  // C. CAPA DE ENTRADA (Response & Errors)
  setupResponseInterceptor(apiClient)
  setupErrorInterceptor(apiClient, { 
    onUnauthorized, 
    onGlobalError 
  })

  if (process.env.NODE_ENV === 'development') {
    console.info('%c ⚙️ [KUBIX:API] Orquestación completada con éxito.', 'color: #8b5cf6; font-weight: bold;');
  }

  return apiClient
}

export default apiClient