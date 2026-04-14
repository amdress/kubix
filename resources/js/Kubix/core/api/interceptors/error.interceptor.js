/**
 * ════════════════════════════════════════════════════════════════
 * 🧠 KUBIX — Error Interceptor (The Executor)
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Reaccionar ante errores normalizados (UI/Router).
 * FILOSOFÍA: "Decisor" - Ejecuta acciones basadas en el código del error.
 * ════════════════════════════════════════════════════════════════
 */

export function setupErrorInterceptor(apiClient, { onUnauthorized, onGlobalError } = {}) {
  apiClient.interceptors.response.use(
    (response) => response, // Pasa de largo en éxitos
    (normalizedError) => {
      
      switch (normalizedError.code) {
        case 'UNAUTHORIZED':
          // Acción: Patear al login si la sesión expiró
          if (typeof onUnauthorized === 'function') onUnauthorized(normalizedError);
          break;

        case 'SERVER_ERROR':
        case 'SERVICE_UNAVAILABLE':
        case 'NETWORK_ERROR':
          // Acción: Avisar al usuario que algo explotó (SweetAlert)
          if (typeof onGlobalError === 'function') onGlobalError(normalizedError);
          break;

        case 'VALIDATION_ERROR':
          // Acción: Silencio. El componente se encarga de mostrar los errores en los inputs.
          break;
      }

      return Promise.reject(normalizedError);
    }
  );
}