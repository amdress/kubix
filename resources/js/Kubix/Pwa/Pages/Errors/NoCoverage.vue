<template>
    <div class="no-coverage">
      <!-- Fondo con gradiente -->
      <div class="background-gradient" />
  
      <!-- Contenido principal -->
      <div class="content-wrapper">
        <!-- Logo Kubix -->
        <div class="logo-section">
          <img
            src="@/assets/images/shared/logo_transparent.png"
            alt="Kubix"
            class="logo"
          />
        </div>
  
        <!-- Mensaje de error -->
        <div class="error-section">
          <h1 class="error-title">Sin cobertura en tu zona</h1>
          <p class="error-message">
            Lo sentimos, Kubix aún no está disponible en tu área.
            <br />
            Estamos trabajando para expandir nuestros servicios.
          </p>
        </div>
  
        <!-- Información de ubicación detectada -->
        <div class="location-info" v-if="locationStore.city">
          <div class="location-badge">
            <span class="location-icon">📍</span>
            <span class="location-text">
              {{ locationStore.neighborhood || locationStore.city }}, {{ locationStore.city }}
            </span>
          </div>
        </div>
  
        <!-- Acciones -->
        <div class="actions">
          <!-- Botón: Reintentar -->
          <button @click="retryDetection" class="btn btn-primary" :disabled="isRetrying">
            <span v-if="!isRetrying">🔄 Reintentar Ubicación</span>
            <span v-else>Reintentando...</span>
          </button>
  
          <!-- Botón: Contactar soporte -->
          <button @click="openSupport" class="btn btn-secondary">
            💬 Contactar Soporte
          </button>
  
          <!-- Botón: Cambiar ubicación manual -->
          <button @click="showLocationPicker = true" class="btn btn-tertiary">
            🗺️ Cambiar Ubicación
          </button>
        </div>
  
        <!-- Información adicional -->
        <div class="info-box">
          <h3 class="info-title">¿Por qué ves este mensaje?</h3>
          <ul class="info-list">
            <li>📌 Tu ubicación no está en nuestro sistema</li>
            <li>⏳ La zona está en proceso de activación</li>
            <li>🔍 Verifica que los permisos de ubicación estén activados</li>
          </ul>
        </div>
  
        <!-- Newsletter (opcional) -->
        <div class="newsletter-section">
          <p class="newsletter-text">Notifícate cuando Kubix llegue a tu zona</p>
          <div class="newsletter-form">
            <input
              v-model="email"
              type="email"
              placeholder="tu@email.com"
              class="email-input"
              @keyup.enter="subscribeNewsletter"
            />
            <button @click="subscribeNewsletter" class="btn-subscribe">
              Suscribirse
            </button>
          </div>
          <p v-if="subscribeMessage" class="subscribe-message" :class="subscribeStatus">
            {{ subscribeMessage }}
          </p>
        </div>
      </div>
  
      <!-- Modal: Selector de ubicación -->
      <div v-if="showLocationPicker" class="modal-overlay" @click="showLocationPicker = false">
        <div class="modal-content" @click.stop>
          <h2>Cambiar Ubicación</h2>
          <p class="modal-subtitle">Selecciona tu ciudad</p>
  
          <div class="city-selector">
            <select v-model="selectedCity" class="city-select">
              <option value="">-- Selecciona una ciudad --</option>
              <option v-for="city in availableCities" :key="city" :value="city">
                {{ city }}
              </option>
            </select>
          </div>
  
          <div class="modal-actions">
            <button @click="checkSelectedCity" class="btn btn-primary">
              Verificar
            </button>
            <button @click="showLocationPicker = false" class="btn btn-secondary">
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useLocationStore } from '@/Kubix/core/stores/location'
  import TerritoriesService from '@/Kubix/core/services/Territory/territoriesService'
  
  // ────────────────────────────────────────────────────────────
  // SETUP
  // ────────────────────────────────────────────────────────────
  const router = useRouter()
  const locationStore = useLocationStore()
  
  // ────────────────────────────────────────────────────────────
  // STATE
  // ────────────────────────────────────────────────────────────
  const isRetrying = ref(false)
  const email = ref('')
  const subscribeMessage = ref('')
  const subscribeStatus = ref('')
  const showLocationPicker = ref(false)
  const selectedCity = ref('')
  const availableCities = ref([
    'Curitiba',
    'São Paulo',
    'Rio de Janeiro',
    'Belo Horizonte',
    'Salvador',
    'Brasília',
  ])
  
  // ────────────────────────────────────────────────────────────
  // MÉTODOS
  // ────────────────────────────────────────────────────────────
  
  /**
   * retryDetection: Reintentar detección de ubicación
   * Limpia localStorage y vuelve a bootstrap
   */
  const retryDetection = async () => {
    isRetrying.value = true
    
    try {
      // Limpiar localStorage para forzar nuevo bootstrap
      localStorage.removeItem('kubix_last_branch')
      
      // Reintentar ubicación
      await locationStore.init()
      
      // Si ahora tenemos ubicación, reiniciar app
      if (locationStore.isReady) {
        window.location.reload()
      } else {
        alert('No se pudo detectar ubicación. Verifica los permisos.')
      }
    } catch (error) {
      console.error('Error reintentando ubicación:', error)
      alert('Error al reintentar. Por favor, recarga la página.')
    } finally {
      isRetrying.value = false
    }
  }
  
  /**
   * checkSelectedCity: Verificar disponibilidad de ciudad seleccionada
   */
  const checkSelectedCity = async () => {
    if (!selectedCity.value) {
      alert('Por favor, selecciona una ciudad')
      return
    }
  
    try {
      const validation = await TerritoriesService.checkAvailability({
        country: 'Brasil',
        region: 'Paraná', // TODO: Hacer dinámico
        city: selectedCity.value,
        neighborhood: null,
        lat: null,
        lon: null,
      })
  
      if (validation.exists && validation.isActive) {
        // Guardar y recargar
        localStorage.setItem('kubix_last_branch', selectedCity.value)
        window.location.reload()
      } else {
        alert(`${selectedCity.value} aún no tiene servicio disponible.`)
      }
    } catch (error) {
      console.error('Error verificando ciudad:', error)
      alert('Error al verificar la ciudad.')
    }
  }
  
  /**
   * subscribeNewsletter: Suscribirse a notificaciones
   */
  const subscribeNewsletter = async () => {
    if (!email.value || !email.value.includes('@')) {
      subscribeStatus.value = 'error'
      subscribeMessage.value = 'Por favor, ingresa un email válido'
      return
    }
  
    try {
      // TODO: Llamar a endpoint de newsletters
      // await api.post('/api/newsletter/subscribe', { email: email.value })
      
      subscribeStatus.value = 'success'
      subscribeMessage.value = '✅ ¡Gracias! Te notificaremos cuando lleguemos a tu zona.'
      email.value = ''
      
      setTimeout(() => {
        subscribeMessage.value = ''
      }, 5000)
    } catch (error) {
      subscribeStatus.value = 'error'
      subscribeMessage.value = '❌ Error al suscribirse. Intenta de nuevo.'
      console.error('Error suscribiendo:', error)
    }
  }
  
  /**
   * openSupport: Abrir contacto de soporte
   */
  const openSupport = () => {
    // Abrir modal de soporte o redirigir a página de contacto
    window.open('mailto:soporte@kubix.com?subject=Sin cobertura en mi zona', '_blank')
    // O: router.push({ name: 'contact' })
  }
  </script>
  
  <style scoped>
  /* ─────────────────────────────────────────────────────────── */
  /* CONTENEDOR PRINCIPAL */
  /* ─────────────────────────────────────────────────────────── */
  .no-coverage {
    position: fixed;
    inset: 0;
    background-color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
    padding: 20px;
    z-index: 1000;
  }
  
  .background-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 58, 138, 0.2) 100%);
    pointer-events: none;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* CONTENIDO */
  /* ─────────────────────────────────────────────────────────── */
  .content-wrapper {
    position: relative;
    z-index: 10;
    text-align: center;
    max-width: 500px;
    width: 100%;
    animation: slideUp 0.6s ease-out;
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* LOGO */
  /* ─────────────────────────────────────────────────────────── */
  .logo-section {
    margin-bottom: 40px;
  }
  
  .logo {
    width: 100px;
    height: 100px;
    object-fit: contain;
    opacity: 0.8;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* ERROR SECTION */
  /* ─────────────────────────────────────────────────────────── */
  .error-section {
    margin-bottom: 40px;
  }
  
  .error-title {
    font-size: 2rem;
    font-weight: 700;
    color: white;
    margin: 0 0 16px 0;
    letter-spacing: -0.5px;
  }
  
  .error-message {
    font-size: 0.95rem;
    color: #cbd5e1;
    line-height: 1.6;
    margin: 0;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* LOCATION INFO */
  /* ─────────────────────────────────────────────────────────── */
  .location-info {
    margin-bottom: 40px;
  }
  
  .location-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 20px;
    font-size: 0.9rem;
    color: #3b82f6;
  }
  
  .location-icon {
    font-size: 1.1rem;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* BOTONES */
  /* ─────────────────────────────────────────────────────────── */
  .actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 40px;
  }
  
  .btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: none;
  }
  
  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
  }
  
  .btn-secondary {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
  }
  
  .btn-secondary:hover {
    background: rgba(59, 130, 246, 0.2);
  }
  
  .btn-tertiary {
    background: transparent;
    color: #94a3b8;
    border: 1px solid rgba(148, 163, 184, 0.3);
  }
  
  .btn-tertiary:hover {
    color: #cbd5e1;
    border-color: rgba(148, 163, 184, 0.5);
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* INFO BOX */
  /* ─────────────────────────────────────────────────────────── */
  .info-box {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 40px;
    text-align: left;
  }
  
  .info-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #cbd5e1;
    margin: 0 0 12px 0;
  }
  
  .info-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .info-list li {
    font-size: 0.85rem;
    color: #94a3b8;
    padding: 6px 0;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* NEWSLETTER */
  /* ─────────────────────────────────────────────────────────── */
  .newsletter-section {
    background: rgba(34, 197, 94, 0.05);
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 12px;
    padding: 24px;
  }
  
  .newsletter-text {
    font-size: 0.9rem;
    color: #cbd5e1;
    margin: 0 0 16px 0;
  }
  
  .newsletter-form {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
  }
  
  .email-input {
    flex: 1;
    padding: 10px 14px;
    background: rgba(148, 163, 184, 0.1);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 6px;
    color: white;
    font-size: 0.9rem;
  }
  
  .email-input::placeholder {
    color: #64748b;
  }
  
  .email-input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
  }
  
  .btn-subscribe {
    padding: 10px 18px;
    background: #22c55e;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }
  
  .btn-subscribe:hover {
    background: #16a34a;
    transform: translateY(-1px);
  }
  
  .subscribe-message {
    font-size: 0.85rem;
    margin: 8px 0 0 0;
  }
  
  .subscribe-message.success {
    color: #22c55e;
  }
  
  .subscribe-message.error {
    color: #ef4444;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* MODAL */
  /* ─────────────────────────────────────────────────────────── */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    backdrop-filter: blur(4px);
  }
  
  .modal-content {
    background: #1e293b;
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    padding: 32px;
    max-width: 400px;
    width: 100%;
    animation: slideUp 0.3s ease-out;
  }
  
  .modal-content h2 {
    margin: 0 0 8px 0;
    color: white;
    font-size: 1.3rem;
  }
  
  .modal-subtitle {
    color: #94a3b8;
    margin: 0 0 24px 0;
    font-size: 0.9rem;
  }
  
  .city-selector {
    margin-bottom: 24px;
  }
  
  .city-select {
    width: 100%;
    padding: 10px 14px;
    background: rgba(148, 163, 184, 0.1);
    border: 1px solid rgba(148, 163, 184, 0.2);
    border-radius: 6px;
    color: white;
    font-size: 0.9rem;
  }
  
  .city-select:focus {
    outline: none;
    border-color: #3b82f6;
  }
  
  .modal-actions {
    display: flex;
    gap: 12px;
  }
  
  .modal-actions .btn {
    flex: 1;
  }
  
  /* ─────────────────────────────────────────────────────────── */
  /* RESPONSIVE */
  /* ─────────────────────────────────────────────────────────── */
  @media (max-width: 640px) {
    .error-title {
      font-size: 1.5rem;
    }
  
    .error-message {
      font-size: 0.85rem;
    }
  
    .btn {
      padding: 10px 20px;
      font-size: 0.9rem;
    }
  
    .newsletter-form {
      flex-direction: column;
    }
  
    .email-input {
      width: 100%;
    }
  }
  </style>