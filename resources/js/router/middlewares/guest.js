/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Guest
 * ════════════════════════════════════════════════════════════════
 *
 * @responsibility: Blindar rutas exclusivas para invitados (Login, Register, etc.)
 *
 * @logic:
 * ✅ No autenticado → Acceso permitido (puede loguearse).
 * ❌ Autenticado → Rebotar al interior (ya no necesita loguearse).
 *
 * ════════════════════════════════════════════════════════════════
 */

export default function guest({ to, next, authStore }) {
  console.log(
    `%c👤 [Guest Check] ${to.name}`,
    'color: #8b5cf6; font-weight: bold; font-size: 12px'
  )

  const isAuthenticated = authStore.isAuthenticated

  // 1. EL FILTRO DE SEGURIDAD:
  // Si el usuario YA ESTÁ logueado, no debería estar en rutas de "invitado"
  if (isAuthenticated) {
    console.warn(
      '%c   ⚠️ Ya autenticado -> Rebotando al Mural para evitar duplicidad de sesión',
      'color: #f59e0b; font-weight: bold; font-size: 11px'
    )

    // Redirigimos al Mural (o Dashboard) porque ya tiene sesión activa.
    return next({ name: 'user.mural' })
  }

  // 2. CASO IDEAL:
  // Es un invitado real y está en una ruta para invitados.
  console.log(
    '%c   ✅ Invitado verificado. Acceso permitido.',
    'color: #10b981; font-size: 11px'
  )
  
  return next()
}