/**
 * ════════════════════════════════════════════════════════════════
 * 🧭 KUBIX — Zone Service (TERRITORIAL UI GATEWAY v1.0)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Proveer datos jerárquicos para UI territorial.
 * - País → Estado → Ciudad → Barrio.
 * - Servir como API layer para ZoneStore.
 *
 * ARQUITECTURA:
 * - ZoneService = API LAYER (UI selection data)
 * - ZoneStore = STATE LAYER (selección del usuario)
 * - ContextStore = SOURCE OF TRUTH (path)
 *
 * PRINCIPIO:
 * - Solo datos de navegación territorial
 * - Sin lógica de negocio
 * - Respuesta normalizada
 *
 * UBICACIÓN: /Kubix/core/services/zoneService.js
 * ════════════════════════════════════════════════════════════════
 */

import { api } from '@/Kubix/core/api'

const ZoneService = {

  // 🌍 COUNTRIES
  async getCountries() {
    try {
      const res = await api.get('/territories/countries')
      return this._normalize(res)
    } catch (err) {
      this._log('getCountries failed', err)
      return []
    }
  },

  // 🗺️ STATES
  async getStates(country) {
    try {
      const res = await api.get('/territories/states', {
        params: { country }
      })
      return this._normalize(res)
    } catch (err) {
      this._log('getStates failed', err)
      return []
    }
  },

  // 🏙️ CITIES
  async getCities(country, state) {
    try {
      const res = await api.get('/territories/cities', {
        params: { country, state }
      })
      return this._normalize(res)
    } catch (err) {
      this._log('getCities failed', err)
      return []
    }
  },

  // 🏘️ NEIGHBORHOODS
  async getNeighborhoods(city) {
    try {
      const res = await api.get('/territories/neighborhoods', {
        params: { city }
      })
      return this._normalize(res)
    } catch (err) {
      this._log('getNeighborhoods failed', err)
      return []
    }
  },

  // 🧠 NORMALIZER
  _normalize(res) {
    return res?.data?.data ?? res?.data ?? res
  },

  // 🪵 LOG
  _log(msg, err) {
    console.warn(`[ZoneService] ${msg}`, err?.message || err)
  }
}

export default ZoneService