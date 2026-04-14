/**
 * ════════════════════════════════════════════════════════════════
 * ⬇️ KUBIX — Response Interceptor
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Normalizar data y mapear errores a códigos KUBIX.
 * FILOSOFÍA: "Traductor" - Convierte el ruido de Axios en datos puros.
 * ════════════════════════════════════════════════════════════════
 */

const mapStatusToCode = (status) => {
  const map = {
    400: 'BAD_REQUEST',
    401: 'UNAUTHORIZED',
    403: 'FORBIDDEN',
    404: 'NOT_FOUND',
    422: 'VALIDATION_ERROR',
    429: 'RATE_LIMIT',
    500: 'SERVER_ERROR',
    503: 'SERVICE_UNAVAILABLE',
  };
  return map[status] || 'UNKNOWN_ERROR';
};

export function setupResponseInterceptor(apiClient) {
  apiClient.interceptors.response.use(
    (response) => {
      if (process.env.NODE_ENV === 'development') {
        console.log(
          `%c ✅ [API:RECEIVE] ${response.config.url}`,
          'color: #10b981; font-weight: bold; border-left: 3px solid #10b981; padding-left: 5px;',
          response.data
        );
      }
      return response.data; // Retorna directamente el body de Laravel
    },
    (error) => {
      let normalizedError = {
        type: error.response ? 'http' : 'network',
        status: error.response?.status || 0,
        message: error.response?.data?.message || 'Erro de conexão ou erro inesperado.',
        errors: error.response?.data?.errors || {},
        code: mapStatusToCode(error.response?.status),
        url: error.config?.url || 'unknown',
        shouldRetry: error.response?.status >= 500 || !error.response,
        original: error,
      };

      return Promise.reject(normalizedError);
    }
  );
}