/**
 * ════════════════════════════════════════════════════════════════
 * 💼 KUBIX — Workspace Routes (workspace Management)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/Pwa/Workspace/routes.js
 * ════════════════════════════════════════════════════════════════
 */

const workspaceRoutes = [
  {
    path: "/workspace/:slug",
    children: [
      {
        path: "",
        name: "workspace.public.splash",
        component: () => import("./public/views/SplashView.vue"),
      },
      {
        path: "landing",
        name: "workspace.public.landing",
        component: () => import("./public/views/LandingView.vue"),
      },
      {
        path: "admin",
        children: [
          {
            path: "dashboard",
            name: "workspace.admin.dashboard",
            component: () => import("./admin/views/DashboardView.vue"),
            meta: { requiresAuth: true, requiresOwner: true }
          },
           {
            path: "list",
            name: "workspace.admin.list",
            component: () => import("./admin/views/businessListView.vue"),
            meta: {  }
          },
        ]
      }
    ]
  }
]

export default workspaceRoutes;