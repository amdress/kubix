<?php
namespace App\Integrations\Locations\Nominatim;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class NominatimService
 *
 * @responsibility
 * Encapsular toda la comunicación con la API de Nominatim (OpenStreetMap).
 *
 * @principles
 * - Single Responsibility: solo HTTP hacia Nominatim
 * - DRY: centraliza llamadas en método base `search`
 * - Resiliencia: manejo consistente de errores
 *
 * @notes
 * Nominatim requiere un User-Agent válido (incluyendo email).
 */
class NominatimService
{
    public function __construct(
        protected string $baseUrl,
        protected string $userAgent,
        protected int $timeout = 15
    ) {}

    /**
     * Método base para consultas tipo SEARCH.
     *
     * @param string $query Texto libre (ej: "Batel Curitiba")
     * @param array $extra Parámetros adicionales de la API
     * @return array|null
     */
    protected function search(string $query, array $extra = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept'     => 'application/json',
            ])
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/search", array_merge([
                    'q' => $query,
                ], $extra));

            if (! $response->successful()) {
                Log::error('NominatimService@search API error', [
                    'status' => $response->status(),
                    'query'  => $query,
                ]);
                return null;
            }

            return $response->json();

        } catch (\Throwable $e) {
            Log::critical('NominatimService@search connection fail', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

/**
     * Obtener geometría (GeoJSON boundary) de una ubicación por nombre.
     * Usado principalmente para niveles "padre" (País, Estado).
     */
    public function getBoundary(string $query): ?array
    {
        // Llamamos a search (que es protected, pero estamos dentro de la clase, así que funciona)
        $data = $this->search($query, [
            'format'          => 'jsonv2', // Usamos jsonv2 para estructura consistente
            'polygon_geojson' => 1,
            'limit'           => 1,
        ]);

        $result = $data[0] ?? null;

        if (!$result) return null;

        return [
            'lat'      => (float) $result['lat'],
            'lon'      => (float) $result['lon'],
            'geometry' => $result['geojson'] ?? null, // GeoJSON crudo
        ];
    }

    /**
     * Obtener coordenadas (lat/lon) de una ubicación.
     *
     * @param string $query
     * @return array|null ['latitude' => float, 'longitude' => float]
     */
    public function getCoordinates(string $query): ?array
    {
        $data = $this->search($query, [
            'format' => 'json',
            'limit'  => 1,
        ]);

        $result = $data[0] ?? null;

        if (! $result || ! isset($result['lat'], $result['lon'])) {
            return null;
        }

        return [
            'latitude'  => (float) $result['lat'],
            'longitude' => (float) $result['lon'],
        ];
    }

    /**
     * Reverse geocoding (coordenadas → dirección).
     *
     * @param float $lat
     * @param float $lon
     * @return array|null
     */
    public function reverseGeocode(float $lat, float $lon): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => $this->userAgent,
                'Accept'     => 'application/json',
            ])
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/reverse", [
                    'lat'            => $lat,
                    'lon'            => $lon,
                    'format'         => 'jsonv2',
                    'addressdetails' => 1,
                ]);

            if (! $response->successful()) {
                Log::error('NominatimService@reverseGeocode API error', [
                    'status' => $response->status(),
                    'lat'    => $lat,
                    'lon'    => $lon,
                ]);
                return null;
            }

            $data    = $response->json();
            $address = $data['address'] ?? [];

            return [
                'neighborhood' => $address['suburb'] ?? $address['neighbourhood'] ?? $address['hamlet'] ?? null,
                'city'         => $address['city'] ?? $address['town'] ?? $address['municipality'] ?? null,
                'state'        => $address['state'] ?? null,
                'country'      => $address['country'] ?? null,
                'display_name' => $data['display_name'] ?? null,
            ];

        } catch (\Throwable $e) {
            Log::critical('NominatimService@reverseGeocode fail', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 🔥 AUTOCOMPLETE / SUGERENCIAS DE UBICACIÓN CON DETECCIÓN AUTOMÁTICA
     *
     * @description
     * Permite a un administrador escribir texto libre (ej: "Batel")
     * y obtiene múltiples coincidencias detectando automáticamente si es
     * barrio, ciudad, estado o país basándose en class/type de Nominatim.
     *
     * @param string $query
     * @param int $limit
     * @return array Lista normalizada con tipo detectado automáticamente
     */
    public function suggest(string $query, int $limit = 5): array
    {
        try {
            $data = $this->search($query, [
                'format'         => 'json',
                'addressdetails' => 1,
                'limit'          => $limit,
            ]);

            if (!is_array($data) || empty($data)) {
                Log::info('NominatimService@suggest: Empty results', ['query' => $query]);
                return [];
            }

            $results = collect($data)
                ->filter(function ($item) {
                    $class = $item['class'] ?? null;
                    $type  = $item['type'] ?? null;

                    if ($class === 'place' && in_array($type, ['city', 'town', 'village', 'suburb', 'neighbourhood'])) {
                        return true;
                    }

                    if ($class === 'boundary' && $type === 'administrative') {
                        return true;
                    }

                    return false;
                })
                ->map(function ($item) {
                    $address = $item['address'] ?? [];
                    $territoryType = $this->detectTerritoryType($item);

                    return [
                        'display_name'   => $item['display_name'] ?? null,
                        'latitude'       => isset($item['lat']) ? (float) $item['lat'] : null,
                        'longitude'      => isset($item['lon']) ? (float) $item['lon'] : null,
                        'territory_type' => $territoryType,
                        'neighborhood'   => $address['suburb'] ?? $address['neighbourhood'] ?? $address['hamlet'] ?? null,
                        'city'           => $address['city'] ?? $address['town'] ?? $address['municipality'] ?? null,
                        'state'          => $address['state'] ?? null,
                        'country'        => $address['country'] ?? null,
                        'type'           => $item['type'] ?? null,
                        'class'          => $item['class'] ?? null,
                        'address_rank'   => $item['address_rank'] ?? null,
                    ];
                })
                ->filter(function ($item) {
                    return !empty($item['display_name']) && $item['latitude'] !== null && $item['longitude'] !== null;
                })
                ->sortBy(function ($item) {
                    return match ($item['territory_type']) {
                        'neighborhood' => 1,
                        'city'         => 2,
                        'state'        => 3,
                        'country'      => 4,
                        default        => 10,
                    };
                })
                ->values()
                ->toArray();

            Log::info('NominatimService@suggest: Success Response', [
                'query'         => $query,
                'results_count' => count($results),
                'types'         => collect($results)->pluck('territory_type')->unique()->values()->toArray(),
            ]);

            return $results;

        } catch (\Throwable $e) {
            Log::error('NominatimService@suggest: Fail', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Detecta automáticamente el tipo de territorio.
     * Usa class/type de Nominatim para identificar con precisión.
     */
    private function detectTerritoryType(array $item): string
    {
        $class = $item['class'] ?? null;
        $type = $item['type'] ?? null;
        $address = $item['address'] ?? [];

        // Estrategia 1: class === 'place'
        if ($class === 'place') {
            return match ($type) {
                'neighbourhood', 'suburb', 'hamlet' => 'neighborhood',
                'city', 'town', 'municipality' => 'city',
                'village' => 'neighborhood',
                default => 'city',
            };
        }

        // Estrategia 2: class === 'boundary' && type === 'administrative'
        if ($class === 'boundary' && $type === 'administrative') {
            // Si tiene suburb/neighbourhood, es un barrio
            if (isset($address['suburb']) || isset($address['neighbourhood'])) {
                return 'neighborhood';
            }
            
            // Si tiene city/town/municipality, es una ciudad
            if (isset($address['city']) || isset($address['town']) || isset($address['municipality'])) {
                return 'city';
            }
            
            // Si tiene state pero no city, es un estado
            if (isset($address['state'])) {
                return 'state';
            }
            
            // Si solo tiene country, es país
            if (isset($address['country'])) {
                return 'country';
            }

            return 'state'; // Default fallback
        }

        return 'city'; // Default final
    }

    /**
     * Obtiene el boundary (polígono) basado en coordenadas exactas.
     * Ideal para evitar la ambigüedad de nombres duplicados.
     * * @param float $lat
     * @param float $lon
     * @param string $type Tipo de territorio para ajustar el zoom (level)
     * @return array|null ['lat', 'lon', 'geometry']
     */
    public function getBoundaryByCoords(float $lat, float $lon, string $type = 'city'): ?array
    {
        // Ajustamos el zoom según el tipo para que Nominatim entienda qué polígono administrativo buscar
        $zoom = match ($type) {
            'country'      => 3,
            'state'        => 5,
            'city'         => 10,
            'neighborhood' => 18,
            default        => 10,
        };

        try {
            $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                ->timeout($this->timeout)
                ->get("{$this->baseUrl}/reverse", [
                    'lat'             => $lat,
                    'lon'             => $lon,
                    'format'          => 'jsonv2',
                    'polygon_geojson' => 1, // <--- CRÍTICO: Esto trae el polígono
                    'zoom'            => $zoom,
                ]);

            if (!$response->successful()) return null;

            $data = $response->json();

            return [
                'lat'      => (float) ($data['lat'] ?? $lat),
                'lon'      => (float) ($data['lon'] ?? $lon),
                'geometry' => $data['geojson'] ?? null, // GeoJSON crudo para el Repository
            ];

        } catch (\Throwable $e) {
            Log::error("NominatimService@getBoundaryByCoords Fail", ['error' => $e->getMessage()]);
            return null;
        }
    }
}