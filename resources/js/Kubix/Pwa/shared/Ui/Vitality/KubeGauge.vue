<template>
  <div class="relative w-full h-full flex items-center justify-center">

    <!-- contenedor escalable -->
    <div
      ref="gaugeElWrapper"
      class="flex items-center justify-center"
      :style="wrapperStyle"
    >
      <div ref="gaugeEl" class="w-full h-full"></div>
    </div>

    <!-- VALUE -->
    <div
      v-if="showOverlay"
      class="absolute bottom-4 text-center pointer-events-none"
    >
      <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-1">
        status
      </p>

      <span
        class="text-2xl font-black leading-none"
        :style="{ color: statusColor }"
      >
        {{ value }}%
      </span>
    </div>

    <!-- FOOTER -->
    <div
      v-if="showOverlay"
      class="absolute bottom-0 w-full text-center pointer-events-none"
    >
      <p
        class="text-[10px] font-bold uppercase tracking-[0.25em]"
        :style="{ color: statusColor }"
      >
        {{ statusLabel }}
      </p>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick, computed } from 'vue'
import { useKubix } from '@/Kubix/core/plugins'

const props = defineProps({
  value: { type: Number, default: 0 }
})

const gaugeEl = ref(null)
const gaugeElWrapper = ref(null)
const kubix = useKubix()

let chartInstance = null
let resizeObserver = null

const wrapperStyle = ref({})
const showOverlay = ref(true)

const BASE_HEIGHT = 120

// ─────────────────────────────
// SEMÁFORO
// ─────────────────────────────
const status = computed(() => {
  if (props.value < 40) return 'critical'
  if (props.value < 70) return 'warning'
  return 'healthy'
})

const statusColor = computed(() => {
  switch (status.value) {
    case 'critical': return '#ef4444'
    case 'warning': return '#f59e0b'
    default: return '#22c55e'
  }
})

const statusLabel = computed(() => {
  switch (status.value) {
    case 'critical': return 'Critical Zone'
    case 'warning': return 'Warning Zone'
    default: return 'Healthy Zone'
  }
})

// ─────────────────────────────
// GAUGE OPTIONS (MULTI COLOR)
// ─────────────────────────────
const buildOptions = () => ({
  series: [{
    type: 'gauge',
    startAngle: 210,
    endAngle: -30,
    min: 0,
    max: 100,
    radius: '90%',
    center: ['50%', '55%'],

    // ZONAS (semáforo real)
    axisLine: {
      lineStyle: {
        width: 10,
        color: [
          [0.4, '#ef4444'],   // rojo
          [0.7, '#f59e0b'],   // amarillo
          [1,   '#22c55e']    // verde
        ]
      }
    },

    // PROGRESO dinámico (refuerza estado)
    progress: {
      show: true,
      width: 10,
      itemStyle: {
        color: statusColor.value,
        shadowBlur: 12,
        shadowColor: statusColor.value + '55'
      }
    },

    // AGUJA fija (NO semáforo)
    pointer: {
      show: true,
      length: '65%',
      width: 4,
      itemStyle: {
        color: '#22d3ee'
      }
    },

    axisTick: {
      distance: 12,
      length: 4,
      lineStyle: { color: '#1e293b' }
    },

    splitLine: {
      distance: 12,
      length: 10,
      lineStyle: { color: '#334155', width: 2 }
    },

    axisLabel: {
      distance: 22,
      color: '#475569',
      fontSize: 10,
      fontFamily: 'monospace',
      fontWeight: 'bold'
    },

    detail: { show: false },

    data: [{ value: props.value }]
  }]
})

// ─────────────────────────────
// SCALE
// ─────────────────────────────
const updateScale = () => {
  const h = gaugeElWrapper.value?.parentElement?.clientHeight || 0
  const scale = Math.min(h / BASE_HEIGHT, 1)

  wrapperStyle.value = {
    width: '160px',
    height: BASE_HEIGHT + 'px',
    transform: `scale(${scale})`,
    transformOrigin: 'center'
  }

  showOverlay.value = h > 80
}

// ─────────────────────────────
// INIT
// ─────────────────────────────
const initChart = async () => {
  await nextTick()
  if (!gaugeEl.value) return

  chartInstance = kubix.echarts.createInstance(
    gaugeEl.value,
    buildOptions()
  )
}

// ─────────────────────────────
// RESIZE SAFE
// ─────────────────────────────
const resize = () => {
  updateScale()
  chartInstance?.setOption(buildOptions())
  chartInstance?.resize()
}

onMounted(() => {
  initChart()
  updateScale()

  resizeObserver = new ResizeObserver(resize)

  if (gaugeElWrapper.value?.parentElement) {
    resizeObserver.observe(gaugeElWrapper.value.parentElement)
  }

  window.addEventListener('resize', resize)
})

onBeforeUnmount(() => {
  resizeObserver?.disconnect()
  window.removeEventListener('resize', resize)
  chartInstance?.dispose()
})

watch(() => props.value, (val) => {
  chartInstance?.setOption({
    series: [{ data: [{ value: val }] }]
  })
})
</script>