/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Unified Routes (vCLEAN - STRUCTURED)
 * ════════════════════════════════════════════════════════════════
 * GRUPOS: Auth (Guest), Social/Workspace (PWA), Kubix (Admin)
 * ════════════════════════════════════════════════════════════════
 */

import { useContextStore } from '@/Kubix/core/stores/contextStore'

// LAYOUTS
import AppLayout from '@/Kubix/Pwa/Layouts/AppLayout.vue'
import GuestLayout from '@/Kubix/Pwa/Layouts/GuestLayout.vue'
import AdminLayout from '@/Kubix/Pwa/Layouts/AdminLayout.vue' // <--- El nuevo "chasis"

// FEATURE ROUTES
import authRoutes from '@/Kubix/Pwa/Auth/routes'
import socialRoutes from '@/Kubix/Pwa/Social/routes'
import workspaceRoutes from '@/Kubix/Pwa/Workspace/routes'
import TerritoryKubixRoutes from '@/Kubix/Pwa/TerritoryKubix/routes' // El Dash que estamos limpiando

// 1. 🟢 CONTEXTO PÚBLICO (GuestLayout)
const guestRoutes = {
  path: '/',
  component: GuestLayout,
  children: [
    {
      path: '',
      name: 'home',
      component: async () => {
        const context = useContextStore();
        if (context.isMobile) {
          return await import('./Pages/Splash/CitySplashScreen.vue');
        }
        return await import('./Pages/Landing/LandingView.vue');
      },
      meta: { showHeader: true, showFooter: true }
    },
    ...authRoutes, // Login, Registro, etc.
    {
      path: 'no-coverage',
      name: 'public.no-coverage',
      component: () => import('@/Kubix/Pwa/Pages/Errors/NoCoverage.vue'),
    },
  ],
}

// 2. 🔵 CONTEXTO PWA / MOBILE-FIRST (AppLayout)
const appRoutes = {
  path: '/app',
  component: AppLayout,
  redirect: '/app/social',
  children: [
    {
      path: 'social',
      name: 'social',
      children: socialRoutes,
    },
    {
      path: 'workspace',
      name: 'workspace',
      children: workspaceRoutes,
    },
  ],
}

// 3. 🔴 CONTEXTO ADMINISTRATIVO / CONTROL PLANE (AdminLayout)
// Aquí es donde vive la magia de la jerarquía que construimos
const adminRoutes = {
  path: '/kubix',
  component: AdminLayout,
  redirect: { name: 'kubix.dashboard' },
  children: TerritoryKubixRoutes, 
}

// 4. ⚠️ FALLBACKS & ERRORS
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
  adminRoutes,
  ...errorRoutes,
]