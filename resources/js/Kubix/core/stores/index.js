/**
 * ════════════════════════════════════════════════════════════════
 * 🏗️ KUBIX — Stores Index (THE FULL STACK)
 * ════════════════════════════════════════════════════════════════
 */

export { useAuthStore } from "./authStore"
export { useContextStore } from "./contextStore"
export { useSocialStore } from "./socialStore"
export { useTerritoryStore } from "./territoryStore"
export { useWorkSpaceStore } from "./workspaceStore"
export { useZoneStore } from "./zoneStore"

/**
 * Reset total para Logout
 */
export function InitAllStores() {
  const stores = [
    useAuthStore(),
    useContextStore(),
    useSocialStore(),
    useTerritoryStore(),
    useWorkSpaceStore(),
    useZoneStore()
  ]

  stores.forEach(s => s.init?.())
  console.log('%c[KUBIX] UNIVERSO RESETEADO', 'color: #ef4444; font-weight: bold')
}