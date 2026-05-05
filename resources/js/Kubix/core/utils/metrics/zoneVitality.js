/**
 * ============================================
 * Zone Vitality Aggregator (KUBIX CORE)
 * ============================================
 *
 * Responsabilidad:
 * - Calcular la vitalidad general de una zona dentro de la plataforma.
 * - Consolidar múltiples indicadores del ecosistema en un único score.
 *
 * Métricas consideradas:
 * - ads        → actividad comercial en la zona
 * - users      → presencia y crecimiento de usuarios
 * - events     → dinamismo social / engagement
 * - companies  → densidad empresarial activa
 *
 * Estrategia:
 * - Utiliza el motor genérico weightedScore.
 * - Aplica ponderación uniforme (puede ajustarse según negocio).
 *
 * Nota:
 * - Representa la “salud” de una zona en tiempo real.
 * - Los valores deben estar previamente normalizados (0–100).
 */

import { weightedScore } from './score'

export function calculateZoneVitality(data) {
  return weightedScore(
    [data.ads, data.users, data.events, data.companies],
    [0.25, 0.25, 0.25, 0.25]
  )
}