/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — Loading Service (UI STATE ATOMIC)
 * ════════════════════════════════════════════════════════════════
 * - Control global de loading
 * - Soporte concurrent requests (counter)
 * - UI layer puro
 *
 * ❌ Sin Vue plugin install
 * ❌ Sin API awareness
 * ❌ Sin Axios coupling
 * ════════════════════════════════════════════════════════════════
 */

import { reactive, readonly } from 'vue'

class LoadingService {
  constructor() {
    this.state = reactive({
      count: 0,
      isLoading: false,
    })

    this._ready = false
  }

  // ─────────────────────────────────────────
  // LIFECYCLE
  // ─────────────────────────────────────────
  init() {
    this._ready = true
    return this
  }

  isReady() {
    return this._ready
  }

  // ─────────────────────────────────────────
  // CORE API
  // ─────────────────────────────────────────
  show() {
    this.state.count++
    this.state.isLoading = true
  }

  hide() {
    this.state.count = Math.max(0, this.state.count - 1)

    if (this.state.count === 0) {
      this.state.isLoading = false
    }
  }

  reset() {
    this.state.count = 0
    this.state.isLoading = false
  }

  // ─────────────────────────────────────────
  // GETTERS UI SAFE
  // ─────────────────────────────────────────
  get active() {
    return this.state.isLoading
  }

  get snapshot() {
    return readonly(this.state)
  }
}

/**
 * FACTORY (USADO POR plugins/index.js)
 */
export function createLoadingService() {
  return new LoadingService().init()
}