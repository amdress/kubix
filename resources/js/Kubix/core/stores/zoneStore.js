/**
 * ════════════════════════════════════════════════════════════════
 * 🧭 KUBIX — Zone Store (TERRITORIAL SELECTION GATEWAY vFINAL)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Manejar selección territorial del usuario (UI layer).
 * - Dropdown jerárquico: país → estado → ciudad → barrio.
 * - Sincronizar listas desde ZoneService.
 * - Construir path (/1/1/1/1).
 * - Emitir cambios al ContextStore.
 *
 * FLUJO:
 * UI → ZoneStore → ContextStore → SocialStore
 * ════════════════════════════════════════════════════════════════
 */

import { defineStore } from 'pinia'
import { useContextStore } from '@/Kubix/core/stores/contextStore'
import ZoneService from '@/Kubix/core/services/Territory/zoneService'

export const useZoneStore = defineStore('zone', {

  state: () => ({
    selected: {
      country: null,
      state: null,
      city: null,
      neighborhood: null
    },

    lists: {
      countries: [],
      states: [],
      cities: [],
      neighborhoods: []
    },

    loading: {
      countries: false,
      states: false,
      cities: false,
      neighborhoods: false
    },

    initialized: false
  }),

  getters: {
    context: (state) => useContextStore(),
    currentPath: (state) => useContextStore().current_path
  },

  actions: {

    async init() {
      await this.loadCountries()
      this.syncFromContext()
      this.initialized = true
    },

    async loadCountries() {
      this.loading.countries = true
      try {
        this.lists.countries = await ZoneService.getCountries()
      } finally {
        this.loading.countries = false
      }
    },

    syncFromContext() {
      const path = useContextStore().current_path
      if (!path) return

      const parts = path.split('/').filter(Boolean)

      this.selected.country = parts[0] || null
      this.selected.state = parts[1] || null
      this.selected.city = parts[2] || null
      this.selected.neighborhood = parts[3] || null
    },

    async selectCountry(country) {
      this.selected.country = country
      this.selected.state = null
      this.selected.city = null
      this.selected.neighborhood = null

      this.lists.states = []
      this.lists.cities = []
      this.lists.neighborhoods = []

      await this.loadStates(country)
    },

    async selectState(state) {
      this.selected.state = state
      this.selected.city = null
      this.selected.neighborhood = null

      this.lists.cities = []
      this.lists.neighborhoods = []

      await this.loadCities(this.selected.country, state)
    },

    async selectCity(city) {
      this.selected.city = city
      this.selected.neighborhood = null

      this.lists.neighborhoods = []

      await this.loadNeighborhoods(city)
    },

    selectNeighborhood(neighborhood) {
      this.selected.neighborhood = neighborhood
      this.emitPath(this.buildPath())
    },

    async loadStates(country) {
      this.loading.states = true
      try {
        this.lists.states = await ZoneService.getStates(country)
      } finally {
        this.loading.states = false
      }
    },

    async loadCities(country, state) {
      this.loading.cities = true
      try {
        this.lists.cities = await ZoneService.getCities(country, state)
      } finally {
        this.loading.cities = false
      }
    },

    async loadNeighborhoods(city) {
      this.loading.neighborhoods = true
      try {
        this.lists.neighborhoods = await ZoneService.getNeighborhoods(city)
      } finally {
        this.loading.neighborhoods = false
      }
    },

    buildPath() {
      const s = this.selected
      return `/${s.country}/${s.state}/${s.city}/${s.neighborhood}`
    },

    emitPath(path) {
      const context = useContextStore()
      context.setPath(path)
    }
  }
})