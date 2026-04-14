import { defineStore } from "pinia";

export const useUIStore = defineStore("ui", {
  state: () => ({
    sidebarCollapsed: true,
    isDark: false, // El estado que sacamos del Dashboard
  }),

  actions: {
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    },
    // Nueva acción para el frame
    toggleTheme() {
      this.isDark = !this.isDark;
    }
  },
});