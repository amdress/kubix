<template>
  <div class="relative w-full h-full min-h-[400px] bg-slate-950 rounded-2xl overflow-hidden border border-slate-900 shadow-2xl">
    <!-- El contenedor del mapa -->
    <div ref="mapEl" class="w-full h-full z-10"></div>

    <!-- Overlay de estado -->
    <div v-if="!items.length" class="absolute inset-0 flex items-center justify-center bg-slate-950/50 backdrop-blur-sm z-20">
      <div class="flex flex-col items-center gap-2">
        <div class="w-8 h-8 border-2 border-cyan-500/20 border-t-cyan-500 rounded-full animate-spin"></div>
        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-cyan-500/50">Awaiting_Coordinates</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
// ✅ Importamos el helper del core, NO el servicio directamente
import { useKubix } from "@/Kubix/core/plugins"; 

const props = defineProps({
  items: { type: Array, default: () => [] },
  config: {
    type: Object,
    default: () => ({
      center: [-25.429, -49.267],
      zoom: 12,
      maxZoom: 18,
      interactive: true,
    })
  }
});

const emit = defineEmits(["select", "ready"]);

// 🛠️ Extraemos el servicio desde el orquestador
const { leaflet: leafletService } = useKubix();

const mapEl = ref(null);
let map = null;
let geojsonLayer = null;
let markersLayer = null;

onMounted(() => {
  initMap();
  render();
});

watch(() => props.items, () => render(), { deep: true });

onBeforeUnmount(() => {
  // Destrucción segura usando el servicio inyectado
  if (map) leafletService.destroyMap(map);
});

function initMap() {
  if (!mapEl.value || !leafletService) return;

  map = leafletService.createMap(mapEl.value, {
    center: props.config.center,
    zoom: props.config.zoom,
    dragging: props.config.interactive,
    scrollWheelZoom: props.config.interactive,
    zoomControl: false,
  });

  // Usamos la instancia de Leaflet (L) expuesta por el servicio
  geojsonLayer = leafletService.L.featureGroup().addTo(map);
  markersLayer = leafletService.L.featureGroup().addTo(map);

  emit("ready", { map, service: leafletService });
}

function render() {
  if (!map || !leafletService) return;

  geojsonLayer.clearLayers();
  markersLayer.clearLayers();

  props.items.forEach(item => {
    // POLÍGONOS
    if (item.type === 'geojson') {
      const layer = leafletService.addGeoJSON(geojsonLayer, item.data, {
        onClick: () => emit('select', item)
      });
      
      if (item.color) {
        layer.setStyle({ 
          color: item.color, 
          fillColor: item.color,
          fillOpacity: 0.2 
        });
      }
    } 

    // PUNTOS
    if (item.type === 'point') {
      const marker = leafletService.L.circleMarker([item.lat, item.lng], {
        radius: item.isMain ? 8 : 5,
        fillColor: item.color || '#22d3ee',
        color: '#ffffff',
        weight: 1.5,
        fillOpacity: 1,
        className: item.isLive ? 'map-pulse-marker' : ''
      }).addTo(markersLayer);

      marker.on('click', () => emit('select', item));

      if (item.label) {
        marker.bindTooltip(item.label, { 
          direction: 'top', 
          offset: [0, -5],
          className: 'kubix-tooltip-dark'
        });
      }
    }
  });

  autoFit();
}

function autoFit() {
  const hasGeo = geojsonLayer?.getLayers().length > 0;
  const hasPoints = markersLayer?.getLayers().length > 0;

  if (!hasGeo && !hasPoints) return;

  const bounds = hasGeo ? geojsonLayer.getBounds() : markersLayer.getBounds();

  if (bounds.isValid()) {
    map.fitBounds(bounds, { 
      padding: [50, 50], 
      maxZoom: 16,
      animate: true 
    });
  }
}
</script>

<style>
/* Estilos técnicos Kubyx */
.kubix-tooltip-dark {
  background: #0f172a !important;
  border: 1px solid #1e293b !important;
  color: #f1f5f9 !important;
  font-family: 'Monaco', 'Consolas', monospace;
  font-size: 10px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  padding: 4px 8px;
  border-radius: 4px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.5);
}

.kubix-tooltip-dark:before {
  border-top-color: #1e293b !important;
}

.map-pulse-marker {
  filter: drop-shadow(0 0 5px #22d3ee);
  animation: marker-glow 2s infinite ease-in-out;
}

@keyframes marker-glow {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.2); }
}
</style>