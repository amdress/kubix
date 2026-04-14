/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Auth Orchestrator (REFACTORIZADO SIN solutionsStore)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD ÚNICA:
 * Orquestar el flujo completo de autenticación.
 *
 * ¿QUÉ HACE?
 * ✅ Orquesta: AuthService → stores → retorna resultado
 * ✅ Manejo de errores
 * ✅ Logging de estado
 *
 * ¿QUÉ NO HACE?
 * ❌ HTTP calls (eso es AuthService)
 * ❌ Modificar stores directamente en profundidad
 * ❌ Redireccionamiento (eso es router guard)
 * ❌ Lógica de UI (eso es componente)
 *
 * ARQUITECTURA:
 * Componente → useAuthOrchestrator → AuthService → API
 *           ↓
 *        authStore + contextStore
 *
 * FLUJO DE LOGIN:
 * 1. Componente llama: authOrch.login(credentials)
 * 2. authOrch llama: AuthService.login(credentials)
 * 3. authOrch recibe: { user, token, context }
 * 4. authOrch escribe: authStore.setAuthData(user, token)
 *                       contextStore.initializeFromAuth(response)
 * 5. authOrch retorna: { success, user, error }
 * 6. Router guard verifica authStore.isAuthenticated
 * 7. Router redirige automáticamente
 *
 * CAMBIOS:
 * ❌ Eliminado: import useSolutionsStore
 * ❌ Eliminado: solutionsStore.setSolutions() en login/register
 * ✅ AHORA: contextStore.initializeFromAuth() maneja TODO (role, permisos, empresas, soluciones)
 *
 * ════════════════════════════════════════════════════════════════
 */

import { ref } from 'vue'
import AuthService from '@/Kubix/core/services/auth/authService'
import { useAuthStore } from '@/Kubix/core/stores/auth'
import { useContextStore } from '@/Kubix/core/stores/context'

// ❌ solutionsStore ELIMINADO COMPLETAMENTE
// Las soluciones ahora están en: contextStore.userEnterprises[].contracts

