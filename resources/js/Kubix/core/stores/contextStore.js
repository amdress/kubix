/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Context Store (TERRITORIAL ORCHESTRATOR vFINAL)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Definir y controlar el contexto global de la aplicación.
 * - Resolver ubicación del usuario (GPS + selección manual).
 * - Mantener el estado territorial base (current_path).
 * - Orquestar el modo de experiencia (social / workspace).
 * - Servir como fuente única de verdad para el territorio activo.
 *
 * ARQUITECTURA:
 * - ContextStore = ORQUESTADOR GLOBAL (decide el territorio)
 * - SocialStore = MOTOR DE CONSUMO (usa el territorio)
 * - AuthStore = IDENTIDAD (define el usuario)
 *
 * FLUJO:
 * - Pre-login: detecta ubicación inicial (GPS / fallback)
 * - Post-login: puede ser sobreescrito por identidad social
 * - Cambios de path → disparan rehidratación en SocialStore
 *
 * UBICACIÓN: /Kubix/core/stores/contextStore.js
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import { isMobile } from '@/Kubix/core/utils/device'
import TerritoryService from '@/Kubix/core/services/Territory/TerritoryService'
import { LocationManager } from '@/Kubix/core/utils/location/location'
import { useSocialStore } from '@/Kubix/core/stores/socialStore'

const STORAGE_SELECTED_PATH = 'kubix_selected_path'
const STORAGE_MODE = 'kubix_mode'

