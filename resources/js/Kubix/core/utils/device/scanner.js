/**
 * ════════════════════════════════════════════════════════════════
 * 📷 KUBIX — QR Scanner Engine (CORE DEVICE LAYER)
 * ════════════════════════════════════════════════════════════════
 *
 * RESPONSABILIDAD:
 * - Gestionar el ciclo de vida del escáner QR (cámara)
 * - Inicializar, ejecutar y detener hardware de forma segura
 * - Prevenir múltiples lecturas (lock interno)
 * - Exponer estado reactivo para UI
 *
 * FLUJO:
 * start() → inicia cámara
 * callback → emite QR detectado
 * stop() → libera hardware
 *
 * REQUISITOS:
 * - HTTPS o localhost
 * - permisos de cámara activos
 *
 * DEPENDENCIAS:
 * - html5-qrcode
 * - Vue 3 (ref)
 *
 * UBICACIÓN:
 * /Kubix/core/utils/device/useScanner.js
 *
 * NOTAS:
 * - No contiene lógica de negocio
 * - No interpreta QR (solo captura)
 * - Debe usarse dentro de overlays o layouts
 *
 * ════════════════════════════════════════════════════════════════
 */

import { ref } from "vue";
import { Html5Qrcode } from "html5-qrcode";

export function useScanner() {
  const isRunning = ref(false);
  const error = ref(null);

  let scanner = null;
  let locked = false;

  /**
   * 📷 START SCANNER
   */
  const start = async (elementId, onResult) => {
    try {
      // Evita doble inicio
      if (isRunning.value) return;

      // Limpieza previa si existe instancia colgada
      if (scanner) await stop();

      error.value = null;
      locked = false;

      scanner = new Html5Qrcode(elementId);

      await scanner.start(
        { facingMode: "environment" },
        {
          fps: 15,
          qrbox: { width: 260, height: 260 },
          aspectRatio: 1
        },
        async (text) => {
          // Anti multi-scan
          if (locked) return;
          locked = true;

          // Emite resultado inmediatamente
          onResult(text);

          // Detiene scanner después del primer scan válido
          await stop();
        },
        () => {
          // Silent frame errors (evita spam en consola)
        }
      );

      isRunning.value = true;

    } catch (e) {
      error.value = e?.message || "Error al acceder a la cámara";
      console.error("[KUBIX Scanner:start]", e);
    }
  };

  /**
   * 🛑 STOP SCANNER
   */
  const stop = async () => {
    try {
      if (scanner) {
        if (isRunning.value) {
          await scanner.stop();
        }
        await scanner.clear();
      }
    } catch (e) {
      console.warn("[KUBIX Scanner:stop]", e);
    } finally {
      scanner = null;
      isRunning.value = false;
      locked = false;
    }
  };

  return {
    isRunning,
    error,
    start,
    stop
  };
}