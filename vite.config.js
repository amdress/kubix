/**
 * ════════════════════════════════════════════════════════════════
 * ⚡ KUBIX — Vite Config (STABLE NETWORK MODE)
 * ════════════════════════════════════════════════════════════════
 *
 * OBJETIVO:
 * - Funcionar en PC + móvil SIN errores de red
 * - Evitar 0.0.0.0 en navegador
 * - HMR estable
 * - PWA desactivado en DEV
 *
 * ════════════════════════════════════════════════════════════════
 */

import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,

    // 🔥 CLAVE REAL
    hmr: {
      host: '192.168.15.2',
      protocol: 'ws',
      port: 5173,
    },
  },

  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),

    vue(),

    // 🔥 PWA CONTROLADO
    VitePWA({
      registerType: 'autoUpdate',
      manifestFilename: 'manifest.webmanifest',

      devOptions: {
        enabled: false, // 🔥 NO CACHE EN DEV
      },

      manifest: {
        name: 'Kubix',
        short_name: 'Kubix',
        start_url: '/',
        display: 'standalone',
        background_color: '#020617',
        theme_color: '#3b82f6',
        icons: [
          {
            src: '/icons/icon-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: '/icons/icon-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
        ],
      },
    }),
  ],

  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
      '@core': path.resolve(__dirname, './resources/js/Kubix/core'),
    },
  },
})