/**
 * ════════════════════════════════════════════════════════════════
 * 🔐 KUBIX — Auth Interceptor
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Inyectar Bearer Token de forma segura.
 * FILOSOFÍA: "Zero-Leak" - No envía tokens a dominios externos.
 * ════════════════════════════════════════════════════════════════
 */

export function setupAuthInterceptor(apiClient, { getToken } = {}) {
  if (typeof getToken !== 'function') {
    throw new Error('[AuthInterceptor] getToken es obligatorio para el blindaje de seguridad.');
  }

  apiClient.interceptors.request.use(
    (config) => {
      // Prevención de fuga de tokens a APIs externas (ej. mapas, clima)
      const isExternal = config.url.startsWith('http') && !config.url.includes(config.baseURL);
      if (isExternal) return config;

      try {
        const token = getToken();
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
      } catch (error) {
        return Promise.reject({
          type: 'auth',
          status: 0,
          message: 'Erro crítico ao recuperar credenciais de acesso.',
          code: 'AUTH_TOKEN_FETCH_FAILURE',
          original: error,
        });
      }
      return config;
    },
    (error) => Promise.reject(error)
  );
}