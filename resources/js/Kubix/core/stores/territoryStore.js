/**
 * ════════════════════════════════════════════════════════════════
 * 🏛️ KUBIX — Territory Store (MONITOR LAYER vFINAL - PURE)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Visualizar métricas agregadas por territorio.
 * - Navegar jerarquía (país → estado → ciudad → barrio).
 * - Mostrar volumen económico y actividad.
 *
 * NO HACE:
 * - ❌ No gestiona usuarios ni equipos
 * - ❌ No modifica datos
 * - ❌ No interactúa con Context ni Social
 *
 * ES:
 * - 📊 Dashboard de monitoreo territorial
 *
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import TerritoryService from '@/Kubix/core/services/Territory/TerritoryService'

export const useTerritoryStore = defineStore('territory', {

  state: () => ({
    /* ─────────────────────────────────────
       🔐 SCOPE
    ───────────────────────────────────── */
    scope: {
      base_path: '/',
      current_path: '/',
      role: null
    },

    /* ─────────────────────────────────────
       🧾 PATH HUMANO
    ───────────────────────────────────── */
    meta: {
      country: null,
      state: null,
      city: null,
      neighborhood: null
    },

    /* ─────────────────────────────────────
       📊 MÉTRICAS AGREGADAS
    ───────────────────────────────────── */
    metrics: {
      users: 0,
      businesses: 0,
      active_ads: 0,
      revenue: 0
    },

    /* ─────────────────────────────────────
       📊 DISTRIBUCIÓN (HIJOS)
    ───────────────────────────────────── */
    children: [
      // { name: 'Batel', path: '/1/1/1/1/', metrics: {...} }
    ],

    loading: false,
    initialized: false
  }),

  /* ─────────────────────────────────────────
     GETTERS
  ───────────────────────────────────────── */
  getters: {

    depth: (s) => s.scope.current_path.split('/').filter(Boolean).length,

    breadcrumbs: (s) => {
      return [
        s.meta.country,
        s.meta.state,
        s.meta.city,
        s.meta.neighborhood
      ].filter(Boolean)
    },

    hasChildren: (s) => s.children.length > 0
  },

  /* ─────────────────────────────────────────
     ACTIONS
  ───────────────────────────────────────── */
  actions: {

    /**
     * 🔐 Inicializa el scope desde backend
     */
    initScope(scopeData) {
      this.scope = scopeData
      this.scope.current_path = scopeData.base_path
      this.initialized = true
    },

    /**
     * 🧭 Navegar territorio (solo lectura)
     */
    async navigateTo(targetPath) {

      if (!targetPath.startsWith(this.scope.base_path)) {
        console.warn('⛔ Fuera de scope')
        return
      }

      this.loading = true
      this.scope.current_path = targetPath

      try {
        const data = await TerritoryService.getMonitor(targetPath)

        this.meta = data.meta
        this.metrics = data.metrics
        this.children = data.children

      } catch (e) {
        console.error('Territory monitor error:', e)
      } finally {
        this.loading = false
      }
    },

    /**
     * 🔼 Subir nivel
     */
    goUp() {
      const parts = this.scope.current_path.split('/').filter(Boolean)
      parts.pop()

      const newPath = parts.length ? '/' + parts.join('/') + '/' : '/'
      this.navigateTo(newPath)
    }
  }
})