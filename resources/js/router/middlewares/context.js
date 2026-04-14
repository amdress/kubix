/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Middleware: Context
 * ════════════════════════════════════════════════════════════════
 *
 * @responsibility: Orquestar el entorno visual y operativo.
 *
 * @logic:
 * 🎭 Identifica el 'mundo' (social o business) desde la ruta.
 * 🎨 Sincroniza el ContextStore para aplicar branding y layout.
 * 🏗️ Prepara los slots del MainLayout (Sidebar expandido/colapsado).
 *
 * ════════════════════════════════════════════════════════════════
 */

export default function context({ to, next, contextStore, workspaceStore }) {
  // 1. DETERMINAR EL MUNDO (Por defecto: social)
  // Las rutas en el router deben tener meta: { world: 'business' } o 'social'
  const targetWorld = to.meta.world || 'social';

  console.log(
    `%c🎭 [Context Orchestrator] Mundo: ${targetWorld.toUpperCase()}`,
    'color: #8b5cf6; font-weight: bold; font-size: 12px'
  );

  // 2. CONFIGURAR EL STORE DE CONTEXTO
  // Esto debería disparar internamente los cambios de variables CSS (--primary-color, etc.)
  contextStore.setAppMode(targetWorld);

  // 3. LOGICA DE TRANSICIÓN PREMIUM
  // Si pasamos de Social a Business, podemos forzar el Splash de carga del territorio
  if (targetWorld === 'business' && workspaceStore.isActive) {
    console.log('%c   🏗️ Cargando entorno empresarial...', 'color: #94a3b8; font-size: 11px');
    // Aquí podrías disparar un loading state si la data del workspace es pesada
  }

  // 4. LIMPIEZA DE UI
  // Aseguramos que el Sidebar se comporte según el mundo (ej: colapsar en social para más espacio)
  if (targetWorld === 'social' && window.innerWidth < 1024) {
    // uiStore.closeSidebar(); // Podrías inyectar uiStore también si fuera necesario
  }

  return next();
}