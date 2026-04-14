/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Social Service (UNIFIED TERRITORIAL API GATEWAY v1.2)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Gateway HTTP puro para social engine territorial.
 * - Feed / Directory / Highlights / Interactions.
 *
 * PRINCIPIOS:
 * - Stateless
 * - Deterministic responses
 * - Fail-safe controlado (consistent fallback shapes)
 * - No business logic
 *
 * UBICACIÓN: /Kubix/core/services/social/SocialService.js
 * ════════════════════════════════════════════════════════════════
 */

import { api } from '@/Kubix/core/api'

const SocialService = {

  // ─────────────────────────────────────────
  // 📜 FEEDS
  // ─────────────────────────────────────────
  async getFeed({ type, path, cursor = null }) {
    return this._get('/social/feed', {
      type,
      path,
      cursor
    }, [])
  },

  async getHighlights(path) {
    return this._get('/social/highlights', { path }, [])
  },

  // ─────────────────────────────────────────
  // 🏢 DIRECTORY
  // ─────────────────────────────────────────
  async getDirectory(path) {
    return this._get('/social/directory', { path }, {
      businesses: [],
      categories: []
    })
  },

  async searchDirectory({ query, path, fallback = true }) {
    const url = fallback
      ? '/social/directory/search/fallback'
      : '/social/directory/search'

    return this._get(url, { query, path }, [])
  },

  // ─────────────────────────────────────────
  // 💾 INTERACTIONS
  // ─────────────────────────────────────────
  async postInteraction({ type, payload }) {
    return this._post('/social/interaction', {
      type,
      ...payload
    }, null)
  },

  async toggleSave({ entityId, entityType }) {
    return this._post('/social/save', {
      entity_id: entityId,
      entity_type: entityType
    }, null)
  },

  // ─────────────────────────────────────────
  // 🧠 CORE ENGINE (SEPARADO LIMPIO)
  // ─────────────────────────────────────────
  async _get(url, params = {}, fallback) {
    try {
      const res = await api.get(url, { params })
      return this._normalize(res)
    } catch (err) {
      this._log('GET', url, err)
      return fallback
    }
  },

  async _post(url, data = {}, fallback) {
    try {
      const res = await api.post(url, data)
      return this._normalize(res)
    } catch (err) {
      this._log('POST', url, err)
      return fallback
    }
  },

  _normalize(res) {
    return res?.data?.data ?? res?.data ?? res
  },

  _log(method, url, err) {
    console.warn(
      `%c[SocialService ${method}] ${url}`,
      'color:#f59e0b; font-weight:bold;',
      err?.response?.data?.message || err?.message || err
    )
  }
}

export default SocialService