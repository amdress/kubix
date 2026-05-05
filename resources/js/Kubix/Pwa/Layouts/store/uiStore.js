import { defineStore } from "pinia";

export const useUIStore = defineStore("ui", {
  state: () => ({
    sidebarCollapsed: true,
    isDark: false,
    isCameraOpen: false,

  }),

  actions: {
    toggleSidebar() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
    },

    toggleTheme() {
      this.isDark = !this.isDark;

      document.documentElement.classList.toggle('dark', this.isDark);
    },

    //Control explícito camera
    openCamera() {
      this.isCameraOpen = true;
    },

    closeCamera() {
      this.isCameraOpen = false;
    },

    toggleCamera() {
      this.isCameraOpen = !this.isCameraOpen;
    }
  },
});