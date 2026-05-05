<template>
  <div class="w-full h-full flex items-center justify-center">
    
    <!-- wrapper escalable -->
    <div 
      ref="wrapper"
      class="w-full h-full flex items-center justify-center"
      :style="wrapperStyle"
    >
      <div ref="chartEl" class="w-full h-full"></div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, watch, inject, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  data: { type: Array, required: true },
  color: { type: String, default: '#3b82f6' }
})

const chartEl = ref(null)
const wrapper = ref(null)

const kubix = inject('kubix')

let instance = null
let resizeObserver = null

const wrapperStyle = ref({})

const BASE_HEIGHT = 120

// ─────────────────────────────
// SCALE LIKE GAUGE
// ─────────────────────────────
const updateScale = () => {
  const h = wrapper.value?.parentElement?.clientHeight || 0
  const scale = Math.min(h / BASE_HEIGHT, 1)

  wrapperStyle.value = {
    width: '100%',
    height: BASE_HEIGHT + 'px',
    transform: `scale(${scale})`,
    transformOrigin: 'center'
  }
}

// ─────────────────────────────
// CHART INIT
// ─────────────────────────────
const init = async () => {
  await nextTick()
  if (!chartEl.value || !kubix?.echarts) return

  instance = kubix.echarts.createInstance(chartEl.value, {
    grid: { top: 10, bottom: 10, left: 0, right: 0, containLabel: false },
    xAxis: { type: 'category', boundaryGap: false, show: false },
    yAxis: { show: false, min: 'dataMin', max: 'dataMax' },

    series: [{
      data: props.data,
      type: 'line',
      smooth: true,
      symbol: 'none',
      lineStyle: { width: 2, color: props.color },
      areaStyle: {
        color: {
          type: 'linear',
          x: 0, y: 0, x2: 0, y2: 1,
          colorStops: [
            { offset: 0, color: props.color + '55' },
            { offset: 1, color: props.color + '00' }
          ]
        }
      }
    }]
  })
}

// ─────────────────────────────
// RESIZE SAFE
// ─────────────────────────────
const resize = () => {
  updateScale()
  instance?.resize()
}

onMounted(() => {
  init()
  updateScale()

  resizeObserver = new ResizeObserver(resize)

  if (wrapper.value?.parentElement) {
    resizeObserver.observe(wrapper.value.parentElement)
  }

  window.addEventListener('resize', resize)
})

onUnmounted(() => {
  resizeObserver?.disconnect()
  window.removeEventListener('resize', resize)
  instance?.dispose()
})

watch(() => props.data, (val) => {
  instance?.setOption({
    series: [{ data: val }]
  })
}, { deep: true })
</script>