export const useContextStore = defineStore('context', {
  state: () => ({
    // 🚀 UI & BOOT
    showSplash: false,
    bootMessage: 'Iniciando sistemas...',
    is_ready: false,
    error: null,
    isLoading: false,

    // 🌍 TERRITORY
    current_path: null,
    real_path: null,
    selected_path: null,

    // 🧠 CONTROL
    isUserOverride: false,
    isUpdatingLocation: false,

    // 🧾 DATA
    territory_data: null,

    // 🛠️ MODE
    mode: (typeof window !== 'undefined')
      ? localStorage.getItem(STORAGE_MODE) || 'social'
      : 'social',

    // 📱 DEVICE
    isMobile: isMobile(),
  }),

  getters: {
    city: (s) => {
      const c = s.territory_data?.data?.branding?.city
      return {
        name: c?.name || "Curitiba",
        primary_color: c?.primary_color || "#3B82F6",
        splash_image: c?.splash_image?.replace('http://', 'https://') || null,
      }
    },

    neighborhood: (s) => {
      const n = s.territory_data?.data?.branding?.neighborhood
      return {
        name: n?.name || "",
        primary_color: n?.primary_color || null,
        splash_image: n?.splash_image?.replace('http://', 'https://') || null,
        is_active: !!n?.is_active,
        welcome_message: n?.welcome_message || ""
      }
    },

    isSocial: (s) => s.mode === 'social',
    isWorkspace: (s) => s.mode === 'workspace',

    accentColor: (s) => {
      if (s.mode === 'workspace') return '#10B981'
      return s.neighborhood.primary_color || s.city.primary_color || '#3B82F6'
    },

    isViewingRealLocation: (s) => s.current_path === s.real_path,
  },

  actions: {
    // ─────────────────────────────────────────
    // 🚀 BOOTSTRAP
    // ─────────────────────────────────────────

    async init() {
      if (this.is_ready) return
      this.is_loading = true

      try {
        this.setBootMessage("Localizando coordenadas...")
        const coords = await LocationManager.getRawCoords().catch(() => null)

        this.setBootMessage("Identificando territorio...")
        const res = await TerritoryService.checkAvailability(coords || {})

        if (res?.status === "success" && res?.data?.exists) {
          this.territory_data = res
          this.real_path = res.data.context.current_path

          const savedSelected = localStorage.getItem(STORAGE_SELECTED_PATH)

          if (savedSelected) {
            try {
              await this.setSelectedPath(savedSelected, true, true)
            } catch {
              localStorage.removeItem(STORAGE_SELECTED_PATH)
              this.current_path = this.real_path
              this.selected_path = this.real_path
              this.isUserOverride = false
            }
          } else {
            this.current_path = this.real_path
            this.selected_path = this.real_path
            this.isUserOverride = false
          }

          this.setBootMessage(`Bienvenido a ${this.city.name}`)
        } else {
          this.error = "UBICACION_NO_SOPORTADA"
        }

      } catch (err) {
        this.error = "ERROR_CONEXION"
        throw err
      } finally {
        this.is_loading = false
        this.is_ready = true
        setTimeout(() => { this.showSplash = false }, 800)
      }
    },

    // ─────────────────────────────────────────
    // 🔄 POST-LOGIN
    // ─────────────────────────────────────────

    async initializeExperience(identities) {
      const social = useSocialStore()
      const workspace = useWorkspaceStore()

      this._log("Inicializando experiencia...", "#6366f1")

      if (identities?.social?.path) {
        await this.setSelectedPath(identities.social.path, false, false)
      }

      if (identities?.social) social.setIdentity(identities.social)
      if (identities?.workspace) workspace.setIdentities(identities.workspace)

      if (this.mode === 'workspace' && (!identities.workspace || identities.workspace.length === 0)) {
        this.setMode('social')
      }

      this._log("Experiencia lista", "#10b981")
    },

    // ─────────────────────────────────────────
    // 📍 GPS WATCH
    // ─────────────────────────────────────────

    startLocationWatch() {
      LocationManager.startWatching(this._handleLocationUpdate.bind(this))
    },

    stopLocationWatch() {
      LocationManager.stopWatching()
      this._log('GPS watch detenido', '#f87171')
    },

    async _handleLocationUpdate(coords) {
      if (this.isUpdatingLocation) return
      this.isUpdatingLocation = true

      try {
        const res = await TerritoryService.checkAvailability(coords)
        if (res?.status !== 'success' || !res?.data?.exists) return

        const newRealPath = res.data.context.current_path
        this.real_path = newRealPath

        if (!this.isUserOverride) {
          this.territory_data = res
          this.current_path = newRealPath
          this.selected_path = newRealPath
          this._log(`Auto-update GPS → ${newRealPath}`, '#10b981')
        } else {
          this._log('Override activo, GPS solo actualiza real_path', '#eab308')
        }

      } catch (e) {
        this._log('Error en actualización GPS', '#ef4444')
      } finally {
        this.isUpdatingLocation = false
      }
    },

    // ─────────────────────────────────────────
    // 🧭 USER CONTROL
    // ─────────────────────────────────────────

    async setSelectedPath(path, isInitialBoot = false, isUserAction = true) {
      if (!path) return
      if (this.is_loading) return
      if (!isInitialBoot && path === this.current_path) return

      this.is_loading = true

      try {
        const res = await TerritoryService.checkAvailability({ path })

        if (res?.status === "success") {
          this.territory_data = res
          this.selected_path = path
          this.current_path = path
          this.isUserOverride = isUserAction

          localStorage.setItem(STORAGE_SELECTED_PATH, path)

          this._log(`Cambio de contexto → ${path}`, '#f59e0b')
        }
      } catch (e) {
        this._log("Error al cambiar territorio", "#ef4444")
        throw e
      } finally {
        this.is_loading = false
      }
    },

    async resetToRealLocation() {
      this._log('Volviendo a ubicación GPS...', '#22c55e')

      localStorage.removeItem(STORAGE_SELECTED_PATH)

      await this.setSelectedPath(this.real_path, true, false)
    },



    // ─────────────────────────────────────────
    // 🎛️ UI HELPERS
    // ─────────────────────────────────────────

    setBootMessage(msg) {
      this.bootMessage = msg
    },

    setMode(newMode) {
      if (!['social', 'workspace'].includes(newMode)) return

      this.mode = newMode
      localStorage.setItem(STORAGE_MODE, newMode)

      this._log(`MODO → ${newMode.toUpperCase()}`, "#a78bfa")
    },

    setLoading(status) {
      this.isLoading = status
    },

    setSplash(status) { 
      this.showSplash = status
    },

    _log(msg, color = "#94a3b8") {
      console.log(`%c[Context] ${msg}`, `color:${color}; font-weight:bold;`)
    }
  }
})