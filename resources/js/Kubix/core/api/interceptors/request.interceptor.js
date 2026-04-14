/**
 * ════════════════════════════════════════════════════════════════
 * ⬆️ KUBIX — Request Interceptor
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Auditoría de salida y preparación de headers.
 * FILOSOFÍA: "Pista Limpia" - No altera lógica, solo observa y prepara.
 * ════════════════════════════════════════════════════════════════
 */

export function setupRequestInterceptor(apiClient) {
  apiClient.interceptors.request.use(
    (config) => {
      if (process.env.NODE_ENV === 'development') {
        console.log(
          `%c 🚀 [API:SEND] ${config.method.toUpperCase()} -> ${config.url}`,
          'color: #3b82f6; font-weight: bold; border-left: 3px solid #3b82f6; padding-left: 5px;',
          { params: config.params, data: config.data }
        );
      }
      return config;
    },
    (error) => Promise.reject(error)
  );
}