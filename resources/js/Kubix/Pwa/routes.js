/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Unified Routes (vCLEAN - STRUCTURED)
 * ════════════════════════════════════════════════════════════════
 * GRUPOS: Auth, Social, Workspace, Kubix + Fallbacks
 * ════════════════════════════════════════════════════════════════
 */

import { useContextStore } from '@/Kubix/core/stores/contextStore'

// LAYOUTS
import AppLayout from '@/Kubix/Pwa/Layouts/AppLayout.vue'
import GuestLayout from '@/Kubix/Pwa/Layouts/GuestLayout.vue'

// FEATURE ROUTES
import authRoutes from '@/Kubix/Pwa/Auth/routes'           // Registro / Login
import socialRoutes from '@/Kubix/Pwa/Social/routes'       // Mural / Directorio / Market
import workspaceRoutes from '@/Kubix/Pwa/Workspace/routes' // Gestión de Negocios (Company/Branch)
import kubixRoutes from '@/Kubix/Pwa/Kubix/routes'         // Core / Admin / Territory Monitor

// 1. CONTEXTO PÚBLICO (Puerta de entrada & Auth)
const guestRoutes = {
  path: '/',
  component: GuestLayout,
  children: [
    {
      path: '',
      name: 'home',
      component: () => {
        const context = useContextStore();
        console.log('Contexto en Ruta Home:', context.isMobile ? 'Mobile' : 'Desktop');

        /**
         * RESOLUCIÓN DINÁMICA
         * Si es Mobile (PWA) -> Splash de Ciudad.
         * Si es Desktop -> Landing Page.
         */
        return context.isMobile
          ? import('./Pages/Splash/CitySplashScreen.vue')
          : import('./Pages/Landing/LandingView.vue');
      },
      meta: {
        showHeader: true,
        showFooter: true,
      }
    },

    // Flujo de Autenticación (Login, Registro, Recuperar)
    ...authRoutes,

    {
      path: 'no-coverage',
      name: 'public.no-coverage',
      component: () => import('@/Kubix/Pwa/Pages/Errors/NoCoverage.vue'),
      meta: { title: 'Sem Cobertura' }
    },
  ],
}

// 2. CONTEXTO PRIVADO (El ecosistema KUBIX)
const appRoutes = {
  path: '/app',
  component: AppLayout,
  children: [
    /**
     * 🌐 SOCIAL: El motor del barrio (Mural, Market, Events)
     */
    {
      path: 'social',
      children: socialRoutes,
    },

    /**
     * 💼 WORKSPACE: Gestión operativa (Empresas y Sucursales)
     */
    {
      path: 'workspace',
      children: workspaceRoutes,
    },

    /**
     * 🛠️ KUBIX: El panel de control del sistema y territorios
     */
    {
      path: 'kubix',
      children: kubixRoutes,
    }
  ],
}

// 3. FALLBACKS & ERRORS
const errorRoutes = [
  {
    path: '/403',
    name: 'error.403',
    component: () => import('@/Kubix/Pwa/Pages/Errors/Error403.vue'),
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'error.404',
    component: () => import('@/Kubix/Pwa/Pages/Errors/Error404.vue'),
  },
]

export default [
  guestRoutes,
  appRoutes,
  ...errorRoutes,
]