<template>
  <Transition name="fade" @after-leave="cleanupThreeJS">
    <div
      v-if="visible"
      class="fixed inset-0 bg-[#020617]/90 backdrop-blur-xl flex flex-col items-center justify-center z-[9999]"
    >
      <div class="relative flex items-center justify-center">
        
        <div class="cube-micro-mount" ref="cubeMount"></div>

        <div
          class="absolute h-32 w-32 rounded-full blur-[60px] opacity-20 transition-colors duration-700 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
          :style="{ backgroundColor: color }"
        ></div>
      </div>

      <div class="mt-12 flex flex-col items-center gap-2 text-center px-8">
        <span class="text-slate-500 text-[10px] font-black tracking-[0.5em] uppercase animate-pulse">
          {{ text }}
        </span>
        <span
          v-if="subtext"
          class="text-[11px] font-bold tracking-[0.2em] uppercase transition-all duration-500"
          :style="{ color: color }"
        >
          {{ subtext }}
        </span>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import * as THREE from 'three'

const props = defineProps({
  visible: { type: Boolean, default: false },
  text:    { type: String,  default: 'Sincronizando' },
  subtext: { type: String,  default: '' },
  color:   { type: String,  default: '#3b82f6' }
})

const cubeMount = ref(null)

// --- LÓGICA MOTOR 3D KUBIX ---
let scene, camera, renderer, pointLight
let cubies = []
let isAnimating = false
let autoMoveInterval = null
let animationFrameId = null

// Reacción al cambio de color dinámico
watch(() => props.color, (newColor) => {
  if (pointLight) pointLight.color.set(newColor)
  updateCubiesColor(newColor)
})

watch(() => props.visible, (isVisible) => {
  if (isVisible) nextTick(() => initThreeJS())
  else cleanupThreeJS()
})

onMounted(() => { if (props.visible) nextTick(() => initThreeJS()) })
onBeforeUnmount(() => cleanupThreeJS())

function initThreeJS() {
  if (!cubeMount.value || scene) return

  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(45, 1, 0.1, 100)
  camera.position.set(4, 4, 6)
  camera.lookAt(0, 0, 0)

  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
  renderer.setSize(120, 120) // Un poco más grande para apreciar el detalle
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  cubeMount.value.appendChild(renderer.domElement)

  const ambientLight = new THREE.AmbientLight(0xffffff, 0.3)
  scene.add(ambientLight)
  
  pointLight = new THREE.PointLight(props.color, 5, 15)
  pointLight.position.set(5, 5, 5)
  scene.add(pointLight)

  buildCube()
  animate()
  startAutoMovement()
}

function buildCube() {
  const geometry = new THREE.BoxGeometry(0.88, 0.88, 0.88)
  const insideColor = 0x020617

  for (let x = -1; x <= 1; x++) {
    for (let y = -1; y <= 1; y++) {
      for (let z = -1; z <= 1; z++) {
        const materials = [
          createMaterial(x === 1 ? props.color : insideColor),
          createMaterial(x === -1 ? props.color : insideColor),
          createMaterial(y === 1 ? props.color : insideColor),
          createMaterial(y === -1 ? props.color : insideColor),
          createMaterial(z === 1 ? props.color : insideColor),
          createMaterial(z === -1 ? props.color : insideColor)
        ]

        const cubie = new THREE.Mesh(geometry, materials)
        cubie.position.set(x, y, z)
        scene.add(cubie)
        cubies.push(cubie)
      }
    }
  }
}

function createMaterial(color) {
  return new THREE.MeshPhysicalMaterial({
    color: color,
    metalness: 0.7,
    roughness: 0.2,
    transparent: true,
    opacity: 0.9,
    emissive: color,
    emissiveIntensity: color !== 0x020617 ? 0.2 : 0
  })
}

function updateCubiesColor(newColor) {
  const insideColor = 0x020617
  cubies.forEach(cubie => {
    cubie.material.forEach((mat, idx) => {
      // Si el material no es del color interior, es una cara exterior
      if (mat.color.getHex() !== insideColor) {
        mat.color.set(newColor)
        mat.emissive.set(newColor)
      }
    })
  })
}

function rotateFace(face) {
  if (isAnimating || !scene) return
  isAnimating = true

  const config = {
    'U': { axis: 'y', layer: 1, dir: -1 }, 'D': { axis: 'y', layer: -1, dir: 1 },
    'R': { axis: 'x', layer: 1, dir: -1 }, 'L': { axis: 'x', layer: -1, dir: 1 },
    'F': { axis: 'z', layer: 1, dir: -1 }, 'B': { axis: 'z', layer: -1, dir: 1 }
  }

  const { axis, layer, dir } = config[face]
  const layerCubies = cubies.filter(c => Math.round(c.position[axis]) === layer)
  const pivot = new THREE.Group()
  
  scene.add(pivot)
  layerCubies.forEach(c => pivot.attach(c))

  const targetAngle = dir * Math.PI / 2
  let currentAngle = 0
  const speed = 0.3 // Un poco más rápido para el loader

  function step() {
    currentAngle += speed
    pivot.rotation[axis] += (targetAngle * speed)

    if (currentAngle >= 1) {
      pivot.rotation[axis] = targetAngle
      layerCubies.forEach(c => {
        scene.attach(c)
        c.position.x = Math.round(c.position.x);
        c.position.y = Math.round(c.position.y);
        c.position.z = Math.round(c.position.z);
      })
      scene.remove(pivot)
      isAnimating = false
    } else {
      requestAnimationFrame(step)
    }
  }
  step()
}

function startAutoMovement() {
  autoMoveInterval = setInterval(() => {
    const moves = ['U', 'D', 'R', 'L', 'F', 'B']
    if (!isAnimating && props.visible) {
      rotateFace(moves[Math.floor(Math.random() * moves.length)])
    }
  }, 1200)
}

function animate() {
  if (!scene) return
  animationFrameId = requestAnimationFrame(animate)
  scene.rotation.y += 0.005
  renderer.render(scene, camera)
}

function cleanupThreeJS() {
  clearInterval(autoMoveInterval)
  if (animationFrameId) cancelAnimationFrame(animationFrameId)
  if (scene) {
    cubies.forEach(c => {
      c.geometry.dispose()
      c.material.forEach(m => m.dispose())
    })
    renderer?.dispose()
    renderer?.domElement?.remove()
    scene = camera = renderer = null
    cubies = []
  }
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.cube-micro-mount { width: 120px; height: 120px; }
</style>