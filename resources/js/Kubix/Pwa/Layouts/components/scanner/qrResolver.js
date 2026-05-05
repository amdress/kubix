/**
 * ════════════════════════════════════════════════════════════════
 * 🔍 KUBIX — QR Resolver (INTERPRETATION LAYER)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Interpretar códigos QR del ecosistema KUBIX
 * - Detectar formato KX (navegación interna)
 * - Mantener compatibilidad con formatos legacy
 * - Normalizar salida para el QR Router Engine
 *
 * NO HACE:
 * - No realiza peticiones HTTP
 * - No ejecuta navegación directa
 * - No contiene lógica de negocio
 *
 * FORMATO OFICIAL (KX PROTOCOL):
 * - kx://company/company123
 * - kx://event/event456
 * - kx://workspace/branch789
 *
 * SALIDA:
 * - Objeto estructurado { type, domain, id, raw? }
 *
 * UBICACIÓN:
 * /Kubix/Pwa/Layouts/components/scanner/qrResolver.js
 *
 * ════════════════════════════════════════════════════════════════
 */

/**
 * 🔍 Resolver principal
 */
export async function resolveQR(raw) {
  if (!raw) throw new Error("QR vacío")

  const clean = raw.trim()

  /**
   * 🔗 KX PROTOCOL (ESTÁNDAR OFICIAL KUBIX)
   */
  if (clean.startsWith("kx://")) {
    const [_, path] = clean.split("kx://")
    const [domain, id] = path.split("/")

    return {
      type: domain,
      domain,
      id,
      raw: clean
    }
  }

  /**
   * 📌 LEGACY ROUTES (/app/*)
   */
  if (clean.includes("/app/")) {
    return {
      type: "route",
      payload: clean
    }
  }

  /**
   * 📦 JSON PAYLOAD (FUTURO EXTENSIBLE)
   */
  try {
    const parsed = JSON.parse(clean)

    return {
      type: parsed.type || "unknown",
      payload: parsed
    }
  } catch (e) {
    // no es JSON
  }

  /**
   * 🧾 SIMPLE ENTITY ID (LEGACY SUPPORT)
   */
  if (/^[a-zA-Z0-9_-]+$/.test(clean)) {
    return {
      type: "entity",
      id: clean
    }
  }

  /**
   * ❌ RAW FALLBACK
   */
  return {
    type: "raw",
    payload: clean
  }
}