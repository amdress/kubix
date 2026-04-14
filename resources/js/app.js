/**
 * ════════════════════════════════════════════════════════════════
 * 🚀 KUBIX — Core Orchestrator (Main Entry Point)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/app.js
 * ════════════════════════════════════════════════════════════════
 */

import './bootstrap'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './app.vue'
import i18n from './i18n'
import { setupPlugins } from '@/Kubix/core/plugins'
import { setupApi } from '@/Kubix/core/api'


// Import the stores that will be used in the boot process
// import { useContextStore } from '@/Kubix/core/stores/contextStore'
// import { useAuthStore } from '@/Kubix/core/stores/authStore'
// import { useSocialStore } from '@/Kubix/core/stores/socialStore'
import { useAuthStore , useContextStore , useSocialStore } from '@/Kubix/core/stores' // Importa otros stores si es necesario

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)

/**
 * ⚙️ FASE 1: Montaje Técnico (Síncrono)
 * Responsabilidad: Levantar plugins, stores y conectar la API.
 */
function bootCore() {
  console.log('%c[KUBIX] CORE: INIT', 'color:#a78bfa;font-weight:bold');

  // 1. Inicializar Plugins (UI/Charts/Maps)
  setupPlugins(app);



  // 2. Acceso a Stores Core
  const authStore = useAuthStore();
  const contextStore = useContextStore();
  const socialStore = useSocialStore();

  // 3. Setup de API (Inyección de dependencias pura)
  // Sin lógica de navegación. Solo conexión de datos.
  setupApi({
    authStore,
    contextStore
  });

  console.log('%c[KUBIX] STORES: READY', 'color:#22c55e;font-weight:bold');

  return { contextStore, authStore, socialStore };
}

/**
 * ⚡ FASE 2: Hidratación de Datos (Asíncrono)
 * Responsabilidad: Verificar sesión y estado inicial.
 */
async function bootstrap(core) {
  const { authStore, contextStore } = core;

  contextStore.setSplash(true);

  try {
    console.log('%c[KUBIX] BOOT: START', 'color:#3b82f6;font-weight:bold');

    // Solo inicializamos el estado de auth (ping/user check)
    // await authStore.init();

    console.log('%c[KUBIX] BOOT: READY', 'color:#10b981;font-weight:bold');


  } catch (error) {

    console.error('[KUBIX BOOT ERROR]', error);
  } finally {

    setTimeout(() => {  
      contextStore.setSplash(false);
      console.log('%c[KUBIX] BOOT: COMPLETE', 'color:#22c55e;font-weight:bold');
    }, 3000);

  }
}

/**
 * 🏁 EJECUCIÓN
 */
(async () => {
  // 1. Levantamos el Core
  const core = bootCore();

  // 2. Montamos la aplicación (UI Ready)
  app.mount('#app');

  // 3. Esperamos al Router y despertamos los datos
  await router.isReady();
  await bootstrap(core);
})();