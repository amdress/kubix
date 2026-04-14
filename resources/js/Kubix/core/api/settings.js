/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — API Settings (Core)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /Kubix/core/api/settings.js
 * * RESPONSABILIDAD:
 * Definir el contrato estático de configuración de red.
 * * FILOSOFÍA:
 * - Robusto: Normaliza URLs automáticamente sin romper el protocolo.
 * - Estándar: Asegura el trailing slash para evitar duplicados en Axios.
 * ════════════════════════════════════════════════════════════════
 */

/**
 * Normaliza la URL base asegurando el protocolo y la versión.
 */
const getBaseUrl = () => {
  // 1. Obtener base del ENV o fallback local
  const rawUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
  const version = 'v1';

  // 2. Limpiar barras duplicadas al final del rawUrl
  const cleanUrl = rawUrl.replace(/\/+$/, '');

  // 3. Unir base + versión + trailing slash
  // La regex ([^:/])\/[/]+ asegura que 'http://' no se toque, pero 'api//v1' se vuelva 'api/v1'
  const fullPath = `${cleanUrl}/${version}/`.replace(/([^:/])\/[/]+/g, "$1/");

  return fullPath;
}

export const API_SETTINGS = {
  // Resultado esperado: "http://192.168.15.2:8000/api/v1/"
  baseURL: getBaseUrl(),

  timeouts: {
    STANDARD: 30000,
    UPLOAD: 120000,
    POLL: 5000,
  },

  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },

  sanctum: {
    withCredentials: true,
    xsrfCookieName: 'XSRF-TOKEN',
    xsrfHeaderName: 'X-XSRF-TOKEN',
  },
}

export default API_SETTINGS;