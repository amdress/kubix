/**
 * ============================================================
 * DASHBOARD STORE (Versión Coherente con authStore y fakeDB)
 * ============================================================
 * - Maneja el contexto de navegación geográfica (State → City → Neighborhood)
 * - Se sincroniza con authStore para obtener el país activo
 * - Trabaja en conjunto con fakeDashboardDB.js para obtener datos
 * ============================================================
 */

import { defineStore } from "pinia";
import { useAuthStore } from "@/Kubix/core/stores/authStore";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        // Contexto de navegación geográfica
        context: {
            state: null,        // Código del estado (ej: "PR", "SP")
            stateName: null,    // Nombre del estado (ej: "Paraná")
            city: null,         // Nombre de la ciudad
            neighborhood: null, // Nombre del barrio
        },
    }),

    getters: {
        /**
         * Nivel actual de navegación geográfica
         * Global → Country → State → City → Neighborhood
         */
        level(state) {
            if (state.context.neighborhood) return "Neighborhood";
            if (state.context.city) return "City";
            if (state.context.state) return "State";

            const auth = useAuthStore();
            
            // Si hay un país activo que NO es GLOBAL, estamos en nivel "Country"
            if (auth.activeCountry?.code && auth.activeCountry.code !== "GLOBAL") {
                return "Country";
            }
            
            return "Global";
        },

        /**
         * Breadcrumb de navegación
         * Ejemplo: ["Global", "Brasil", "Paraná", "Curitiba"]
         */
        breadcrumb(state) {
            const bc = ["Global"];
            const auth = useAuthStore();

            if (auth.activeCountry?.code && auth.activeCountry.code !== "GLOBAL") {
                bc.push(auth.activeCountry.name);
                
                if (state.context.state) {
                    bc.push(state.context.stateName || state.context.state);
                }
                
                if (state.context.city) {
                    bc.push(state.context.city);
                }
                
                if (state.context.neighborhood) {
                    bc.push(state.context.neighborhood);
                }
            }

            return bc;
        },

        /**
         * Query object para fakeDashboardDB
         */
        queryContext(state) {
            const auth = useAuthStore();
            
            return {
                level: this.level,
                country: auth.activeCountry?.code || "GLOBAL",
                state: state.context.state,
                city: state.context.city,
                neighborhood: state.context.neighborhood,
            };
        },
    },

    actions: {
        /**
         * Establece el estado activo
         */
        setState(stateCode, stateName = null) {
            this.context.state = stateCode;
            this.context.stateName = stateName || stateCode;
            this.context.city = null;
            this.context.neighborhood = null;
        },

        /**
         * Establece la ciudad activa
         */
        setCity(cityName) {
            this.context.city = cityName;
            this.context.neighborhood = null;
        },

        /**
         * Establece el barrio activo
         */
        setNeighborhood(name) {
            this.context.neighborhood = name;
        },

        /**
         * Navega a un nivel específico
         */
        setLevel(levelName) {
            if (levelName === "Global") {
                this.reset();
            } else if (levelName === "Country") {
                this.context.state = null;
                this.context.stateName = null;
                this.context.city = null;
                this.context.neighborhood = null;
            } else if (levelName === "State") {
                this.context.city = null;
                this.context.neighborhood = null;
            } else if (levelName === "City") {
                this.context.neighborhood = null;
            }
        },

        /**
         * Resetea todo el contexto a Global
         */
        reset() {
            this.context.state = null;
            this.context.stateName = null;
            this.context.city = null;
            this.context.neighborhood = null;
        },
    },
});