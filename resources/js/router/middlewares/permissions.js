/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Permissions
 * ════════════════════════════════════════════════════════════════
 */

export default function permissions({ to, next, authStore }) {
  console.log(
    `%c🔑 [Permissions Check] ${to.name}`,
    'color: #f59e0b; font-weight: bold; font-size: 12px'
  )

  // 1. REQUISITO: ¿La ruta exige permisos?
  const routePermissions = to.meta?.permissions
  if (!routePermissions) {
    console.log('%c   ✅ Sin requisito de permisos', 'color: #10b981; font-size: 11px')
    return next()
  }

  // 2. EXTRACCIÓN DEL STORE: Acceso seguro a identidad y capacidades
  const userPerms = authStore.permissions || []
  const userRole = authStore.role || authStore.user?.role || 'guest'

  // 3. NORMALIZACIÓN: Permisos requeridos a Array
  const requiredPerms = Array.isArray(routePermissions) 
    ? routePermissions 
    : [routePermissions]

  console.log(
    `%c   Role: ${userRole.toUpperCase()} | Requeridos: [${requiredPerms.join(', ')}]`,
    'color: #94a3b8; font-size: 11px'
  )

  // 4. PASO VIP: Superadmin rompe cualquier bloqueo de permiso
  if (userRole === 'superadmin') {
    console.log('%c   👑 Acceso Superadmin (Bypass Permissions)', 'color: #10b981; font-weight: bold')
    return next()
  }

  // 5. COMODÍN: Si el usuario tiene el wildcard '*', tiene todo
  if (userPerms.includes('*')) {
    console.log('%c   ✨ Permiso Wildcard "*" detectado', 'color: #10b981; font-weight: bold')
    return next()
  }

  // 6. LÓGICA AND: El usuario debe tener TODOS los permisos de la lista
  const missingPerms = requiredPerms.filter(perm => !userPerms.includes(perm))
  const hasAccess = missingPerms.length === 0

  if (hasAccess) {
    console.log('%c   ✅ Todos los permisos verificados', 'color: #10b981')
    return next()
  }

  // 7. DENEGACIÓN: Mostrar qué falta para facilitar el debug
  console.error(
    `%c   ❌ Permisos insuficientes. Faltan: [${missingPerms.join(', ')}]`,
    'color: #ef4444; font-weight: bold'
  )

  return next({ name: 'error.403' })
}