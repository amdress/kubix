<template>
  <transition name="fade">
    <div 
      v-if="context.showSplash" 
      class="business-splash"
      :style="{ '--primary': branding.primary }"
    >
      <div class="splash-content">
        <div class="logo-wrapper" :class="{ 'pulse': !branding.logo }">
          <img 
            v-if="branding.logo" 
            :src="branding.logo" 
            :alt="branding.name"
            class="business-logo"
          />
          <div v-else class="fallback-logo">
            {{ branding.name.charAt(0) }}
          </div>
        </div>

        <div class="loader-info">
          <h1 class="business-name">{{ branding.name }}</h1>
          <p class="boot-message">{{ context.bootMessage }}</p>
          
          <div class="progress-bar">
            <div class="progress-line"></div>
          </div>
        </div>
      </div>

      <div 
        v-if="branding.splash" 
        class="splash-bg" 
        :style="{ backgroundImage: `url(${branding.splash})` }"
      ></div>
    </div>
  </transition>
</template>

<script setup>
import { computed } from 'vue'
import { useContextStore } from '@/Kubix/core/stores/context'
import { useWorkspaceStore } from '@/Kubix/core/stores/workspace'

const context = useContextStore()
const workspace = useWorkspaceStore()

/**
 * LA MAGIA: El Splash decide qué mostrar según el modo.
 * No necesita parámetros, él sabe dónde está parado.
 */
const branding = computed(() => {
  if (context.mode === 'business' && workspace.isActive) {
    return workspace.businessBranding
  }
  return context.cityBranding
})
</script>

<style scoped>
.business-splash {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: #0f172a; /* Fondo base oscuro */
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  color: white;
}

.splash-content {
  position: relative;
  z-index: 2;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
}

.logo-wrapper {
  width: 120px;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.business-logo {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  filter: drop-shadow(0 0 20px rgba(255,255,255,0.2));
}

.fallback-logo {
  width: 80px;
  height: 80px;
  background: var(--primary);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  font-weight: bold;
}

.business-name {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.025em;
  margin: 0;
}

.boot-message {
  font-size: 0.875rem;
  opacity: 0.6;
  margin-top: 0.5rem;
}

.progress-bar {
  width: 200px;
  height: 3px;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  margin-top: 1.5rem;
  overflow: hidden;
}

.progress-line {
  width: 40%;
  height: 100%;
  background: var(--primary);
  border-radius: 10px;
  animation: loading 2s infinite ease-in-out;
}

.splash-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  opacity: 0.15;
  filter: blur(8px);
  z-index: 1;
}

/* Animaciones */
@keyframes loading {
  0% { transform: translateX(-150%); }
  100% { transform: translateX(250%); }
}

.pulse {
  animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.8; }
}

.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-leave-to {
  opacity: 0;
}
</style>