/**
 * ════════════════════════════════════════════════════════════════
 * 🛠️ KUBIX — Core/Admin Routes
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/Pwa/Kubix/routes.js
 * Responsabilidad: Panel de control maestro y gestión de territorios.
 * ════════════════════════════════════════════════════════════════
 */

const kubixRoutes = [
  {
    path: "",
    name: "kubix.home",
    component: () => import("./Views/Home.vue"),
    meta: { 
      title: "Master Dashboard",
      showHeader: true,
      showTabs: false // En el core admin, el ruido social suele estorbar
    }
  },
  // {
  //   path: "territory-monitor",
  //   name: "kubix.territory.monitor",
  //   component: () => import("./Views/TerritoryMonitor.vue"),
  //   meta: { 
  //     title: "Monitor de Territórios",
  //     showHeader: true,
  //     showTabs: false
  //   }
  // }
];

export default kubixRoutes;