/**
 * ════════════════════════════════════════════════════════════════
 * 🌐 KUBIX — i18n (REFINADO)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/i18n/index.js
 *
 * RESPONSABILIDAD:
 * - Resolver idioma inicial de forma consistente
 * - Mantener sincronización entre app, DOM y localStorage
 * - Exponer API simple para cambio de idioma
 *
 * SOPORTA:
 * - pt-BR (default)
 * - es
 * - en
 * ════════════════════════════════════════════════════════════════
 */

import { createI18n } from 'vue-i18n'

import ptBR from './locales/pt-BR.json'
import es   from './locales/es.json'
import en   from './locales/en.json'

const STORAGE_KEY = 'kubix_locale'

const SUPPORTED = ['pt-BR', 'es', 'en']
const FALLBACK  = 'pt-BR'

/**
 * Normaliza cualquier entrada a un locale válido
 */
function normalizeLocale(locale) {
  if (!locale) return FALLBACK

  const lower = locale.toLowerCase()

  if (lower.startsWith('pt')) return 'pt-BR'
  if (lower.startsWith('es')) return 'es'
  if (lower.startsWith('en')) return 'en'

  return FALLBACK
}

/**
 * Detecta idioma inicial
 * Prioridad:
 * 1. localStorage
 * 2. <html lang="">
 * 3. navegador
 * 4. fallback
 */
function detectLocale() {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved) return normalizeLocale(saved)

  const htmlLang = document.documentElement.lang
  if (htmlLang) return normalizeLocale(htmlLang)

  const browserLang = navigator.language || navigator.userLanguage
  return normalizeLocale(browserLang)
}

/**
 * Aplica efectos colaterales del idioma
 */
function applyLocaleEffects(locale) {
  document.documentElement.lang = locale
  localStorage.setItem(STORAGE_KEY, locale)
}

const initialLocale = detectLocale()

applyLocaleEffects(initialLocale)

const i18n = createI18n({
  legacy: false,                 // Composition API only
  locale: initialLocale,
  fallbackLocale: FALLBACK,
  globalInjection: true,

  messages: {
    'pt-BR': ptBR,
    es,
    en,
  },

  // Limpieza de consola
  missingWarn: false,
  fallbackWarn: false,
})

/**
 * Cambia idioma de forma segura
 */
export function setLocale(locale) {
  const normalized = normalizeLocale(locale)

  i18n.global.locale.value = normalized
  applyLocaleEffects(normalized)
}

/**
 * Lectura reactiva del idioma actual
 */
export const currentLocale = i18n.global.locale

export default i18n