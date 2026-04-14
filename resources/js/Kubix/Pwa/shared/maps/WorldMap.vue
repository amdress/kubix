<template>
  <div ref="mapEl" class="w-full h-full min-h-[350px] bg-slate-950"></div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const props = defineProps({
  /**
   * DATA: Array de objetos que pueden ser:
   * - { type: 'point', lat, lng, label, color }
   * - { type: 'circle', lat, lng, radius, color, fillOpacity }
   * - { type: 'geojson', data: {GeoJSON}, label, color }
   */
  items: { type: Array, default: () => [] },
  
  config: {
    type: Object,
    default: () => ({
      zoom: 12,
      maxZoom: 18,
      center: [-25.429, -49.267],
      primaryColor: '#3b82f6',
      interactive: true,
      tileLayer: "https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png"
    })
  }
});

const emit = defineEmits(["select", "ready"]);
const mapEl = ref(null);
let map = null;
let mainLayer = null;

onMounted(() => {
  initMap();
  render();
});

watch(() => props.items, () => render(), { deep: true });

onBeforeUnmount(() => map?.remove());

function initMap() {
  if (!mapEl.value) return;
  map = L.map(mapEl.value, { 
    zoomControl: false, 
    attributionControl: false,
    dragging: props.config.interactive,
    scrollWheelZoom: false
  }).setView(props.config.center, props.config.zoom);

  L.tileLayer(props.config.tileLayer).addTo(map);
  mainLayer = L.featureGroup().addTo(map);
  emit("ready", map);
}

function render() {
  if (!map || !mainLayer) return;
  mainLayer.clearLayers();

  props.items.forEach(item => {
    let layer;

    switch (item.type) {
      case 'geojson':
        layer = L.geoJSON(item.data, {
          style: {
            color: item.color || props.config.primaryColor,
            weight: 2,
            fillColor: item.color || props.config.primaryColor,
            fillOpacity: item.fillOpacity || 0.2
          }
        });
        break;

      case 'circle':
        layer = L.circle([item.lat, item.lng], {
          radius: item.radius || 1000,
          color: item.color || props.config.primaryColor,
          fillColor: item.color || props.config.primaryColor,
          fillOpacity: item.fillOpacity || 0.1,
          weight: 1
        });
        break;

      case 'point':
      default:
        const icon = L.divIcon({
          html: `<div style="width:12px;height:12px;background:${item.color || props.config.primaryColor};border-radius:50%;border:2px solid white;box-shadow:0 0 10px ${item.color}aa;"></div>`,
          className: '', iconSize: [12, 12], iconAnchor: [6, 6]
        });
        layer = L.marker([item.lat, item.lng], { icon });
        break;
    }

    if (item.label) {
      layer.bindTooltip(item.label, { sticky: true, className: 'kubix-tooltip' });
    }

    layer.on('click', () => emit('select', item));
    layer.addTo(mainLayer);
  });

  // Ajuste automático de cámara al contenido
  const bounds = mainLayer.getBounds();
  if (bounds.isValid()) {
    map.fitBounds(bounds, { padding: [30, 30], maxZoom: props.config.maxZoom });
  }
}
</script>