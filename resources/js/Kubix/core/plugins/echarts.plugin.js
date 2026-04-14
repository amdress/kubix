import * as echarts from 'echarts'
import { isMobile } from '@/Kubix/core/utils/device'

/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — ECharts Service (UI LAYER PURE)
 * ════════════════════════════════════════════════════════════════
 * - Sin Vue coupling
 * - Sin install(app)
 * - Solo servicio reutilizable
 * - Orquestado desde plugins/index.js
 * ════════════════════════════════════════════════════════════════
 */

class EChartsService {
  constructor() {
    this.echarts = echarts
    this._ready = false
    this.instances = new Set()
  }

  // ─────────────────────────────────────────
  // LIFECYCLE
  // ─────────────────────────────────────────
  init() {
    if (this._ready) return this
    this.registerTheme()
    this._ready = true
    return this
  }

  isReady() {
    return this._ready
  }

  // ─────────────────────────────────────────
  // DEVICE
  // ─────────────────────────────────────────
  isMobile() {
    return isMobile()
  }

  // ─────────────────────────────────────────
  // DESIGN TOKENS
  // ─────────────────────────────────────────
  get colors() {
    return {
      primary: '#3b82f6',
      secondary: '#8b5cf6',
      success: '#10b981',
      warning: '#f59e0b',
      danger: '#ef4444',
      info: '#06b6d4',
      neutral: '#6b7280',
    }
  }

  // ─────────────────────────────────────────
  // THEME
  // ─────────────────────────────────────────
  getTheme() {
    const mobile = this.isMobile()

    return {
      color: Object.values(this.colors),

      textStyle: {
        color: '#1f2937',
        fontFamily: 'system-ui, -apple-system, sans-serif',
      },

      title: {
        textStyle: {
          fontSize: mobile ? 14 : 16,
          fontWeight: 'bold',
        },
      },

      legend: {
        textStyle: {
          fontSize: mobile ? 11 : 12,
        },
      },

      categoryAxis: {
        axisLabel: {
          fontSize: mobile ? 11 : 12,
        },
      },

      valueAxis: {
        axisLabel: {
          fontSize: mobile ? 11 : 12,
        },
      },
    }
  }

  // ─────────────────────────────────────────
  // BASE OPTIONS
  // ─────────────────────────────────────────
  getDefaultOptions() {
    const mobile = this.isMobile()

    return {
      animation: !mobile,
      grid: {
        left: mobile ? '5%' : '12%',
        right: mobile ? '5%' : '5%',
        top: '15%',
        bottom: mobile ? '15%' : '10%',
        containLabel: true,
      },
      tooltip: {
        trigger: 'axis',
      },
      legend: {
        top: mobile ? 'bottom' : 'top',
      },
    }
  }

  // ─────────────────────────────────────────
  // THEME REGISTER
  // ─────────────────────────────────────────
  registerTheme() {
    this.echarts.registerTheme('kubix', this.getTheme())
  }

  // ─────────────────────────────────────────
  // INSTANCE FACTORY
  // ─────────────────────────────────────────
  createInstance(el, options = {}) {
    if (!el) return null

    const chart = this.echarts.init(el, 'kubix')

    chart.setOption({
      ...this.getDefaultOptions(),
      ...options,
    })

    const ro = new ResizeObserver(() => {
      const rect = el.getBoundingClientRect()

      const visible =
        rect.width > 0 &&
        rect.height > 0 &&
        document.contains(el)

      if (visible) chart.resize()
    })

    ro.observe(el)

    this.instances.add({ chart, ro, el })

    return chart
  }

  // ─────────────────────────────────────────
  // CLEANUP
  // ─────────────────────────────────────────
  destroyAll() {
    this.instances.forEach(({ chart, ro }) => {
      ro.disconnect()
      chart.dispose()
    })
    this.instances.clear()
  }
}

/**
 * FACTORY (USADO POR plugins/index.js)
 */
export function createEChartsService() {
  return new EChartsService().init()
}