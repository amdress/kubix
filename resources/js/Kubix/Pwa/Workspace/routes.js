/**
 * ════════════════════════════════════════════════════════════════
 * 💼 KUBIX — Workspace Routes (workspace Management)
 * ════════════════════════════════════════════════════════════════
 * Ubicación: /resources/js/Kubix/Pwa/Workspace/routes.js
 * ════════════════════════════════════════════════════════════════
 */

const workspaceRoutes = [
  {
    path: "",
    redirect: '/app/workspace/Splash',
    children: [
      {
        path: "Splash",
        name: "workspace.public.businessSplash",
        component: () => import("./public/views/BusinessSplashView.vue"),
        meta: {
          showHeader: false,
          showFooter: false,
          showTabs: false,
        }
      },
      {
        path: "busisinessProfile",
        name: "workspace.public.businessProfile",
        component: () => import("./public/views/BusinessProfileView.vue"),
        meta: {
          showHeader: true,
          showFooter: false,
          showTabs: false,
        }
      },
      {
        path: "business",
        redirect: '/app/workspace/admin/list',
        children: [
          {
            path: "dashboard",
            name: "workspace.business.dashboard",
            component: () => import("./admin/views/DashboardView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          //  Creacion de negocio 
          {
            path: "onboarding",
            name: "workspace.admin.onboarding",
            component: () => import("./admin/views/business/OnBoardingView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          {
            path: "list",
            name: "workspace.admin.businessList",
            component: () => import("./admin/views/business/businessListView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          {
            path: "businessCreate",
            name: "workspace.admin.businessCreate",
            component: () => import("./admin/views/business/businessCreateView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          {
            path: "businessConfig",
            name: "workspace.admin.businessConfig",
            component: () => import("./admin/views/business/businessConfigView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          //  Advertising
          {
            path: "adsList",
            name: "workspace.admin.adsList",
            component: () => import("./admin/views/ads/AdsListView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
          {
            path: "adsCreate",
            name: "workspace.admin.adsCreate",
            component: () => import("./admin/views/ads/AdsCreateView.vue"),
            meta: {
              showHeader: true,
              showFooter: false,
              showTabs: false,
            }
          },
        ]
      }
    ]
  }
]

export default workspaceRoutes;