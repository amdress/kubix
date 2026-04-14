<?php
namespace App\Kubix\Domains\Geo\Territory\Management;

use App\Integrations\Locations\Nominatim\NominatimService;
use App\Kubix\Domains\Geo\Territory\DTO\CheckAvailabilityDto;
use App\Kubix\Domains\Organization\Branch\Management\BranchManagementService;
use App\Kubix\Domains\Geo\Territory\DTO\ResolveTerritorySelectionDto;
use App\Kubix\Domains\Geo\Territory\DTO\SuggestTerritoryDto;
use App\Kubix\Domains\Geo\Territory\TerritoryRepository;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class TerritoryService
 *
 * @responsibility
 * Orquestar la lógica de territorios:
 * - Resolución geográfica (Nominatim)
 * - Matching con territorios internos
 * - validación geométrica (boundary)
 * - Resolución de branding (Branch)
 */
class TerritoryManagementService
{
    public function __construct(
        protected TerritoryRepository $repository,
        protected NominatimService $nominatimService,
        protected BranchManagementService $branchService
    ) {}

    // ===================  Funciones Principales  ============================
/**
 * Lógica central para determinar la disponibilidad y el branding del Pre-login.
 * * Este método no devuelve una respuesta HTTP, sino el array de datos puro
 * para que el Controller lo procese.
 *
 * @param CheckAvailabilityDto $dto
 * @return array
 */
    public function checkAvailability(CheckAvailabilityDto $dto): array
    {
        try {
            Log::info("PASO 1: Inicio Check", ['lat' => $dto->latitude, 'lon' => $dto->longitude, 'path' => $dto->path]);

            $neighborhoodName = null;
            $cityName         = null;
            $territory        = null;

            // --- LÓGICA DE ENTRADA DUAL ---
            if ($dto->path) {
                // Si viene path (Selección Manual), buscamos el territorio directo
                $territory        = $this->repository->findByPath($dto->path);
                $neighborhoodName = $territory?->name;
                Log::debug("PASO 2: Entrada por Path", ['path' => $dto->path]);
            } elseif ($dto->latitude && $dto->longitude) {
                // Si vienen coordenadas, usamos Nominatim (como siempre)
                $geoData          = $this->nominatimService->reverseGeocode($dto->latitude, $dto->longitude);
                $neighborhoodName = $geoData['neighborhood'] ?? $geoData['suburb'] ?? null;
                $cityName         = $geoData['city'] ?? $geoData['town'] ?? null;

                $territory = $this->repository->findByCoordsAndName($neighborhoodName, $dto->latitude, $dto->longitude);
                Log::debug("PASO 2: Geo-Localización", ['barrio' => $neighborhoodName, 'ciudad' => $cityName]);
            }

            if (! $territory) {
                return $this->emptyResponse();
            }

            Log::info("PASO 3: Territorio Base Encontrado", ['id' => $territory->id, 'path' => $territory->path]);

            // --- LÓGICA DE BRANCHES (TU FLUJO ORIGINAL) ---
            $branchHijo    = $this->branchService->getBranchByPath($territory->path);
            $branchHermano = null;

            // Si el barrio está OFF, buscamos hermano (usando tu lógica de path /1/1/1/)
            if (! $branchHijo || ! $branchHijo->is_active) {
                Log::notice("PASO 4: Barrio OFF o sin Branch. Buscando Hermano.");
                $parts = explode('/', trim($territory->path, '/'));
                if (count($parts) >= 3) {
                    $parentPath    = '/' . $parts[0] . '/' . $parts[1] . '/' . $parts[2] . '/';
                    $branchHermano = $this->branchService->findNearestActive(
                        $dto->latitude ?? 0, // Fallback si es manual
                        $dto->longitude ?? 0,
                        $parentPath,
                        $territory->id
                    );
                }
            }

            // Buscamos siempre la Branch de la Ciudad (Padre) para la doble capa
            $branchPadre = $this->branchService->findCityBranchInPath($territory->path);

            // --- PREPARACIÓN DE LA RESPUESTA DE DOBLE CAPA ---
            // Decidimos qué branding es el "activo" para el contexto general
            $activeBranch = ($branchHijo && $branchHijo->is_active) ? $branchHijo : ($branchHermano ?? $branchPadre);

            return [
                'exists'      => (bool) $branchPadre,
                'match'       => ($branchHijo && $branchHijo->is_active) ? 'exact' : 'proximity',
                'context'     => [
                    'current_path' => $territory->path,
                    'mode'         => ($branchHijo && $branchHijo->is_active) ? 'local' : ($branchHermano ? 'near' : 'city'),
                ],
                'branding'    => [
                    'neighborhood' => [
                        'is_active'       => ($branchHijo && $branchHijo->is_active),
                        'name'            => $neighborhoodName,
                        'primary_color'   => $activeBranch?->branding['primary_color'] ?? '#3B82F6',
                        'welcome_message' => "Bem-vindo a " . ($neighborhoodName ?? 'sua região'),
                        'splash_image'    => $activeBranch?->branding['splash_image'] ?? 'default_splash.png',
                    ],
                    'city'         => [
                        'is_active'     => (bool) $branchPadre,
                        'name'          => $branchPadre?->name ?? $cityName ?? 'Curitiba',
                        'primary_color' => $branchPadre?->branding['primary_color'] ?? '#1E40AF',
                        'splash_image'  => $branchPadre?->branding['splash_image'] ?? 'city_splash.png',
                    ],
                ],
                'geo'         => [
                    'neighborhood' => $neighborhoodName,
                    'city'         => $branchPadre?->name ?? $cityName,
                    'sibling'      => $branchHermano?->name ?? null,
                ],
                'ads_context' => [
                    'zone_path' => $territory->path,
                ],
            ];

        } catch (\Throwable $e) {
            Log::error("ERROR CRÍTICO KUBIX", ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->emptyResponse();
        }
    }

/**
 * Motor de sugerencias para el autocompletado de territorios.
 * Procesa la entrada del usuario, consulta proveedores externos y
 * devuelve una estructura limpia lista para el Frontend.
 */
    public function findSuggests(SuggestTerritoryDto $dto): array
    {
        try {
            $query = $dto->queryNormalized();

            // Bloqueo de consultas irrelevantes (menos de 2 caracteres)
            if (empty($query) || mb_strlen($query) < 2) {
                return [
                    'query'   => $dto->query,
                    'results' => [],
                ];
            }

            // Llamada al motor de geocodificación (Nominatim)
            $rawSuggestions = $this->nominatimService->suggest($query);

            // Mapeo selectivo: solo extraemos lo que KUBIX necesita para su jerarquía
            $suggestions = array_map(fn($item) => [
                'display_name'   => $item['display_name'] ?? null,
                'territory_type' => $item['territory_type'] ?? null,
                'country'        => $item['country'] ?? null,
                'state'          => $item['state'] ?? null,
                'city'           => $item['city'] ?? null,
                'neighborhood'   => $item['neighborhood'] ?? null,
                'latitude'       => $item['latitude'] ?? null,
                'longitude'      => $item['longitude'] ?? null,
            ], $rawSuggestions);

            Log::info("function findSuggests: Found " . count($suggestions) . " results for '{$dto->query}'");

            return [
                'query'   => $dto->query,
                'results' => $suggestions,
            ];

        } catch (Throwable $e) {
            // Protección contra caídas de servicios externos o errores de red
            Log::error('function findSuggests: Error en el servicio de sugerencias', ['query' => $dto->query]);

            return [
                'query'   => $dto->query,
                'results' => [],
            ];
        }
    }

/**
 * Resuelve la selección de un territorio garantizando la integridad de la jerarquía.
 * * 1. Cortocircuito: Si el objetivo (Target) ya existe en DB, retorna de inmediato.
 * 2. Reconstrucción: Si es nuevo, recorre desde País hasta el Target.
 * 3. Precisión: Usa coordenadas para obtener el polígono (Boundary) sin ambigüedad.
 * 4. Automatización: Dispara el auto-seed de configuraciones de marca (Branch).
 * * @param ResolveTerritorySelectionDto $dto
 * @return array [existed: bool, territory: Territory]
 */
    public function resolveSelection(ResolveTerritorySelectionDto $dto): array
    {
        Log::info("KUBIX_DISCOVERY: Iniciando resolución jerárquica", [
            'target' => "{$dto->neighborhood}, {$dto->city}",
            'type' => $dto->territory_type,
        ]);

        // 1. Intentar localizar si el target ya existe para evitar duplicados
        $targetInfo = $this->resolveTarget($dto);
        if ($targetInfo && $targetInfo['exists']) {
            Log::info("KUBIX_DISCOVERY: Target ya existe en DB.", ['id' => $targetInfo['territory']->id]);
            return ['existed' => true, 'territory' => $targetInfo['territory']];
        }

        // 2. Definir niveles (Filtramos nulos para no romper el loop)
        $levels = array_filter([
            'country'      => $dto->country,
            'state'        => $dto->state,
            'city'         => $dto->city,
            'neighborhood' => $dto->neighborhood,
        ]);

        $currentParent   = null;
        $targetTerritory = null;

        foreach ($levels as $type => $name) {
            $isTarget = ($type === $dto->territory_type);

            // Buscamos si el nivel ya existe bajo su padre actual
            $territory = $this->repository->findStrict($name, $type, $currentParent?->id);

            if (! $territory) {
                Log::notice("KUBIX_DISCOVERY: Creando nivel [{$type}] -> {$name}");

                // ESTRATEGIA DE GEOMETRÍA:
                // Siempre intentamos buscar por NOMBRE + CONTEXTO para asegurar Polígono (GeoJSON)
                $searchQuery = $name . ($currentParent ? ", {$currentParent->name}" : "");
                $geoData     = $this->nominatimService->getBoundary($searchQuery);

                // FALLBACK PARA EL TARGET: Si por nombre no trajo geometría, usamos sus coordenadas GPS
                if ($isTarget && empty($geoData['geometry'])) {
                    Log::debug("KUBIX_DISCOVERY: Reintentando geometría por coordenadas para el target");
                    $geoData = $this->nominatimService->getBoundaryByCoords($dto->latitude, $dto->longitude, $type);
                }

                // PRIORIDAD DE COORDENADAS:
                // Si es el Target, mandan las coordenadas del DTO (las del usuario).
                // Si es un Padre, mandan las coordenadas oficiales de Nominatim.
                $finalLat = $isTarget ? $dto->latitude : ($geoData['lat'] ?? null);
                $finalLon = $isTarget ? $dto->longitude : ($geoData['lon'] ?? null);

                // CREACIÓN EN REPOSITORIO
                $territory = $this->repository->createFromDiscovery([
                    'type'      => $type,
                    'name'      => $name,
                    'parent_id' => $currentParent?->id,
                    'latitude'  => $finalLat,
                    'longitude' => $finalLon,
                    'boundary'  => $geoData['geometry'] ?? null, // Aquí es donde entra el Polígono
                ]);

                Log::info("KUBIX_DISCOVERY: Nivel creado con éxito", [
                    'type'     => $type,
                    'has_poly' => ! empty($geoData['geometry']),
                ]);

                // AUTO-SEED: Creamos la Branch y su configuración inicial (KUBIX Style)
                $this->branchService->autoSeed($territory);

            } else {
                Log::debug("KUBIX_DISCOVERY: Nivel saltado (ya existe)", ['type' => $type]);
            }

            $currentParent = $territory;

            if ($isTarget) {
                $targetTerritory = $territory;
                break; // No seguimos procesando niveles inferiores si ya llegamos al objetivo
            }
        }

        return ['existed' => false, 'territory' => $targetTerritory];
    }

//============================  FUNCIONES AUXILIARES  ===================================

/**
 * Identifica el objetivo final y verifica si ya existe en la base de datos.
 * Utiliza el contexto jerárquico (padres) para una búsqueda precisa.
 */
    private function resolveTarget(ResolveTerritorySelectionDto $dto): ?array
    {
        $type = $dto->territory_type;

        // 1. Extraer el nombre según el tipo
        $name = match ($type) {
            'country'      => $dto->country,
            'state'        => $dto->state,
            'city'         => $dto->city,
            'neighborhood' => $dto->neighborhood,
            default        => null,
        };

        if (! $name) {
            Log::warning("TerritoryService: No se pudo extraer el nombre para el tipo [{$type}]");
            return null;
        }

        /**
         * 2. BÚSQUEDA DE ALTA PRECISIÓN
         * Para evitar falsos positivos (ej. "Santiago" en Chile vs "Santiago" en España),
         * intentamos buscar el nombre con el contexto de su padre inmediato si existe.
         */
        $parentName = match ($type) {
            'state'        => $dto->country,
            'city'         => $dto->state,
            'neighborhood' => $dto->city,
            default        => null,
        };

        // Buscamos primero al padre para obtener su ID
        $parentId = null;
        if ($parentName) {
            // Un nivel arriba para filtrar
            $parentType = match ($type) {
                'state'        => 'country',
                'city'         => 'state',
                'neighborhood' => 'city',
            };
            $parent   = $this->repository->findStrict($parentName, $parentType);
            $parentId = $parent?->id;
        }

        // 3. Verificamos si el TARGET ya existe
        $existing = $this->repository->findStrict($name, $type, $parentId);

        return [
            'name'      => $name,
            'type'      => $type,
            'exists'    => (bool) $existing,
            'territory' => $existing, // Si existe, lo devolvemos de una vez
        ];
    }

/**
 * Respuesta por defecto cuando no hay coincidencias geográficas.
 * Estructura consistente para evitar errores en el Frontend.
 */
    protected function emptyResponse(): array
    {
        return [
            'exists'     => false,
            'match'      => 'none',
            'branding'   => [
                'active'      => $this->getDefaultBranding(),
                'is_fallback' => true,
                'mode'        => 'global',
            ],
            'geo'        => [
                'neighborhood' => 'Desconocido',
                'city'         => 'KUBIX Global',
                'sibling'      => null,
            ],
            'debug_path' => null,
        ];
    }

    /**
     * Branding por defecto de la plataforma (Master Brand).
     * Se activa cuando no hay match de barrio, hermano ni ciudad.
     */
    private function getDefaultBranding(): array
    {
        return [
            'primary_color'   => '#1E293B', // Un azul grisáceo elegante (Slate 800)
            'secondary_color' => '#3B82F6', // Azul KUBIX
            'splash_image'    => 'assets/branding/default_splash.png',
            'logo_url'        => 'assets/branding/logo_kubix.png',
            'welcome_message' => 'Bem-vindo ao ecossistema KUBIX',
            'mode'            => 'global',
        ];
    }

}
