/**
 * ════════════════════════════════════════════════════════════════
 * 🏛️ KUBIX — Territory Service (MONITOR LAYER)
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Consultas analíticas y geográficas de la red.
 * FILTRADO: Basado estrictamente en Materialized Path.
 */

import { api } from '@/Kubix/core/api'

const TerritoryService = {

  /**
   * Obtiene la data consolidada para el monitor (Dashboard Admin)
   * @param {string} path - El path jerárquico (ej: /1/1/1/)
   */
  async getMonitor(path) {
    try {
      // Enviamos el path como query param o en el cuerpo según tu API
      const { data } = await api.get('/admin/territory/monitor', {
        params: { path }
      });

      /**
       * Esperamos: 
       * { 
       * meta: { country, state, city, neighborhood }, 
       * metrics: { users, businesses, active_ads, revenue },
       * children: [...] 
       * }
       */
      return data.data;
    } catch (error) {
      console.error(`Error en monitor territorial para ${path}:`, error);
      throw error;
    }
  },

  /**
   * Obtiene la capa geográfica (GeoJSON) para pintar en el mapa
   * Útil para resaltar el área que se está monitoreando.
   */
  async getGeoData(path) {
    try {
      const { data } = await api.get('/admin/territory/geojson', {
        params: { path }
      });
      return data.geojson; 
    } catch (error) {
      console.error("Error cargando GeoJSON territorial:", error);
      return null;
    }
  },

  /**
   * Obtiene un ranking rápido de los "hijos" más rentables del path
   * (Opcional, si el monitor principal no lo trae todo)
   */
  async getTopPerformers(path) {
    try {
      const { data } = await api.get('/admin/territory/ranking', {
        params: { path, limit: 5 }
      });
      return data.data;
    } catch (error) {
      return [];
    }
  }
};

export default TerritoryService;