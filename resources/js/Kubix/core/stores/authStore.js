/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Auth Store 
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * Gestionar la identidad biográfica (User) y la validez técnica 
 * de la sesión (JWT).
 *
 * FILOSOFÍA:
 * - Isolation Total: No depende de otros stores (ni siquiera ContextStore).
 * - Data Carrier: Devuelve el bloque 'identities' sin interpretarlo.
 * - Single Entry Point: UI -> AuthStore -> Service.
 *
 * NOTA IMPORTANTE:
 * - AuthStore NO conoce ContextStore.
 * - El `current_path` debe ser inyectado desde el exterior (UI o middleware).
 * - Esto elimina acoplamiento y facilita testing.
 *
 * FLUJO:
 * UI -> AuthStore.login({ email, password, current_path })
 * -> AuthService -> AuthStore (persistencia)
 * -> devuelve identities -> ContextStore orquesta (fuera de aquí)
 *
 * UBICACIÓN: /Kubix/core/stores/authStore.js
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import  AuthService  from '@/Kubix/core/services/Auth/authService'

const STORAGE_KEY = 'kubix_session_vault'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,         
    token: null,        
    isInitialized: false,
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user?.name || 'Invitado',
    userAvatar: (state) => 
      state.user?.avatar || 
      `https://ui-avatars.com/api/?name=${state.user?.name || 'U'}&background=random`
  },

  actions: {
    /**
     * REHIDRATACIÓN
     * Recupera la sesión persistida en localStorage al iniciar la app.
     */
    init() {
      try {
        const stored = localStorage.getItem(STORAGE_KEY)
        if (!stored) return

        const data = JSON.parse(stored)

        if (data.token && data.user) {
          this.user = data.user
          this.token = data.token
          this._log('Identidad biográfica rehidratada.')
        }
      } catch (e) {
        this._log('Fallo en rehidratación. Purgando...', '#ef4444')
        this.logout()
      } finally {
        this.isInitialized = true
      }
    },

    /**
     * LOGIN
     * Procesa credenciales y recibe el contexto externo (current_path).
     *
     * @param {Object} payload
     * @param {string} payload.email
     * @param {string} payload.password
     * @param {string} payload.current_path - Path calculado en pre-login
     *
     * @returns {Object} identities
     * - Bloque de identidades devuelto por backend.
     * - NO es interpretado aquí.
     */
    async login({ email, password, current_path }) {
      this.loading = true

      try {
        const response = await AuthService.login({
          email,
          password,
          current_path
        })

        const { user, token, identities } = response.data

        // Persistencia de sesión
        this.user = user
        this.token = token
        this._persist()

        this._log(`Acceso validado: ${this.user.name}`)

        /**
         * 🔥 CLAVE:
         * AuthStore NO orquesta.
         * Devuelve identities para que:
         * - ContextStore decida
         * - Otros stores reaccionen
         */
        return identities

      } catch (error) {
        this._log('Error en autenticación.', '#ef4444')
        throw error
      } finally {
        this.loading = false
      }
    },

    /**
     * LOGOUT
     * Invalida la sesión en backend y limpia completamente el frontend.
     */
    async logout() {
      try {
        if (this.token) {
          await AuthService.logout()
        }
      } catch (e) {
        this._log('Servidor no respondió al logout (ignorado)', '#f59e0b')
      } finally {
        // Limpieza total
        this.user = null
        this.token = null
        localStorage.removeItem(STORAGE_KEY)

        this._log('Sesión purgada.', '#ef4444')

        // Hard reset para eliminar cualquier estado en memoria
        window.location.href = '/'
      }
    },

    // ─────────────────────────────────────────
    // SOPORTE INTERNO
    // ─────────────────────────────────────────

    _persist() {
      if (!this.token || !this.user) return

      localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify({
          user: this.user,
          token: this.token
        })
      )
    },

    _log(msg, color = '#10b981') {
      console.log(`%c🔐 [Auth] ${msg}`, `color:${color}; font-weight:bold`)
    }
  }
})