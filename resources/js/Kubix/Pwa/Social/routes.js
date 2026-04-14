/**
 * ════════════════════════════════════════════════════════════════
 * 🛤️ KUBIX — Social Routes (social Experience)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/Pwa/Social/routes.js
 * ════════════════════════════════════════════════════════════════
 */

const SocialRoutes = [
  {
    path: "",
    name: "social.mural", 
    component: () => import("./Views/Mural.vue"),
    meta: { 
      title: "Mural",
      showHeader: true,
      showTabs: true 
    }
  },
  {
    path: "directory",
    name: "social.directory",
    component: () => import("./Views/Directory.vue"),
    meta: { 
      title: "Guía Local",
      showHeader: true,
      showTabs: true 
    }
  },
  {
    path: "events",
    name: "social.events",
    component: () => import("./Views/Events.vue"),
    meta: { 
      title: "Eventos",
      showHeader: true,
      showTabs: true 
    }
  },
  {
    path: "market",
    name: "social.market",
    component: () => import("./Views/Market.vue"),
    meta: { 
      title: "Marketplace",
      showHeader: true,
      showTabs: true 
    }
  }
];

export default SocialRoutes;