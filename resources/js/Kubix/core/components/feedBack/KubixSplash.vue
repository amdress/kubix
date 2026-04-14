<template>
  <div class="splash">
    <div class="cube-3d-bg" ref="cubeMount"></div>
    
    <div class="overlay-vignette"></div>

    <div class="cubes-bg">
      <div v-for="i in 12" :key="i" class="cube-particle" :style="cubeStyle(i)" />
    </div>

    <div class="splash-center" :class="{ visible: showContent }">
      <div class="logo-ring">
        <div class="ring ring-1" :style="{ borderTopColor: territories.primary_color }" />
        <div class="ring ring-2" />
        <img
          src="@/assets/images/shared/logo_transparent.png"
          alt="Kubix"
          class="logo-img"
        />
      </div>

      <div class="brand">
        <h1 class="brand-name">KUBIX</h1>
        <span class="brand-by ml-10">by <strong>Drunk-Code</strong></span>
      </div>

      <div class="dots">
        <span :style="{ backgroundColor: territories.primary_color }" />
        <span :style="{ backgroundColor: territories.primary_color }" />
        <span :style="{ backgroundColor: territories.primary_color }" />
      </div>

      <p class="status-text">{{ statusText }}</p>
    </div>

    <div class="splash-footer" :class="{ visible: showContent }">
      <span>© {{ year }} Drunk-Code · Kubix SyS</span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import * as THREE from 'three'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import { useTerritoryStore } from '@/Kubix/core/stores/territoryStore'

// Stores
const context = useContextStore()
const territories = useTerritoryStore()

// Refs y Estado
const cubeMount = ref(null)
const showContent = ref(false)
const year = new Date().getFullYear()

// Computed
const statusText = computed(() => context.bootMessage || 'System Kubix started')
const statusColor = computed(() => territories.primary_color || '#3b82f6')

const cubeStyle = (i) => ({
  '--x': `${(i * 8.3) % 100}%`,
  '--delay': `${(i * 0.4) % 3}s`,
  '--size': `${8 + (i % 4) * 6}px`,
  '--dur': `${4 + (i % 3) * 2}s`,
  '--particle-color': i % 2 === 0 ? statusColor.value : '#1e293b'
})

// --- LÓGICA THREE.JS ---
let scene, camera, renderer, pointLight1, pointLight2
let cubies = []
let isAnimating = false
let autoMoveInterval = null

// Reacción al cambio de territorio
watch(() => context.showSplash, (isShowing) => {
  if (!isShowing) {
    showContent.value = false // Inicia desvanecimiento de logo antes de desmontar
  }
})

onMounted(() => {
  setTimeout(() => (showContent.value = true), 150)
  initThreeJS()
  buildCube()
  animate()
  startAutoMovement()
  window.addEventListener('resize', onWindowResize)
})

onBeforeUnmount(() => {
  clearInterval(autoMoveInterval)
  window.removeEventListener('resize', onWindowResize)
  
  // Limpieza de GPU
  cubies.forEach(c => {
    c.geometry.dispose()
    if (Array.isArray(c.material)) c.material.forEach(m => m.dispose())
    else c.material.dispose()
  })
  renderer.dispose()
})

function initThreeJS() {
  scene = new THREE.Scene()
  
  camera = new THREE.PerspectiveCamera(45, cubeMount.value.clientWidth / cubeMount.value.clientHeight, 0.1, 100)
  camera.position.set(5, 5, 7)
  camera.lookAt(0, 0, 0)

  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true })
  renderer.setSize(cubeMount.value.clientWidth, cubeMount.value.clientHeight)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  cubeMount.value.appendChild(renderer.domElement)

  // Iluminación
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.2)
  scene.add(ambientLight)
  
  pointLight1 = new THREE.PointLight(statusColor.value, 4, 15)
  pointLight1.position.set(5, 5, 5)
  scene.add(pointLight1)
  
  pointLight2 = new THREE.PointLight(0x1e293b, 2, 15)
  pointLight2.position.set(-5, -5, -5)
  scene.add(pointLight2)
}

