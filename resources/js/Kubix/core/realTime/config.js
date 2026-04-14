/**
 * KUBIX CORE - Realtime Configuration
 * --------------------------------------------------------------------------
 * Gestiona las variables de entorno para el servidor de WebSockets (Reverb).
 * Centraliza puertos, hosts y protocolos para evitar hardcoding en el engine.
 */

export const realtimeConfig = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    
    /**
     * RUTA DE AUTORIZACIÓN
     * Laravel por defecto usa /broadcasting/auth para validar canales privados.
     */
    authEndpoint: '/broadcasting/auth', 
};