/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Social Store (TERRITORIAL REACTIVE ENGINE vFINAL)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Consumir contenido territorial basado en ContextStore (current_path).
 * - Gestionar feeds: mural, services, events.
 * - Gestionar directorio, highlights e interacción del usuario.
 *
 * ARQUITECTURA:
 * - ContextStore = ORQUESTADOR (define territorio)
 * - SocialStore = MOTOR (consume y organiza data)
 * - SocialService = ÚNICO GATEWAY API
 *
 * UBICACIÓN: /Kubix/core/stores/socialStore.js
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import { watch } from 'vue'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import SocialService from '@/Kubix/core/services/social/SocialService'

export const useSocialStore = defineStore('social', {

  state: () => ({
    // 📜 FEEDS (STREAM TERRITORIAL)
    feeds: {
      mural:    { items: [], cursor: null, loading: false, has_more: true, error: null },
      services: { items: [], cursor: null, loading: false, has_more: true, error: null },
      events:   { items: [], cursor: null, loading: false, has_more: true, error: null }
    },

    // 🏢 DIRECTORY (UNIFICADO)
    directory: {
      businesses: [],
      categories: [],
      results: [],
      loading: false,
      error: null
    },

    // ⭐ HIGHLIGHTS
    highlights: {
      items: [],
      last_updated_at: null,
      loading: false
    },

    // 👤 INTERACTION
    interaction: {
      saved: [],
      notifications: {
        items: [],
        unread_count: 0,
        last_checked_at: null
      }
    },

    // ⚙️ CONTROL
    control: {
      last_path: null,
      active_tab: 'mural',
      is_initialized: false,
      is_ready: false
    },

    // 🧠 TERRITORIAL CACHE
    cache: {
      by_path: {}
    },

    _hydrating: false
  }),

  getters: {
    currentPath: () => useContextStore().current_path,
    isSynced: (state) => state.control.last_path === useContextStore().current_path,
    activeFeed: (state) => state.feeds[state.control.active_tab],
    getBusinessesByCategory: (state) => (id) => 
      !id ? state.directory.businesses : state.directory.businesses.filter(b => b.category_id === id),
    urgentServices: (state) => state.feeds.services.items.filter(s => s.priority === 'high')
  },

  actions: {
    /**
     * 🚀 INIT (BINDING AUTOMÁTICO)
     */
    init() {
      const context = useContextStore()

      watch(
        () => context.current_path,
        (newPath) => this.hydrate(newPath),
        { immediate: true }
      )

      this.control.is_ready = true
      this._log('SocialStore online → Centralized API Gateway ready', '#6366f1')
      return true
    },

    /**
     * 🔁 HYDRATE (CORE REACTIVO)
     */
    async hydrate(path) {
      if (!path || path === this.control.last_path || this._hydrating) return

      this._hydrating = true
      this._log(`Hydrating territory → ${path}`, '#6366f1')

      // CACHE HIT
      if (this.cache.by_path[path]) {
        this._applyCache(path)
        this._hydrating = false
        return
      }

      this.resetState()
      this.control.last_path = path

      // Carga paralela unificada en SocialService
      await Promise.allSettled([
        this.fetchHighlights(path),
        this.fetchFeed('mural', path),
        this.fetchDirectory(path)
      ])

      this.cache.by_path[path] = this._snapshot()
      this.control.is_initialized = true
      this._hydrating = false
    },

    _applyCache(path) {
      const cached = this.cache.by_path[path]
      if (!cached) return

      this.feeds = cached.feeds
      this.directory = cached.directory
      this.highlights = cached.highlights
      this.control.last_path = path

      this._log(`Cache restored → ${path}`, '#22c55e')
    },

    _snapshot() {
      return JSON.parse(JSON.stringify({
        feeds: this.feeds,
        directory: this.directory,
        highlights: this.highlights
      }))
    },

    resetState() {
      Object.keys(this.feeds).forEach(k => {
        this.feeds[k] = { items: [], cursor: null, loading: false, has_more: true, error: null }
      })
      this.directory = { businesses: [], categories: [], results: [], loading: false, error: null }
      this.highlights = { items: [], last_updated_at: null, loading: false }
      this.control.is_initialized = false
    },

    /**
     * 📜 FEEDS (VIA SOCIAL SERVICE)
     */
    async fetchFeed(type, path) {
      const feed = this.feeds[type]
      if (!feed || feed.loading || !feed.has_more) return

      feed.loading = true
      try {
        const res = await SocialService.getFeed(type, path, feed.cursor)
        if (path !== this.control.last_path) return
        
        feed.items.push(...res.data)
        feed.cursor = res.next_cursor
        feed.has_more = !!res.next_cursor
      } catch (e) {
        feed.error = "Feed error"
        this._log(`Feed ${type} failed`, '#ef4444')
      } finally {
        feed.loading = false
      }
    },

    /**
     * 🏢 DIRECTORY (VIA SOCIAL SERVICE)
     */
    async fetchDirectory(path) {
      this.directory.loading = true
      try {
        const res = await SocialService.getDirectory(path)
        if (path !== this.control.last_path) return
        
        this.directory.businesses = res.businesses || []
        this.directory.categories = res.categories || []
      } catch (e) {
        this.directory.error = "Directory error"
      } finally {
        this.directory.loading = false
      }
    },

    /**
     * ⭐ HIGHLIGHTS (VIA SOCIAL SERVICE)
     */
    async fetchHighlights(path) {
      this.highlights.loading = true
      try {
        const res = await SocialService.getHighlights(path)
        if (path !== this.control.last_path) return
        
        this.highlights.items = res || []
        this.highlights.last_updated_at = new Date()
      } catch (e) {
        this._log('Highlights error', '#ef4444')
      } finally {
        this.highlights.loading = false
      }
    },

    /**
     * 💾 INTERACTION (VIA SOCIAL SERVICE)
     */
    async toggleSave(id, type) {
      const i = this.interaction.saved.indexOf(id)
      i > -1 ? this.interaction.saved.splice(i, 1) : this.interaction.saved.push(id)

      try {
        await SocialService.postInteraction('save', { id, type })
      } catch (e) {
        this._log('Save sync failed', '#ef4444')
      }
    },

    setActiveTab(tab) {
      this.control.active_tab = tab
      if (this.feeds[tab]?.items.length === 0) {
        this.fetchFeed(tab, this.currentPath)
      }
    },

    _log(msg, color = '#94a3b8') {
      console.log(`%c[SocialStore] ${msg}`, `color:${color}; font-weight:bold`)
    }
  }
})