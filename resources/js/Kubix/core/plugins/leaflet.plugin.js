/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — Leaflet Service (UI LAYER PURE)
 * ════════════════════════════════════════════════════════════════
 * - Render mapas
 * - Render GeoJSON
 * - Render puntos
 * - FitBounds
 * - Cleanup
 *
 * ❌ Sin Vue plugin
 * ❌ Sin app.install
 * ❌ Sin stores
 * ❌ Sin lógica de negocio
 * ════════════════════════════════════════════════════════════════
 */

import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { isMobile } from '@/Kubix/core/utils/device'

class LeafletService {
  constructor() {
    this.L = L
    this.maps = new Set()
    this._ready = false

    this.fixAssets()
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
  // DEVICE
  // ─────────────────────────────────────────
  isMobile() {
    return isMobile()
  }

  // ─────────────────────────────────────────
  // LEAFLET FIX (VITE ASSETS)
  // ─────────────────────────────────────────
  fixAssets() {
    delete L.Icon.Default.prototype._getIconUrl

    L.Icon.Default.mergeOptions({
      iconRetinaUrl: new URL(
        'leaflet/dist/images/marker-icon-2x.png',
        import.meta.url
      ).href,
      iconUrl: new URL(
        'leaflet/dist/images/marker-icon.png',
        import.meta.url
      ).href,
      shadowUrl: new URL(
        'leaflet/dist/images/marker-shadow.png',
        import.meta.url
      ).href,
    })
  }

  // ─────────────────────────────────────────
  // MAP CORE
  // ─────────────────────────────────────────
  createMap(el, options = {}) {
    if (!el) return null

    const mobile = this.isMobile()

    const map = L.map(el, {
      zoom: mobile ? 12 : 13,
      minZoom: 3,
      maxZoom: 18,
      dragging: !mobile,
      touchZoom: mobile,
      scrollWheelZoom: !mobile,
      zoomControl: !mobile,
      attributionControl: true,
      ...options,
    })

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap',
      maxZoom: 19,
    }).addTo(map)

    this.maps.add(map)

    return map
  }

  // ─────────────────────────────────────────
  // GEOJSON (PURE RENDER)
  // ─────────────────────────────────────────
  addGeoJSON(map, geojson, options = {}) {
    if (!map || !geojson) return null

    return L.geoJSON(geojson, {
      style: (feature) => this.getStyle(feature),

      onEachFeature: (feature, layer) => {
        if (options.onClick) {
          layer.on('click', () => options.onClick(feature, layer))
        }

        if (options.popup) {
          layer.bindPopup(
            typeof options.popup === 'function'
              ? options.popup(feature)
              : options.popup
          )
        }
      },
    }).addTo(map)
  }

  // ─────────────────────────────────────────
  // POINTS (PURE RENDER)
  // ─────────────────────────────────────────
  addPoints(map, points = [], options = {}) {
    if (!map || !Array.isArray(points)) return []

    return points.map((p) => {
      const marker = L.circleMarker([p.lat, p.lng], {
        radius: options.radius ?? 8,
        fillColor: p.color ?? '#ef4444',
        color: '#ffffff',
        weight: 2,
        fillOpacity: 0.85,
      }).addTo(map)

      if (options.popup) {
        marker.bindPopup(options.popup(p))
      }

      return marker
    })
  }

  // ─────────────────────────────────────────
  // STYLE SYSTEM
  // ─────────────────────────────────────────
  getStyle(feature) {
    const selected = feature?.properties?.selected

    return {
      fillColor: selected ? '#3b82f6' : '#e5e7eb',
      fillOpacity: selected ? 0.3 : 0.2,
      color: selected ? '#1e40af' : '#9ca3af',
      weight: selected ? 2 : 1,
      dashArray: selected ? '0' : '3',
      opacity: 1,
    }
  }

  // ─────────────────────────────────────────
  // VIEW HELPERS
  // ─────────────────────────────────────────
  fitBounds(map, layer) {
    if (!map || !layer) return

    const bounds = layer.getBounds?.()
    if (!bounds || !bounds.isValid()) return

    const mobile = this.isMobile()

    map.fitBounds(bounds, {
      padding: mobile ? [50, 50, 150, 50] : [50, 50, 50, 50],
      maxZoom: 16,
      animate: true,
    })
  }

  // ─────────────────────────────────────────
  // CLEANUP
  // ─────────────────────────────────────────
  clearMap(map) {
    if (!map) return

    map.eachLayer((layer) => {
      if (
        layer instanceof L.GeoJSON ||
        layer instanceof L.Marker ||
        layer instanceof L.CircleMarker
      ) {
        map.removeLayer(layer)
      }
    })
  }

  destroyMap(map) {
    if (!map) return

    map.remove()
    this.maps.delete(map)
  }

  destroyAll() {
    this.maps.forEach((map) => map.remove())
    this.maps.clear()
  }
}

/**
 * FACTORY (USADO POR plugins/index.js)
 */
export function createLeafletService() {
  return new LeafletService().init()
}