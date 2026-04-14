/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Workspace Store (FINAL - ECONOMIC ENGINE)
 * ════════════════════════════════════════════════════════════════
 *
 * FILOSOFÍA:
 * - Workspace = Dashboard del dueño dentro de KUBIX.
 * - NO gestiona el negocio interno (eso es Solutions).
 * - Gestiona:
 *   → Visibilidad (Ads)
 *   → Impacto (métricas sociales)
 *   → Presencia (branding + directory)
 *   → Monetización (wallet tipo InDriver)
 *   → Expansión (unidades por territorio)
 *
 * ARQUITECTURA:
 * - Company → Identidad (marca)
 * - Unit → Presencia territorial (por path)
 * - ZoneStore → Define alcance (paths)
 * - SocialStore ← se hidrata desde aquí
 *
 * FLUJO:
 * Workspace → publish → Context/Zone → Social
 *
 * UBICACIÓN:
 * /Kubix/core/stores/workspaceStore.js
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import WorkSpaceService from '@/Kubix/core/services/WorkSpace/WorkSpaceService'
import { useZoneStore } from '@/Kubix/core/stores/zoneStore'

export const useWorkSpaceStore = defineStore('workspace', {

  state: () => ({
    /* 🏢 UNIVERSO DEL USUARIO */
    companies: [], // [{ id, name, branding, units: [] }]
    activeCompanyId: null,
    activeUnitId: null,

    /* 💰 WALLET (MODELO COMBUSTIBLE) */
    wallet: {
      balance: 0,
      pending_debts: 0,
      fuel_status: 'green', // green | orange | red
      history: [],

      // 🔥 Inteligencia de consumo
      burn_rate_per_day: 0,
      estimated_days_left: 0
    },

    /* 🎛️ CONTROL DE UNIDAD (LO QUE IMPACTA SOCIAL) */
    unit_control: {
      activations: {
        ads: [],
        jobs: [],
        events: []
      },

      modules: [],

      appearance: {
        template_id: 'default',
        config: {}
      },

      // 🔥 Estado real de visibilidad
      status: {
        has_active_ads: false,
        expires_at: null
      }
    },

    /* 📊 MÉTRICAS (FEEDBACK DEL SOCIAL) */
    analytics: {
      views: { total: 0, history: [] },
      likes: 0,
      saves: 0,
      qr_scans: 0,
      heat_map: [],
      top_actions: []
    },

    /* 📥 COMUNICACIÓN DE NEGOCIO */
    inbox: {
      leads: [],
      applications: [],
      system_alerts: []
    },

    /* 🤝 RADAR B2B */
    supply_radar: {
      nearby_suppliers: [],
      active_connections: []
    },

    loading: false,
    initialized: false
  }),

  /* ─────────────────────────────────────────
     GETTERS
     ───────────────────────────────────────── */
  getters: {

    /* 🏢 Empresa activa */
    currentCompany: (state) =>
      state.companies.find(c => c.id === state.activeCompanyId) || null,

    /* 📍 Unidad activa (FIX aplicado) */
    currentUnit: (state) => {
      const company = state.companies.find(c => c.id === state.activeCompanyId)
      if (!company) return null
      return company.units?.find(u => u.id === state.activeUnitId) || null
    },

    /* 🎨 Branding listo para UI */
    branding: (state) => {
      const company = state.companies.find(c => c.id === state.activeCompanyId)
      const b = company?.branding || {}

      return {
        name: company?.trade_name || "Nombre Negocio",
        logo: b.logo || null,
        primary_color: b.primary_color || "#3B82F6",
        splash: b.splash ? b.splash.replace('http://', 'https://') : null,
        social: company?.social_links || {}
      }
    },

    /* 🚥 Estado de visibilidad */
    visibilityStatus: (state) => {
      const hasFunds = state.wallet.balance > 0
      const hasAds = state.unit_control.status.has_active_ads

      return {
        is_visible: hasFunds && hasAds,
        label: (hasFunds && hasAds)
          ? 'En el Mapa'
          : 'Solo Directorio',
        color: hasFunds
          ? (hasAds ? '#10B981' : '#F59E0B')
          : '#EF4444'
      }
    },

    /* 🔥 Unidad activa realmente visible */
    isUnitActive: (state) => {
      return (
        state.unit_control.status.has_active_ads &&
        state.wallet.balance > 0
      )
    },

    /* 📊 Resumen rápido */
    performanceSummary: (state) => ({
      reach: state.analytics.views.total,
      engagement: state.analytics.likes + state.analytics.saves,
      conversion: state.analytics.qr_scans,
      unread_leads: state.inbox.leads.filter(l => !l.read).length
    }),

    /* 💸 Presupuesto */
    weeklyBudget: (state) => {
      const ads = state.unit_control.activations.ads.reduce(
        (acc, ad) => acc + (ad.cost || 0), 0
      )

      const modules = state.unit_control.modules.reduce(
        (acc, m) => acc + (m.weekly_price || 0), 0
      )

      return ads + modules
    }
  },

  /* ─────────────────────────────────────────
     ACTIONS
     ───────────────────────────────────────── */
  actions: {

    /* 🚀 Init */
    async init() {
      this.loading = true
      try {
        const data = await WorkSpaceService.getOwnerDashboard()

        this.companies = data.companies || []
        this.wallet = { ...this.wallet, ...data.wallet }

        this.initialized = true
      } finally {
        this.loading = false
      }
    },

    /* 🎯 Selección de unidad */
    async selectUnit(companyId, unitId) {
      this.activeCompanyId = companyId
      this.activeUnitId = unitId

      this.loading = true

      try {
        const [detail, stats, inbox] = await Promise.all([
          WorkSpaceService.getUnitDetail(unitId),
          WorkSpaceService.getUnitStats(unitId),
          WorkSpaceService.getBusinessInbox(unitId)
        ])

        this.unit_control = detail
        this.analytics = stats
        this.inbox = inbox

        await this.scanNearbySuppliers()

      } finally {
        this.loading = false
      }
    },

    /* 📣 PUBLICAR EN SOCIAL (FIX ZONE INTEGRADO) */
    async publishToSocial(type, payload) {
      if (!this.activeUnitId) return

      const zoneStore = useZoneStore()

      const response = await WorkSpaceService.publish({
        unitId: this.activeUnitId,
        type,
        payload,
        target_paths: zoneStore.selectedPaths // 🔥 CLAVE
      })

      if (response?.success) {
        this.unit_control.activations[type + 's'].push(response.data)

        if (response.new_balance !== undefined) {
          this.wallet.balance = response.new_balance
        }
      }

      return response
    },

    /* 🛒 SOLUCIONES */
    async rentSolution(moduleId, category = 'module') {
      const response = await WorkSpaceService.activateModule(
        this.activeUnitId,
        moduleId
      )

      if (response?.success) {
        if (category === 'template') {
          this.unit_control.appearance.template_id = moduleId
        } else {
          this.unit_control.modules.push(response.module)
        }
      }
    },

    /* 🤝 B2B */
    async scanNearbySuppliers() {
      const unit = this.currentUnit
      if (!unit) return

      this.supply_radar.nearby_suppliers =
        await WorkSpaceService.findSuppliersByPath(unit.path)
    },

    /* 💵 WALLET */
    async topUpWallet(amount) {
      return await WorkSpaceService.generatePixCharge(amount)
    }
  }
})