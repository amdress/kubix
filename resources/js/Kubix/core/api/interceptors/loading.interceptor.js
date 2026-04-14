/**
 * ════════════════════════════════════════════════════════════════
 * ⏳ KUBIX — Loading Interceptor
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Gestionar el estado visual de carga global.
 * FILOSOFÍA: "Anti-Flicker" - Maneja concurrencia de peticiones.
 * ════════════════════════════════════════════════════════════════
 */

export function setupLoadingInterceptor(apiClient, { onStart, onFinish } = {}) {
  if (typeof onStart !== 'function' || typeof onFinish !== 'function') {
    throw new Error('[LoadingInterceptor] Callbacks onStart/onFinish son obligatorios.');
  }

  let activeRequests = 0;

  const increment = (config) => {
    if (config?.silent) return;
    if (activeRequests === 0) onStart();
    activeRequests++;
  };

  const decrement = (config) => {
    if (config?.silent) return;
    if (activeRequests > 0) activeRequests--;
    if (activeRequests === 0) onFinish();
  };

  apiClient.interceptors.request.use(
    (config) => { increment(config); return config; },
    (error) => { decrement(error?.config); return Promise.reject(error); }
  );

  apiClient.interceptors.response.use(
    (response) => { decrement(response.config); return response; },
    (error) => { decrement(error?.config); return Promise.reject(error); }
  );
}