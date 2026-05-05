/**
 * ============================================
 * Company Vitality Calculator (KUBIX CORE)
 * ============================================
 *
 * Responsabilidad:
 * - Calcular la vitalidad de una empresa dentro de una zona.
 * - Traducir métricas de interacción (clicks, views, WhatsApp)
 *   en un score único interpretable por UI (gauge).
 *
 * Métricas consideradas:
 * - clicks     → interés directo
 * - views      → visibilidad
 * - whatsapp   → intención de conversión
 *
 * Estrategia:
 * - Usa el motor genérico weightedScore.
 * - Aplica pesos definidos por negocio.
 *
 * Nota:
 * - Los valores deben estar normalizados (0–100).
 * - Este módulo define semántica, no matemática base.
 */

import { weightedScore } from './score'

export function calculateCompanyVitality(data) {
  return weightedScore(
    [data.clicks, data.views, data.whatsapp],
    [0.4, 0.4, 0.2]
  )
}