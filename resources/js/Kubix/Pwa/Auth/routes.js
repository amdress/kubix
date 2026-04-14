/**
 * ============================================================
 * AUTH ROUTES (KUBIX ENGINE)
 * Contexto: Público
 * URL: /auth/access
 * ============================================================
 */

// Middlewares
import guest from "@/router/middlewares/guest";

export default [
    {
        path: "auth",
        meta: { 
            middleware: [],
        },
        children: [
            {
                path: "access", // Ruta única que contiene Welcome, Login, Register y Recover
                name: "auth.access",
                component: () => import("@/Kubix/Pwa/Auth/Views/AuthIndex.vue"),
            },
            // Redirección por si alguien entra a /auth seco
            {
                path: "",
                redirect: { name: 'auth.access' }
            }
        ],
    },
];