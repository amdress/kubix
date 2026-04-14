/**
 * ════════════════════════════════════════════════════════════════
 * 🛠️ KUBIX — WorkSpace Service
 * ════════════════════════════════════════════════════════════════
 * RESPONSABILIDAD: Comunicación con la API para la gestión comercial.
 * UBICACIÓN: /Kubix/core/services/WorkSpaceService.js
 */

import { api } from '@/Kubix/core/api' // Tu instancia de Axios configurada

const WorkSpaceService = {

  /**
   * Carga el resumen inicial del dueño (Empresas + Wallet)
   */
  async getOwnerDashboard() {
    try {
      const { data } = await api.get('/workspace/dashboard');
      return data.data; // { companies: [], wallet: {} }
    } catch (error) {
      console.error("Error cargando dashboard de negocio:", error);
      throw error;
    }
  },

  /**
   * Obtiene el detalle operativo de una unidad específica
   */
  async getUnitDetail(unitId) {
    try {
      const { data } = await api.get(`/workspace/units/${unitId}/control`);
      return data.data; // { activations, modules, appearance, status }
    } catch (error) {
      console.error(`Error cargando detalle de unidad ${unitId}:`, error);
      throw error;
    }
  },

  /**
   * Obtiene métricas de impacto social para una unidad
   */
  async getUnitStats(unitId) {
    try {
      const { data } = await api.get(`/workspace/units/${unitId}/stats`);
      return data.data; // analytics object
    } catch (error) {
      console.error("Error cargando métricas:", error);
      return this.getDefaultStats();
    }
  },

  /**
   * Carga el inbox de leads y alertas de la unidad
   */
  async getBusinessInbox(unitId) {
    try {
      const { data } = await api.get(`/workspace/units/${unitId}/inbox`);
      return data.data; // { leads, applications, system_alerts }
    } catch (error) {
      console.error("Error cargando inbox:", error);
      return { leads: [], applications: [], system_alerts: [] };
    }
  },

  /**
   * 📣 PUBLICAR ACTIVACIÓN (Ads, Jobs, Events)
   * Integra los paths seleccionados en el ZoneStore
   */
  async publish({ unitId, type, payload, target_paths }) {
    try {
      const { data } = await api.post(`/workspace/units/${unitId}/publish`, {
        type,      // 'ad', 'job', 'event'
        payload,   // Contenido de la publicación
        paths: target_paths // Los barrios donde impactará
      });
      return data; // { success: true, data: { activation }, new_balance: 75.0 }
    } catch (error) {
      console.error("Error en publicación social:", error);
      return { success: false, error: error.response?.data?.message };
    }
  },

  /**
   * 🛒 ALQUILAR MÓDULO (Marketplace)
   */
  async activateModule(unitId, moduleId) {
    try {
      const { data } = await api.post(`/workspace/units/${unitId}/modules`, {
        module_id: moduleId
      });
      return data; // { success: true, module: {} }
    } catch (error) {
      console.error("Error activando módulo:", error);
      return { success: false };
    }
  },

  /**
   * 🥩 RADAR B2B: Busca proveedores por territorio (path)
   */
  async findSuppliersByPath(path) {
    try {
      const { data } = await api.get('/workspace/radar/suppliers', {
        params: { path }
      });
      return data.data; // Array de proveedores
    } catch (error) {
      console.error("Error en Radar B2B:", error);
      return [];
    }
  },

  /**
   * 💵 GENERAR COBRO PIX (Recarga Wallet)
   */
  async generatePixCharge(amount) {
    try {
      const { data } = await api.post('/workspace/wallet/topup', { amount });
      return data.data; // { pix_code, qr_image, transaction_id }
    } catch (error) {
      console.error("Error generando Pix:", error);
      throw error;
    }
  },

  /**
   * Fallback de métricas vacías
   */
  getDefaultStats() {
    return {
      views: { total: 0, history: [] },
      likes: 0,
      saves: 0,
      qr_scans: 0,
      heat_map: [],
      top_actions: []
    };
  }
};

export default WorkSpaceService;