function buildCube() {
  const geometry = new THREE.BoxGeometry(0.88, 0.88, 0.88)
  const insideColor = 0x020617

  for (let x = -1; x <= 1; x++) {
    for (let y = -1; y <= 1; y++) {
      for (let z = -1; z <= 1; z++) {
        // Materiales con efecto "Glass/Crystal"
        const materials = [
          createMaterial(x === 1 ? statusColor.value : insideColor),
          createMaterial(x === -1 ? statusColor.value : insideColor),
          createMaterial(y === 1 ? statusColor.value : insideColor),
          createMaterial(y === -1 ? statusColor.value : insideColor),
          createMaterial(z === 1 ? statusColor.value : insideColor),
          createMaterial(z === -1 ? statusColor.value : insideColor)
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
    metalness: 0.6,
    roughness: 0.2,
    transparent: true,
    opacity: 0.85,
    clearcoat: 1.0,
    emissive: color,
    emissiveIntensity: color !== 0x020617 ? 0.15 : 0
  })
}

function startAutoMovement() {
  const moves = ['U', 'D', 'R', 'L', 'F', 'B']
  autoMoveInterval = setInterval(() => {
    if (!isAnimating && showContent.value) {
      rotateFace(moves[Math.floor(Math.random() * moves.length)])
    }
  }, 1800)
}

function rotateFace(face) {
  if (isAnimating) return
  isAnimating = true

  const config = {
    'U': { axis: 'y', layer: 1, dir: -1 },
    'D': { axis: 'y', layer: -1, dir: 1 },
    'R': { axis: 'x', layer: 1, dir: -1 },
    'L': { axis: 'x', layer: -1, dir: 1 },
    'F': { axis: 'z', layer: 1, dir: -1 },
    'B': { axis: 'z', layer: -1, dir: 1 }
  }

  const { axis, layer, dir } = config[face]
  const layerCubies = cubies.filter(c => Math.round(c.position[axis]) === layer)
  const pivot = new THREE.Group()
  
  scene.add(pivot)
  layerCubies.forEach(c => pivot.attach(c))

  const targetAngle = dir * Math.PI / 2
  let currentAngle = 0
  const speed = 0.2

  function step() {
    currentAngle += speed
    pivot.rotation[axis] += (targetAngle * speed)

    if (currentAngle >= 1) {
      pivot.rotation[axis] = targetAngle
      layerCubies.forEach(c => {
        scene.attach(c)
        c.position.x = Math.round(c.position.x)
        c.position.y = Math.round(c.position.y)
        c.position.z = Math.round(c.position.z)
      })
      scene.remove(pivot)
      isAnimating = false
    } else {
      requestAnimationFrame(step)
    }
  }
  step()
}

function animate() {
  requestAnimationFrame(animate)
  scene.rotation.y += 0.003
  scene.rotation.x = Math.sin(Date.now() * 0.0004) * 0.15
  renderer.render(scene, camera)
}

function onWindowResize() {
  if (!cubeMount.value) return
  camera.aspect = cubeMount.value.clientWidth / cubeMount.value.clientHeight
  camera.updateProjectionMatrix()
  renderer.setSize(cubeMount.value.clientWidth, cubeMount.value.clientHeight)
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=Inter:wght@300;400&display=swap');

.splash {
  position: fixed;
  inset: 0;
  background: #020617;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  font-family: 'Inter', sans-serif;
  z-index: 9999;
}

.cube-3d-bg {
  position: absolute;
  inset: 0;
  z-index: 1;
  opacity: 0.7;
}

.overlay-vignette {
  position: absolute;
  inset: 0;
  z-index: 2;
  background: radial-gradient(circle at center, transparent 0%, #020617 85%);
  pointer-events: none;
}

.cubes-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 3;
}

.cube-particle {
  position: absolute;
  left: var(--x);
  bottom: -20px;
  width: var(--size);
  height: var(--size);
  background: var(--particle-color);
  box-shadow: 0 0 15px var(--particle-color);
  border-radius: 2px;
  opacity: 0;
  animation: floatUp var(--dur) var(--delay) infinite ease-in-out;
}

@keyframes floatUp {
  0% { transform: translateY(0) rotate(0deg); opacity: 0; }
  20% { opacity: 0.3; }
  80% { opacity: 0.2; }
  100% { transform: translateY(-110vh) rotate(360deg); opacity: 0; }
}

.splash-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
  opacity: 0;
  transform: scale(0.9);
  transition: all 1s cubic-bezier(0.34, 1.56, 0.64, 1);
  z-index: 10;
}

.splash-center.visible {
  opacity: 1;
  transform: scale(1);
}

.logo-ring {
  position: relative;
  width: 130px;
  height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ring {
  position: absolute;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.05);
  animation: spinRing linear infinite;
}

.ring-1 {
  width: 100%;
  height: 100%;
  animation-duration: 10s;
  border-top-color: v-bind('statusColor');
  filter: drop-shadow(0 0 10px v-bind('statusColor'));
}

.ring-2 {
  width: 75%;
  height: 75%;
  animation-duration: 6s;
  animation-direction: reverse;
  border-bottom-color: rgba(255, 255, 255, 0.2);
}

@keyframes spinRing {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.logo-img {
  width: 70%;
  height: 70%;
  object-fit: contain;
  filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.1));
}

.brand-name {
  font-family: 'Rajdhani', sans-serif;
  font-size: 3rem;
  font-weight: 700;
  letter-spacing: 0.5em;
  margin: 0;
  margin-left: 5px;
  background: linear-gradient(to bottom, #ffffff, #64748b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 0 30px v-bind('statusColor + "44"');
}

.brand-by {
  font-size: 0.7rem;
  color: #475569;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  margin-top: 0.5rem;
}

.brand-by strong { color: v-bind('statusColor'); }

.status-text {
  font-size: 0.85rem; /* Un poco más grande para el móvil */
  color: #ffffff;    /* Blanco puro para que se lea sobre el fondo oscuro */
  text-shadow: 0 0 10px rgba(0, 0, 0, 0.8); /* Sombra para legibilidad */
  z-index: 20;
  min-height: 1.2em; /* Evita que el layout salte si el texto está vacío */
}

.splash-footer {
  position: absolute;
  bottom: 40px;
  font-size: 0.6rem;
  color: #ffffff;
  letter-spacing: 0.1em;
  opacity: 0;
  transition: opacity 1s ease 1s;
  z-index: 10;
}

.splash-footer.visible { opacity: 1; }
</style>