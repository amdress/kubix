/**
 * ════════════════════════════════════════════════════════════════
 * KUBIX — Location Manager (CORE UTILS)
 * ════════════════════════════════════════════════════════════════
 * * @responsibility:
 * - Interfaz única para la obtención de ubicación (GPS / IP).
 * - Observación reactiva del movimiento del usuario (Watch Mode).
 * - Filtrado de ruido por distancia (Haversine) para optimizar API.
 * - Proveer el "Apretón de Manos" inicial para el Territory Service.
 *
 * @logic:
 * 1. Prioriza GPS de alta precisión.
 * 2. Fallback a IP en caso de timeout o denegación de permisos.
 * 3. Notifica cambios significativos (> 150m) a los suscriptores.
 * * ════════════════════════════════════════════════════════════════
 */

const GPS_TIMEOUT = 10000;
const FETCH_TIMEOUT = 5000;
const MIN_DISTANCE_CHANGE = 150; // Metros mínimos para disparar re-check en el Back

class LocationManager {
    /** @type {number|null} ID del observador nativo de geolocalización */
    static watchId = null;

    /** @type {Object|null} Última coordenada válida emitida */
    static lastEmittedCoords = null;

    /**
     * EL APRETÓN DE MANOS INICIAL (Single Fetch)
     * Intenta obtener la posición actual una sola vez.
     * * @returns {Promise<Object>} { lat, lon, accuracy, source }
     */
    static async getRawCoords() {
        try {
            return await this.requestGPS();
        } catch (err) {
            console.warn("📍 [LocationManager] GPS falló o timeout, intentando IP...", err.message);
            return await this.fetchIPLocation();
        }
    }

    /**
     * INICIAR OBSERVACIÓN (Watch Mode)
     * Activa el radar activo. Ideal para el ciclo de vida del TerritoryStore.
     * * @param {Function} onMoveCallback - Ejecutado solo si hay movimiento significativo.
     */
    static startWatching(onMoveCallback) {
        if (this.watchId) {
            console.warn("📍 [LocationManager] El observador ya está activo.");
            return;
        }

        if (!navigator.geolocation) {
            console.error("📍 [LocationManager] Geolocation no soportada en este entorno.");
            return;
        }

        this.watchId = navigator.geolocation.watchPosition(
            (pos) => {
                const current = {
                    lat: pos.coords.latitude,
                    lon: pos.coords.longitude,
                    accuracy: pos.coords.accuracy,
                    source: 'gps'
                };

                // Si es la primera vez o si el movimiento supera el umbral
                if (!this.lastEmittedCoords || this.shouldEmitUpdate(current)) {
                    this.lastEmittedCoords = current;
                    console.log("%c📍 [LocationManager] Movimiento detectado, notificando...", "color: #10b981");
                    onMoveCallback(current);
                }
            },
            (err) => {
                console.error(`📍 [LocationManager] Watch Error (${err.code}): ${err.message}`);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 30000, // Caché de 30 segundos
                timeout: GPS_TIMEOUT
            }
        );
    }

    /**
     * DETENER OBSERVACIÓN
     * Limpia el proceso de fondo para ahorrar batería/recursos.
     */
    static stopWatching() {
        if (this.watchId !== null) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
            this.lastEmittedCoords = null;
            console.log("📍 [LocationManager] Observador desactivado.");
        }
    }

    /**
     * SOLICITUD AL HARDWARE (GPS)
     * @private
     */
    static requestGPS(options = {}) {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) return reject(new Error('No Geolocation Support'));

            navigator.geolocation.getCurrentPosition(
                (pos) => resolve({
                    lat: pos.coords.latitude,
                    lon: pos.coords.longitude,
                    accuracy: pos.coords.accuracy,
                    source: 'gps'
                }),
                (err) => reject(err),
                {
                    enableHighAccuracy: true,
                    timeout: GPS_TIMEOUT,
                    maximumAge: 0,
                    ...options,
                }
            );
        });
    }

    /**
     * FALLBACK POR IP (Basado en Servicio Externo)
     * @private
     */
    static async fetchIPLocation() {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), FETCH_TIMEOUT);

        try {
            const res = await fetch('https://ipapi.co/json/', { signal: controller.signal });
            const data = await res.json();
            
            return {
                lat: data.latitude,
                lon: data.longitude,
                source: 'ip'
            };
        } catch (error) {
            throw new Error("Imposible obtener ubicación por IP");
        } finally {
            clearTimeout(timeoutId);
        }
    }

    /**
     * LÓGICA DE FILTRADO (Haversine)
     * Determina si el cambio de posición justifica un nuevo llamado al backend.
     * * @param {Object} current - Nuevas coordenadas recibidas.
     * @returns {boolean}
     */
    static shouldEmitUpdate(current) {
        if (!this.lastEmittedCoords) return true;

        const distance = this.calculateDistance(
            this.lastEmittedCoords.lat,
            this.lastEmittedCoords.lon,
            current.lat,
            current.lon
        );

        return distance >= MIN_DISTANCE_CHANGE;
    }

    /**
     * FÓRMULA DE HAVERSINE
     * Calcula distancia en metros entre dos puntos.
     */
    static calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Radio de la Tierra en metros
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                  Math.cos(φ1) * Math.cos(φ2) *
                  Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; 
    }
}

export { LocationManager };