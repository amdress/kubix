<?php

namespace App\Integrations\Locations\Nominatim;

use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

/**
 * Class NominatimServiceProvider
 *
 * @responsibility
 * Registrar el servicio de Nominatim dentro del contenedor de Laravel.
 *
 * @details
 * - Inyecta configuración desde config/services.php
 * - Garantiza que los valores críticos existan (fail-fast)
 * - Expone una única instancia (singleton)
 *
 * @usage
 * Automático vía Laravel (registrado en config/app.php o auto-discovery)
 */
class NominatimServiceProvider extends ServiceProvider
{
    /**
     * Registrar bindings en el contenedor.
     */
    public function register(): void
    {
        $this->app->singleton(NominatimService::class, function () {

            $config = config('services.nominatim');

            // 🔥 Validación crítica (evita errores silenciosos en producción)
            if (empty($config['url'])) {
                throw new InvalidArgumentException('Nominatim URL not configured.');
            }

            if (empty($config['user_agent'])) {
                throw new InvalidArgumentException('Nominatim User-Agent is required.');
            }

            return new NominatimService(
                baseUrl:   $config['url'],
                userAgent: $config['user_agent'],
                timeout:   $config['timeout'] ?? 15
            );
        });
    }
}