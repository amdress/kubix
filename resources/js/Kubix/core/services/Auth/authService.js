/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Auth Service (TERRITORIAL IDENTITY GATEWAY vFINAL)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Gestionar autenticación básica (login / logout / session)
 * - Proveer identidad del usuario autenticado
 * - Soportar flujo pre-login → post-login
 * - Servir como puente hacia AuthStore
 *
 * ARQUITECTURA:
 * - AuthService = API LAYER (HTTP puro)
 * - AuthStore = STATE LAYER (sesión + identidad)
 * - ContextStore = TERRITORIAL LAYER (ubicación)
 *
 * PRINCIPIOS:
 * - Sin lógica de negocio compleja
 * - Respuesta normalizada
 * - Preparado para expansión (refresh token, social login)
 *
 * UBICACIÓN: /Kubix/core/services/authService.js
 * ════════════════════════════════════════════════════════════════
 */

import { api } from '@/Kubix/core/api'
import { isMobile } from '@/Kubix/core/utils/device'

const DEVICE_NAME = isMobile() ? 'mobile_pwa' : 'web_platform'

const AuthService = {

  /**
   * 🔐 LOGIN
   */
  async login(credentials) {
    try {
      const res = await api.post('/login', {
        email: credentials.email,
        password: credentials.password,
        device_name: DEVICE_NAME
      })

      return this._normalize(res)
    } catch (error) {
      this._log('Login failed', error)
      throw error
    }
  },

  /**
   * 🚪 LOGOUT
   */
  async logout() {
    try {
      const res = await api.post('/logout')
      return this._normalize(res)
    } catch (error) {
      this._log('Logout failed', error)
      throw error
    }
  },

  /**
   * 👤 GET CURRENT USER (SESSION RESTORE)
   * CRÍTICO: usado en bootstrap / app init
   */
  async me() {
    try {
      const res = await api.get('/me')
      return this._normalize(res)
    } catch (error) {
      this._log('Session restore failed', error)
      throw error
    }
  },

  /**
   * 🔄 REFRESH TOKEN (PREPARADO FUTURO)
   */
  async refresh() {
    try {
      const res = await api.post('/refresh')
      return this._normalize(res)
    } catch (error) {
      this._log('Token refresh failed', error)
      throw error
    }
  },

  /**
   * 🧠 NORMALIZER (CONSISTENCIA DE DATA)
   */
  _normalize(res) {
    return res?.data?.data ?? res?.data ?? res
  },

  /**
   * 🪵 LOGGING
   */
  _log(msg, error) {
    console.error(`[AuthService] ${msg}`, error?.message || error)
  }
}

export default AuthService