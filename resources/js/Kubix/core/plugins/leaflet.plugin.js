import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import { isMobile } from '@/Kubix/core/utils/device'

class LeafletService {
  constructor() {
    this.L = L
    this.maps = new Set()
    this._ready = false
    // Colores estándar de la identidad KUBIX
    this.colors = {
      primary: '#22d3ee',    // Cyan 400 (Puntos activos / Selección)
      border: '#1e293b',     // Slate 800 (Bordes por defecto)
      fill: '#0f172a',       // Slate 950 (Fondo polígonos)
      highlight: '#0891b2',  // Cyan 600 (Hover/Enfoque)
      danger: '#f43f5e',     // Rose 500 (Alertas en mapa)
      text: '#94a3b8'        // Slate 400 (Etiquetas)
    }

    this.fixAssets()
  }

  init() {
    this._ready = true
    return this
  }

  isReady() { return this._ready }
  isMobile() { return isMobile() }

  fixAssets() {
    delete L.Icon.Default.prototype._getIconUrl
    L.Icon.Default.mergeOptions({
      iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
      iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
      shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
    })
  }

  // ─────────────────────────────────────────
  // MAP CORE (ESTILO DARK)
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
      zoomControl: false, // Lo manejamos nosotros o lo movemos a la derecha
      attributionControl: false,
      ...options,
    })

    // CAMBIO CLAVE: Usamos CartoDB Dark Matter para coherencia con el Slate-950
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap &copy; CARTO',
      subdomains: 'abcd',
      maxZoom: 20
    }).addTo(map)

    this.maps.add(map)
    return map
  }

  // ─────────────────────────────────────────
  // GEOJSON (DELIMITADORES DE TERRITORIO)
  // ─────────────────────────────────────────
  addGeoJSON(map, geojson, options = {}) {
    if (!map || !geojson) return null

    return L.geoJSON(geojson, {
      style: (feature) => this.getStyle(feature),
      onEachFeature: (feature, layer) => {
        // Efecto Hover táctico
        layer.on({
          mouseover: (e) => {
            const l = e.target;
            l.setStyle({
              fillOpacity: 0.4,
              weight: 3,
              color: this.colors.primary
            });
          },
          mouseout: (e) => {
            this.addGeoJSON(map, geojson).resetStyle(e.target);
            // Si no quieres re-renderizar todo, usa un helper de reset
          }
        });

        if (options.onClick) layer.on('click', () => options.onClick(feature, layer))
        if (options.popup) layer.bindPopup(
          typeof options.popup === 'function' ? options.popup(feature) : options.popup,
          { className: 'kubix-popup' } // Para dar estilo al popup en CSS
        )
      },
    }).addTo(map)
  }

  // ─────────────────────────────────────────
  // POINTS (OPERADORES / EVENTOS)
  // ─────────────────────────────────────────
  addPoints(map, points = [], options = {}) {
    if (!map || !Array.isArray(points)) return []

    return points.map((p) => {
      const marker = L.circleMarker([p.lat, p.lng], {
        radius: options.radius ?? 6,
        fillColor: p.color ?? this.colors.primary, // Cyan por defecto
        color: '#ffffff',
        weight: 1,
        fillOpacity: 1,
      }).addTo(map)

      // Añadimos un pequeño "glow" al punto si es importante
      if (p.isLive) {
        marker.options.className = 'animate-pulse-glow'; 
      }

      if (options.popup) marker.bindPopup(options.popup(p))
      return marker
    })
  }

  // ─────────────────────────────────────────
  // STYLE SYSTEM (EL ADN KUBIX)
  // ─────────────────────────────────────────
  getStyle(feature) {
    const isSelected = feature?.properties?.selected
    const level = feature?.properties?.level // 'COUNTRY' | 'STATE' | 'CITY' | 'NEIGHBORHOOD'

    return {
      fillColor: isSelected ? this.colors.primary : this.colors.fill,
      fillOpacity: isSelected ? 0.3 : 0.1,
      color: isSelected ? this.colors.primary : this.colors.text,
      weight: isSelected ? 2 : 1,
      dashArray: isSelected ? '0' : '5, 5', // Línea discontinua para lo no seleccionado (estilo técnico)
      opacity: 0.8,
    }
  }

  // ─────────────────────────────────────────
  // VIEW HELPERS & CLEANUP
  // ─────────────────────────────────────────
  fitBounds(map, layer) {
    if (!map || !layer) return
    const bounds = layer.getBounds?.()
    if (!bounds || !bounds.isValid()) return

    map.fitBounds(bounds, {
      padding: this.isMobile() ? [20, 20, 100, 20] : [40, 40, 40, 40],
      maxZoom: 16,
      animate: true,
    })
  }

  clearMap(map) {
    if (!map) return
    map.eachLayer((layer) => {
      if (layer instanceof L.GeoJSON || layer instanceof L.Marker || layer instanceof L.CircleMarker) {
        map.removeLayer(layer)
      }
    })
  }

  destroyMap(map) {
    if (!map) return
    map.remove()
    this.maps.delete(map)
  }
}

export function createLeafletService() {
  return new LeafletService().init()
}