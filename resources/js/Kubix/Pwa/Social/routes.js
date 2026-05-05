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
    component: () => import("./views/Mural/MuralView.vue"),
    meta: {
      title: "Mural",
      showHeader: true,
      showTabs: true
    }
  },
  {
    path: "radar",
    name: "social.radar",
    component: () => import("./views/Radar/RadarView.vue"),
    meta: {
      title: "Guía Local",
      showHeader: true,
      showTabs: true
    }
  },
  {
    path: "events",
    name: "social.events",
    component: () => import("./views/Events/EventsView.vue"),
    meta: {
      title: "Eventos",
      showHeader: true,
      showTabs: true
    }
  },
  {
    path: "market",
    name: "social.market",
    component: () => import("./Views/Market/MarketView.vue"),
    meta: {
      title: "Marketplace",
      showHeader: true,
      showTabs: true
    }
  },
  {
    path: "market/:id",
    name: "social.market.detail",
    component: () => import("./Views/Market/MarketDetailView.vue"),
    meta: {
      title: "Detalle",
      showHeader: true,
      showTabs: false
    }
  },
  // user 
  {
    path: "profile",
    name: "social.profile",
    component: () => import("./views/Profile/ProfileView.vue"),
    meta: {
      title: "Perfil",
      showHeader: true,
      showTabs: true
    }
  },

];

export default SocialRoutes;