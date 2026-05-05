<template>
  <div class="sparkline-wrapper">
    <v-chart 
      :key="history.length"
      :option="chartOption" 
      autoresize 
      class="echarts-inner" 
    />
  </div>
</template>

<script setup>
import { computed, provide } from 'vue';
import { use } from "echarts/core";
import { CanvasRenderer } from "echarts/renderers";
import { LineChart } from "echarts/charts";
import { GridComponent } from "echarts/components";
import VChart, { THEME_KEY } from "vue-echarts";

// Registrar componentes necesarios
use([CanvasRenderer, LineChart, GridComponent]);

const props = defineProps({
  history: { type: Array, default: () => [] },
  color: { type: String, default: '#a3e635' }
});

const chartOption = computed(() => ({
  grid: { left: 0, right: 0, top: 10, bottom: 0 },
  xAxis: { type: 'category', show: false },
  yAxis: { type: 'value', show: false, min: 'dataMin' },
  series: [{
    data: props.history,
    type: 'line',
    smooth: true,
    symbol: 'none',
    lineStyle: { color: props.color, width: 2.5 },
    areaStyle: {
      color: {
        type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
        colorStops: [
          { offset: 0, color: props.color + '55' }, // Más opacidad (33%)
          { offset: 1, color: props.color + '00' }
        ]
      }
    }
  }],
  animation: true // Aseguramos que se vea la transición al cargar
}));
</script>

<style scoped>
.sparkline-wrapper {
  width: 100%;
  height: 65px; /* Un poco más alto para que se note la curva */
  position: relative;
  display: block;
}
.echarts-inner {
  width: 100% !important;
  height: 100% !important;
}
</style>