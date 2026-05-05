
export default [
    {
        path: "",
        name: "kubix.dashboard",
        component: () => import("./Views/Dashboards/Dashboard.vue"),
    },
    {
        path: "branch",
        name: "kubix.branches.create",
        component: () => import("./Views/Branches/pages/BranchCreate.vue"),
    },
];


