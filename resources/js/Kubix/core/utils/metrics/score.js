/**
 * ============================================
 * Weighted Score Engine (KUBIX CORE)
 * ============================================
 *
 * Responsabilidad:
 * - Calcular una puntuación ponderada basada en múltiples métricas numéricas.
 * - Recibir arrays de valores y pesos alineados por índice.
 * - Generar un score final normalizado (0–100 aprox).
 *
 * Características:
 * - Lógica pura (sin contexto de negocio).
 * - Reutilizable en cualquier dominio (empresa, admin, zonas, etc).
 * - Independiente de UI, backend o framework.
 *
 * Nota:
 * - Se espera que los valores de entrada estén previamente normalizados
 *   para evitar sesgos entre métricas de diferente escala.
 */

export function weightedScore(values, weights) {
  if (values.length !== weights.length) {
    throw new Error('Values and weights must match')
  }

  const totalWeight = weights.reduce((a, b) => a + b, 0)

  const score = values.reduce((acc, val, i) => {
    return acc + val * weights[i]
  }, 0)

  return Math.round(score / totalWeight)
}