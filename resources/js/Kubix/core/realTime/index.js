/**
 * KUBIX CORE - Realtime Engine (Singleton)
 * --------------------------------------------------------------------------
 * Inicializa y provee la instancia única de Laravel Echo.
 * Permite que Orquestadores y Stores se suscriban a canales (Chat, GPS, Pagos)
 * compartiendo una sola conexión TCP.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { realtimeConfig } from './config';

// Requerido por el driver de Reverb/Pusher
window.Pusher = Pusher;

let echoInstance = null;

/**
 * Inicializa el motor de tiempo real.
 * @param {string|null} token - JWT opcional para canales privados.
 * @returns {Echo} Instancia única de Laravel Echo.
 */
export const initRealtime = (token = null) => {
    if (echoInstance) return echoInstance;

    echoInstance = new Echo({
        ...realtimeConfig,
        auth: {
            headers: {
                Authorization: token ? `Bearer ${token}` : '',
                Accept: 'application/json',
            }
        }
    });

    console.log("KUBIX-CORE: Realtime Engine Operacional.");
    return echoInstance;
};

/**
 * Hook para obtener la instancia activa desde cualquier parte del sistema.
 * @returns {Echo|null}
 */
export const useRealtime = () => {
    if (!echoInstance) {
        console.warn("KUBIX-CORE: Intento de acceso a Realtime antes de inicialización.");
    }
    return echoInstance;
};