export function useAuthOrchestrator() {
  // ════════════════════════════════════════════════════════════
  // STATE
  // ════════════════════════════════════════════════════════════

  const isProcessing = ref(false)
  const error = ref(null)

  // ════════════════════════════════════════════════════════════
  // LOGIN ORCHESTRATION
  // ════════════════════════════════════════════════════════════

  /**
   * login
   *
   * ORQUESTA el flujo completo de login.
   *
   * RESPONSABILIDADES:
   * 1. Validar input básico
   * 2. Llamar AuthService.login()
   * 3. Distribuir respuesta a stores
   * 4. Manejar errores
   * 5. Retornar estado para componente/router guard
   *
   * PARÁMETROS:
   * @param {Object} credentials - { email: string, password: string }
   *
   * RETORNA:
   * @returns {Promise<Object>}
   *   {
   *     success: boolean,
   *     user: { id, name, role } | null,
   *     error: string | null
   *   }
   *
   * FLUJO:
   * 1. Validar credenciales
   * 2. Llamar AuthService.login()
   * 3. authStore.setAuthData(user, token) ← Guarda credenciales
   * 4. contextStore.initializeFromAuth(response) ← Inicializa TODO:
   *    - role, permissions
   *    - userEnterprises, contracts (SOLUCIONES)
   *    - activeTerritory, branding
   *    - capacidades (canAccessBusiness, canAccessAdmin)
   * 5. Retornar { success: true, user }
   */
  async function login(credentials) {
    isProcessing.value = true
    error.value = null

    try {
      // ── VALIDACIÓN BÁSICA ──────────────────────────────────
      if (!credentials?.email || !credentials?.password) {
        throw new Error('Email y contraseña son requeridos')
      }

      console.log(
        '%c🔐 [AuthOrch] Iniciando login...',
        'color: #3b82f6; font-weight: bold'
      )

      // ── LLAMAR SERVICE ─────────────────────────────────────
      const response = await AuthService.login(credentials)

      if (!response?.user || !response?.token) {
        throw new Error('Respuesta inválida del servidor')
      }

      console.log(
        '%c📡 [AuthOrch] Respuesta recibida, distribuyendo a stores...',
        'color: #3b82f6'
      )

      // ── DISTRIBUIR A STORES ────────────────────────────────
      const authStore = useAuthStore()
      const contextStore = useContextStore()

      console.log('datos en contextStore : ', contextStore)

      // Auth: Usuario e identidad
      authStore.setAuthData(response.user, response.token)

      // Context: TODO (role, permisos, empresas, soluciones, territorio, branding)
      // ✅ contextStore.initializeFromAuth() maneja TODA la inicialización
      try {
        if (response.context) {
          contextStore.initializeFromAuth(response)
        } else {
          console.warn(
            '%c⚠️ [AuthOrch] Response sin context, saltando initializeFromAuth',
            'color: #f59e0b'
          )
        }
      } catch (contextErr) {
        console.warn(
          '%c⚠️ [AuthOrch] Error en initializeFromAuth:',
          'color: #f59e0b',
          contextErr.message
        )
        // No bloqueamos login por esto
      }

      // ❌ NO NECESITAMOS solutionsStore.setSolutions()
      // Las soluciones ya están en contextStore.userEnterprises[].contracts

      console.log(
        '%c✅ [AuthOrch] Login completado',
        'color: #10b981; font-weight: bold'
      )
      console.log(
        `%c   Usuario: %c${response.user.name} (${response.user.role || 'sin rol'})`,
        'color: #10b981',
        'color: #94a3b8'
      )

      // ── RETORNAR ÉXITO ─────────────────────────────────────
      return {
        success: true,
        user: response.user,
        error: null,
      }
    } catch (err) {
      // ── MANEJO DE ERRORES ──────────────────────────────────
      const errorMessage = err.response?.data?.message || err.message

      error.value = errorMessage

      console.error(
        '%c❌ [AuthOrch] Error en login:',
        'color: #ef4444; font-weight: bold',
        errorMessage
      )

      // Limpiar parcialmente (sin redirigir)
      useAuthStore().forceLogout()

      // ── RETORNAR ERROR ─────────────────────────────────────
      return {
        success: false,
        user: null,
        error: errorMessage,
      }
    } finally {
      isProcessing.value = false
    }
  }

  // ════════════════════════════════════════════════════════════
  // LOGOUT ORCHESTRATION
  // ════════════════════════════════════════════════════════════

  /**
   * logout
   *
   * ORQUESTA el logout completo.
   *
   * RESPONSABILIDADES:
   * 1. Notificar al backend
   * 2. Limpiar stores
   * 3. Manejar errores (no bloquear logout)
   *
   * FLUJO:
   * AuthService.logout()  ← puede fallar, pero continuamos
   *     ↓
   * authStore.forceLogout()
   *     ↓
   * Retorna
   *     ↓
   * Router guard redirige a login
   *
   * IMPORTANTE:
   * - Aunque backend falle, logout igual (limpieza local)
   * - No redirige aquí (lo hace router guard)
   */
  async function logout() {
    console.log(
      '%c🚪 [AuthOrch] Iniciando logout...',
      'color: #ef4444; font-weight: bold'
    )

    try {
      // Notificar al backend (puede fallar, es ok)
      await AuthService.logout()
      console.log(
        '%c✅ [AuthOrch] Backend notificado',
        'color: #10b981'
      )
    } catch (err) {
      console.warn(
        '%c⚠️ [AuthOrch] Backend logout falló (continuamos):',
        'color: #f59e0b',
        err.message
      )
      // Continuamos de todas formas
    } finally {
      // Limpiar estado local SIEMPRE
      useAuthStore().forceLogout()
      console.log(
        '%c🗑️ [AuthOrch] Estado local limpiado',
        'color: #f59e0b'
      )
    }
  }

  // ════════════════════════════════════════════════════════════
  // REGISTER ORCHESTRATION
  // ════════════════════════════════════════════════════════════

  /**
   * register
   *
   * ORQUESTA el flujo de registro.
   *
   * RESPONSABILIDADES:
   * 1. Validar input
   * 2. Llamar AuthService.register()
   * 3. Distribuir a stores
   * 4. Manejar errores
   *
   * PARÁMETROS:
   * @param {Object} userData
   *   {
   *     name: string,
   *     email: string,
   *     password: string,
   *     password_confirmation: string,
   *     avatar: File (opcional)
   *   }
   *
   * RETORNA:
   * @returns {Promise<Object>}
   *   {
   *     success: boolean,
   *     user: { id, name, role } | null,
   *     error: string | null
   *   }
   */
  async function register(userData) {
    isProcessing.value = true
    error.value = null

    try {
      // ── VALIDACIÓN BÁSICA ──────────────────────────────────
      if (!userData?.name || !userData?.email || !userData?.password) {
        throw new Error('Nombre, email y contraseña son requeridos')
      }

      if (userData.password !== userData.password_confirmation) {
        throw new Error('Las contraseñas no coinciden')
      }

      console.log(
        '%c✨ [AuthOrch] Iniciando registro...',
        'color: #3b82f6; font-weight: bold'
      )

      // ── LLAMAR SERVICE ─────────────────────────────────────
      const response = await AuthService.register(userData)

      if (!response?.user || !response?.token) {
        throw new Error('Respuesta inválida del servidor')
      }

      console.log(
        '%c📡 [AuthOrch] Respuesta recibida, distribuyendo a stores...',
        'color: #3b82f6'
      )

      // ── DISTRIBUIR A STORES ────────────────────────────────
      const authStore = useAuthStore()
      const contextStore = useContextStore()

      authStore.setAuthData(response.user, response.token)

      // ✅ contextStore.initializeFromAuth() maneja TODO
      if (response.context) {
        contextStore.initializeFromAuth(response)
      }

      // ❌ NO NECESITAMOS solutionsStore.setSolutions()

      console.log(
        '%c✅ [AuthOrch] Registro completado',
        'color: #10b981; font-weight: bold'
      )
      console.log(
        `%c   Usuario nuevo: %c${response.user.name}`,
        'color: #10b981',
        'color: #94a3b8'
      )

      return {
        success: true,
        user: response.user,
        error: null,
      }
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message

      error.value = errorMessage

      console.error(
        '%c❌ [AuthOrch] Error en registro:',
        'color: #ef4444; font-weight: bold',
        errorMessage
      )

      useAuthStore().forceLogout()

      return {
        success: false,
        user: null,
        error: errorMessage,
      }
    } finally {
      isProcessing.value = false
    }
  }

  // ════════════════════════════════════════════════════════════
  // REHYDRATION
  // ════════════════════════════════════════════════════════════

  /**
   * rehydrate
   *
   * ORQUESTA la restauración de sesión en bootstrap.
   *
   * FLUJO:
   * app.js FASE 4
   *     ↓
   * authOrch.rehydrate()
   *     ↓
   * authStore.rehydrate()  ← restaura desde localStorage
   *     ↓
   * Si no hay sesión anterior → return { isValid: false }
   *
   * Si hay sesión anterior:
   *     ↓
   * AuthService.checkSession()  ← valida token
   *     ↓
   * Si token válido:
   *     ├─→ contextStore.rehydrate()  ← restaura empresas, soluciones, etc
   *     └─→ return { isValid: true }
   *
   * Si token inválido:
   *     ├─→ authStore.forceLogout()
   *     └─→ return { isValid: false }
   *
   * @returns {Promise<Object>}
   *   { isValid: boolean }
   */
  async function rehydrate() {
    console.log(
      '%c🔄 [AuthOrch] Rehidratando sesión...',
      'color: #3b82f6; font-weight: bold'
    )

    try {
      // Restaurar auth
      const authStore = useAuthStore()
      authStore.rehydrate()

      // Si no hay sesión anterior
      if (!authStore.isAuthenticated) {
        console.log(
          '%c👋 [AuthOrch] Sin sesión anterior',
          'color: #94a3b8'
        )
        return { isValid: false }
      }

      console.log(
        '%c✓ [AuthOrch] Sesión anterior encontrada, validando token...',
        'color: #3b82f6'
      )

      // Validar token
      const isValid = await AuthService.checkSession()

      if (!isValid) {
        console.warn(
          '%c⚠️ [AuthOrch] Token expiró',
          'color: #f59e0b'
        )
        authStore.forceLogout()
        return { isValid: false }
      }

      console.log(
        '%c✅ [AuthOrch] Token válido, restaurando contexto...',
        'color: #10b981'
      )

      // Restaurar otros stores
      const contextStore = useContextStore()

      contextStore.rehydrate()
      
      // ❌ NO NECESITAMOS solutionsStore.rehydrate()
      // Las soluciones están en contextStore.userEnterprises[].contracts

      console.log(
        '%c✅ [AuthOrch] Rehidratación completada',
        'color: #10b981; font-weight: bold'
      )

      return { isValid: true }
    } catch (err) {
      console.error(
        '%c❌ [AuthOrch] Error rehidratando:',
        'color: #ef4444',
        err.message
      )
      useAuthStore().forceLogout()
      return { isValid: false }
    }
  }

  // ════════════════════════════════════════════════════════════
  // RETURN
  // ════════════════════════════════════════════════════════════

  return {
    // State
    isProcessing,
    error,

    // Actions
    login,
    logout,
    register,
    rehydrate,
  }
}