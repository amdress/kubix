/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Device Index
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * Punto central de exportación (Public API) para utilidades de detección 
 * de hardware y dimensiones del viewport.
 *
 * FILOSOFÍA:
 * Centralizar las herramientas de UI adaptativa para que el resto de
 * la aplicación no necesite conocer la implementación interna.
 *
 * USO:
 * import { isMobile, useDevice } from '@/Kubix/core/utils/device'
 * ════════════════════════════════════════════════════════════════
 */

export {
  isMobile,
  useDevice
} from './device'