<?php

namespace App\Models\Traits;

use App\Integrations\Locations\Nominatim\NominatimService;
use League\ISO3166\ISO3166;
use Illuminate\Support\Facades\Log;

/**
 * Trait HasDynamicGeometry
 * Encargado de la lógica de optimización y comunicación con el Service.
 */
trait HasDynamicGeometry
{
    /**
     * Obtiene el polígono (GeoJSON) simplificado.
     * Los parámetros son opcionales para que buildQuery sea flexible.
     */
    public function fetchBoundary(
        string  $country,
        ?string $state        = null,
        ?string $city         = null,
        ?string $neighborhood = null,
        float   $tolerance    = 0.0001
    ): ?array {
        
        // 1. Construimos la cadena de búsqueda (ej: "Batel, Curitiba, Parana, Brasil")
        $query = $this->buildQuery($country, $state, $city, $neighborhood);

        if (!$query) {
            Log::warning('HasDynamicGeometry: No se pudo construir la query.');
            return null;
        }

        // 2. LLAMADA AL SERVICE (El que configuramos en el Provider)
        // Usamos app() para que Laravel inyecte las credenciales del config/services.php
        $service = app(NominatimService::class);
        $feature = $service->getBoundary($query);

        if (!$feature || empty($feature['geometry'])) {
            return null;
        }

        $geometry = $feature['geometry'];

        // 3. OPTIMIZACIÓN: Si es un polígono, lo simplificamos para no saturar la DB
        if ($tolerance > 0 && isset($geometry['coordinates'])) {
            $geometry = $this->simplifyGeometry($geometry, $tolerance);
        }

        return $this->isValidGeometry($geometry) ? $geometry : null;
    }

    /**
     * Obtiene coordenadas (Lat/Lon) simples.
     */
    public function fetchCoordinates(
        string  $country,
        ?string $state        = null,
        ?string $city         = null,
        ?string $neighborhood = null
    ): ?array {
        $query = $this->buildQuery($country, $state, $city, $neighborhood);
        
        if (!$query) return null;

        return app(NominatimService::class)->getCoordinates($query);
    }

    // =========================================================================
    // LÓGICA INTERNA (Simplificación y Query)
    // =========================================================================

    private function buildQuery(string $country, ?string $state, ?string $city, ?string $neighborhood): ?string
    {
        // Ordenamos de lo más específico a lo más general para Nominatim
        $parts = array_filter([
            $neighborhood,
            $city,
            $state,
            $this->resolveCountryName($country),
        ], fn ($v) => !empty(trim((string) $v)));

        return empty($parts) ? null : implode(', ', $parts);
    }

    private function resolveCountryName(string $value): string
    {
        $value = trim($value);
        if (empty($value)) return '';
        
        $iso = new ISO3166;
        try {
            if (strlen($value) === 3) return $iso->alpha3(strtoupper($value))['name'];
            if (strlen($value) === 2) return $iso->alpha2(strtoupper($value))['name'];
        } catch (\Exception $e) {
            // Si falla o no es ISO, devolvemos el string original (ej: "Brasil")
        }
        return $value;
    }

    /**
     * Simplificación de geometría (Visvalingam-Whyatt básico)
     */
    private function simplifyGeometry(array $geometry, float $tolerance): array
    {
        $type = $geometry['type'] ?? null;

        $geometry['coordinates'] = match ($type) {
            'Polygon' => array_map(fn ($ring) => $this->simplifyRing($ring, $tolerance), $geometry['coordinates']),
            'MultiPolygon' => array_map(
                fn ($polygon) => array_map(fn ($ring) => $this->simplifyRing($ring, $tolerance), $polygon),
                $geometry['coordinates']
            ),
            default => $geometry['coordinates'],
        };

        return $geometry;
    }

    private function simplifyRing(array $ring, float $tolerance): array
    {
        if (count($ring) <= 4) return $ring;
        
        $result = [$ring[0]];
        for ($i = 1; $i < count($ring) - 1; $i++) {
            $area = $this->triangleArea($ring[$i-1], $ring[$i], $ring[$i+1]);
            if ($area > $tolerance) {
                $result[] = $ring[$i];
            }
        }
        $result[] = end($ring);
        return $result;
    }

    private function triangleArea(array $p0, array $p1, array $p2): float
    {
        return abs(($p0[0] * ($p1[1] - $p2[1]) + $p1[0] * ($p2[1] - $p0[1]) + $p2[0] * ($p0[1] - $p1[1])) / 2);
    }

    private function isValidGeometry(array $geometry): bool
    {
        return isset($geometry['type'], $geometry['coordinates']) && 
               in_array($geometry['type'], ['Polygon', 'MultiPolygon']);
    }
}