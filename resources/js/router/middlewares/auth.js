/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Auth
 * ════════════════════════════════════════════════════════════════
 *
 * @responsibility: Verificar el estado de autenticación y proteger rutas.
 *
 * ════════════════════════════════════════════════════════════════
 */

export default function auth({ to, next, authStore }) {
  console.log(
    `%c🔐 [Auth Check] ${to.name}`,
    'color: #3b82f6; font-weight: bold; font-size: 12px'
  )

  const isAuthenticated = authStore.isAuthenticated

  // 1. CASO: RUTA DE INVITADO (Login/Register) Y USUARIO YA LOGUEADO
  // Si estoy logueado y trato de ir a login, me escolta al interior.
  if (to.meta.guestOnly && isAuthenticated) {
    console.warn('%c   ⚠️ Ya autenticado -> Rebotando al Mural', 'color: #f59e0b')
    return next({ name: 'user.mural' })
  }

  // 2. CASO: RUTA PROTEGIDA Y USUARIO NO LOGUEADO
  // Si no estoy logueado y la ruta no es de invitado, al login.
  if (!isAuthenticated && !to.meta.guestOnly) {
    console.error('%c   ❌ No autenticado -> Al Login', 'color: #ef4444')
    return next({
      name: 'auth.access',
      query: { redirect: to.fullPath },
    })
  }

  // 3. CASO: TODO CORRECTO
  console.log('%c   ✅ Acceso Permitido', 'color: #10b981')
  return next()
}