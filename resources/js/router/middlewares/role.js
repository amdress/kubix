/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Role
 * ════════════════════════════════════════════════════════════════
 */

export default function role({ to, next, authStore }) {
  console.log(
    `%c👥 [Role Check] ${to.name}`,
    'color: #06b6d4; font-weight: bold; font-size: 12px'
  )

  // 1. REQUISITO DE RUTA: ¿Pide roles?
  const routeRoles = to.meta?.role;
  if (!routeRoles) {
    console.log('%c   ✅ Sin requisito de rol', 'color: #10b981; font-size: 11px')
    return next()
  }

  // 2. CONSULTA AL STORE: Accedemos al estado del usuario
  // Accedemos a authStore.user.role o authStore.role según tu estructura
  const userRole = authStore.user?.role || authStore.role || 'guest';

  // 3. NORMALIZACIÓN: Roles requeridos a Array
  const requiredRoles = Array.isArray(routeRoles) ? routeRoles : [routeRoles];

  console.log(
    `%c   Usuario: ${userRole.toUpperCase()} | Requerido: [${requiredRoles.join(', ')}]`,
    'color: #94a3b8; font-size: 11px'
  )

  // 4. BYPASS: Superadmin tiene acceso total
  if (userRole === 'superadmin') {
    console.log('%c   👑 Acceso Superadmin garantizado', 'color: #10b981; font-weight: bold')
    return next()
  }

  // 5. EVALUACIÓN: Comparación de rol actual vs requeridos
  if (requiredRoles.includes(userRole)) {
    console.log('%c   ✅ Rol verificado y permitido', 'color: #10b981')
    return next()
  }

  // 6. DENEGACIÓN: Error 403
  console.error(`%c   ❌ Acceso denegado para el rol: ${userRole}`, 'color: #ef4444')
  return next({ name: 'error.403' })
}