/**
 * ============================================================
 * USER ROUTES
 * Contexto: Privado / Cliente
 * Layout: AppLayout
 * ============================================================
 */


export default [
    {
        path: "",
        name: "user.mural",
        component: () => import("./Views/Home.vue"),
        meta: {
            showHeader: true,
            showTabs: true,    // Visible para navegación rápida
            showFooter: true   // En el Mural (Home) solemos dejar el footer en Desktop
        }
    },

    {
        path: "radar",
        name: "user.radar",
        component: () => import('./views/RadarPage.vue'),
        meta: {
            showHeader: true,
            showTabs: true,    
            showFooter: false
        }
    },

    {
        path: "publications",
        name: "user.publications",
        component: () => import('./Views/Publications.vue'),
        meta: {
            showHeader: true,
            showTabs: true,    
            showFooter: false
        }
    },

    {
        path: "profile",
        name: "user.profile",
        component: () => import("./Views/Profile.vue"),
        meta: {
            showHeader: true,
            showTabs: false,   
            showFooter: false
        }
    },
];