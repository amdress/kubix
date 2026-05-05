import { defineStore } from 'pinia';

const userJuan = {
    permissions: ["kpi.read", "ads.manage"],
    scopesT: [
        { path: "0", name: "Global" },

        // Rama Brasil
        { path: "0/1", name: "Brasil" },

        // --- RAMA PARANÁ (0/1/1) ---
        { path: "0/1/1", name: "Paraná" },
        { path: "0/1/1/1", name: "Curitiba" },
        { path: "0/1/1/1/1", name: "Batel" },
        { path: "0/1/1/1/2", name: "Capão Raso" },

        // --- RAMA AMAZONAS (0/1/2) ---
        { path: "0/1/2", name: "Amazonas" },

        // Ciudad 1: Manaus (0/1/2/1)
        { path: "0/1/2/1", name: "Manaus" },
        { path: "0/1/2/1/1", name: "Armando Mendes" },
        { path: "0/1/2/1/2", name: "Centro" },

        // Ciudad 2: Manacapuru (0/1/2/2)
        { path: "0/1/2/2", name: "Manacapuru" },
        { path: "0/1/2/2/1", name: "Centro Manacapuru" },
        { path: "0/1/2/2/2", name: "Aparício" }
    ]
};

export const useAdminStore = defineStore('admin', {
    state: () => ({
        scopes: [],
        // Guardamos solo los IDs: ['0', '1', '1', '1', null]
        selections: [null, null, null, null, null],
    }),

    getters: {
        // 1. Para el TerritoryNav: ¿Qué opciones muestro en este nivel?
        getOptionsByLevel: (state) => (level) => {
            // Si es el nivel 0, devolvemos la raíz (Global)
            if (level === 0) return state.scopes.filter(s => s.path === "0");

            // Para niveles > 0, buscamos hijos del path actual seleccionado
            // Construimos el path padre: e.j. "0/1/1"
            const parentPath = state.selections.slice(0, level).join('/');

            // Filtramos scopes cuyo path sea exactamente "parentPath/ID"
            return state.scopes.filter(s => {
                const parts = s.path.split('/');
                const isDirectChild = parts.length === level + 1;
                const belongsToParent = s.path.startsWith(parentPath + '/');
                return isDirectChild && belongsToParent;
            });
        },

        // 2. Para el Header: ¿Cuál es el nombre de lo que estoy viendo?
        currentLabel: (state) => {
            const currentPath = state.selections.filter(s => s !== null).join('/');
            const match = state.scopes.find(s => s.path === currentPath);
            return match ? match.name : 'Global';
        },

        // 3. Para el Sidebar: ¿Estamos en un nivel que permite gestionar?
        isDeepestLevel: (state) => {
            const currentPath = state.selections.filter(s => s !== null).join('/');
            // Si no hay ningún scope en la lista que sea hijo de este, es el nivel final
            return !state.scopes.some(s => s.path.startsWith(currentPath + '/') && s.path !== currentPath);
        }
    },

    actions: {
        loadMock() {
            this.scopes = userJuan.scopesT;
            // Inicializamos con el primer nivel seleccionado siempre
            this.selections[0] = "0";

            // Auto-completar niveles si solo hay UNA opción posible (opcional)
            this.autoFill();
        },

        updateSelection(level, id) {
            this.selections[level] = id;
            // Reset de hijos
            for (let i = level + 1; i < this.selections.length; i++) {
                this.selections[i] = null;
            }
            this.autoFill();
            // --- EL LOG DETALLADO ---
            // const currentPath = this.selections.filter(s => s !== null).join('/');
            // console.group(`🎯 Navegación: ${this.currentLabel}`);
            // console.log(`Path ID: %c${currentPath}`, "color: #22d3ee; font-weight: bold");
            // console.log(`Nivel Profundo: ${this.isDeepestLevel ? 'SÍ (Leaf)' : 'NO (Branch)'}`);
            // console.groupEnd();
        },

        autoFill() {
            // Si al seleccionar un nivel, el siguiente nivel solo tiene 1 opción, la seleccionamos
            for (let i = 0; i < this.selections.length - 1; i++) {
                if (this.selections[i]) {
                    const nextOptions = this.getOptionsByLevel(i + 1);
                    if (nextOptions.length === 1 && !this.selections[i + 1]) {
                        this.selections[i + 1] = nextOptions[0].path.split('/').pop();
                    }
                }
            }
        }
    }
});