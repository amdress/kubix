/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Workspace
 * ════════════════════════════════════════════════════════════════
 * * @responsibility: Validar que exista un contexto empresarial activo.
 * * @logic:
 * ✅ Ruta Business + Workspace Activo → next()
 * ❌ Ruta Business + Sin Workspace → Redirigir al Mural (Modo Personal)
 * 🔄 Sincronización automática con el ContextStore si es necesario.
 *
 * ════════════════════════════════════════════════════════════════
 */

export default function workspace({ to, next, workspaceStore }) {
  console.log(
    `%c🏢 [Workspace Check] ${to.name}`,
    'color: #10b981; font-weight: bold; font-size: 12px'
  )

  const hasActiveWorkspace = workspaceStore.isActive

  // 1. VALIDACIÓN DE ENTRADA A MUNDO BUSINESS
  if (to.meta.requiresBusiness) {
    if (!hasActiveWorkspace) {
      console.warn(
        '%c   ⚠️ Intento de acceso a Gestión sin Workspace activo. Redirigiendo...',
        'color: #f59e0b; font-weight: bold'
      )
      
      // Si no hay empresa seleccionada, no puede estar aquí.
      // Lo mandamos al Mural para que elija una desde el Sidebar.
      return next({ name: 'user.mural' })
    }

    console.log(
      `%c   ✅ Workspace Activo: ${workspaceStore.current?.label}`,
      'color: #10b981; font-size: 11px'
    )
  }

  // 2. LIMPIEZA DE CONTEXTO (Opcional)
  // Si la ruta es exclusivamente personal, podríamos querer avisar
  // que el workspace sigue activo en segundo plano o resetearlo.
  if (to.meta.personalOnly && hasActiveWorkspace) {
    console.log('%c   ℹ️ Navegando en modo personal con Workspace en segundo plano.', 'color: #94a3b8')
  }

  return next()
}