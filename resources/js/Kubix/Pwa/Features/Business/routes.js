export default [
    {
        path: "dashboard",
        name: "business.dashboard",
        component: () => import("./views/Dashboards/Dashboard.vue"),
        meta: {
            showFooter: true,
            showTabs: false
        }
    }
];

