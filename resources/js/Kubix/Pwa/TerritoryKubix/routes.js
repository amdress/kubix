/**
 * ════════════════════════════════════════════════════════════════
 * 🛠️ KUBIX — Core/Admin Routes
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/Pwa/Kubix/routes.js
 * Responsabilidad: Panel de control maestro y gestión de territorios.
 * ════════════════════════════════════════════════════════════════
 */

const TerritoryKubixRoutes = [
  {
    path: "",
    name: "kubix.dashboard",
    component: () => import("./Views/Monitor.vue"),
    meta: {
      title: "Master Dashboard",
       usesTerritoryNav: true,
    }
  },
  {
    path: "home",
    name: "kubix.home",
    component: () => import("./views/Monitor.vue"),
    meta: {
      title: "Master Dashboard",
    }
  },
  {
    path: "create",
    name: "kubix.territoryCreate",
    component: () => import('./Views/CreateNew.vue'),
    meta: {
      title: "Create New Territory"
    }
  },
  {
    path: "settings",
    name: "kubix.territorySettings",
    component: () => import('./Views/SettingView.vue'),
    meta: {
      title: "Create New Territory"
    }
  }



];

export default TerritoryKubixRoutes;