<?php
namespace App\Http\Controllers\Territory\Management;

use App\Http\Controllers\Controller;
use App\Kubix\Core\Traits\ApiResponse;
use App\Kubix\Domains\Geo\Territory\Management\TerritoryManagementService;
use App\Kubix\Domains\Geo\Territory\DTO\CheckAvailabilityDto;
use App\Kubix\Domains\Geo\Territory\DTO\ResolveTerritorySelectionDto;
use App\Kubix\Domains\Geo\Territory\DTO\SuggestTerritoryDto;
use App\Http\Controllers\Territory\Resources\TerritorySuggestionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * TerritoryManagementController
 * * Endpoints públicos para el bootstrap geográfico de KUBIX.
 */
class TerritoryManagementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected TerritoryManagementService $service,
    ) {}

//============================  FUNCIONES PRINCIPALES  ===================================

    /**
     * Gestiona la disponibilidad y branding territorial para el Pre-login.
     * * @param Request $request Contiene latitude y longitude.
     * @return JsonResponse Estructura estandarizada con data de branding y geolocalización.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            /**
             * 1. Usamos el DTO para validar y capturar los datos.
             * Quitamos el $request->validate manual porque el DTO ya tiene las reglas
             * de "Si no hay coordenadas, pide path".
             */
            $dto = CheckAvailabilityDto::from($request->all());

            // 2. Invocamos al servicio (El "Bailarín" ya sabe qué hacer si $dto->path tiene valor)
            $result = $this->service->checkAvailability($dto);

            /**
             * 3. Respuesta de Éxito
             * El Service ahora devuelve la "Doble Capa". 
             * Usamos el flag 'exists' para decidir el mensaje.
             */
            return $this->successResponse(
                data: $result,
                message: ($result['exists'] ?? false) 
                    ? "Cobertura identificada." 
                    : "Sin cobertura en esta zona."
            );

        } catch (\Throwable $e) {
            // Si el DTO falla por validación o el Service explota, capturamos aquí
            return $this->handleException($e, 'TerritoryManagementController@checkAvailability');
        }
    }

    /**
     * GET /api/v1/territories/suggest
     * Endpoint público para autocompletar territorios usando Nominatim.
     */
    public function findSuggests(Request $request): JsonResponse
    {
        Log::info('TerritoryManagementController@findSuggests: Request received.', [
            'query' => $request->get('query'),
            'ip'    => $request->ip(),
        ]);

        $validated = $request->validate(
            SuggestTerritoryDto::rules(),
            SuggestTerritoryDto::messages()
        );

        try {
            // Log::info('TerritoryManagementController@findSuggests: Validation passed.', [
            //     'query' => $validated['query'],
            // ]);

            $dto = new SuggestTerritoryDto(
                query: $validated['query']
            );

            // Log::info('TerritoryManagementController@findSuggests: DTO created.', [
            //     'query' => $dto->query,
            // ]);

            $suggestions = $this->service->findSuggests($dto);

            $resultsCount = count($suggestions['results'] ?? []);

            Log::info('TerritoryManagementController@findSuggests: Service response.', [
                'query'         => $dto->query,
                'results_count' => $resultsCount,
            ]);

            $results = collect($suggestions['results'] ?? [])->map(fn($item) => (array) $item);

            return $this->successResponse(
                TerritorySuggestionResource::collection($results),
                count($results) > 0 ? 'Sugerencias encontradas.' : 'No se encontraron sugerencias.'
            );

        } catch (\Throwable $e) {

            Log::error('TerritoryManagementController@findSuggests: Fail.', [
                'error' => $e->getMessage(),
                'query' => $request->get('query'),
            ]);

            return $this->handleException($e, 'TerritoryManagementController@findSuggests');
        }
    }

/**
 * POST /api/v1/territories/resolve-selection
 * Procesa la selección de una sugerencia y construye la jerarquía.
 */
    public function resolveSelection(Request $request): JsonResponse
    {
        try {

            // 1. Validamos usando los métodos estáticos del DTO
            $validated = $request->validate(
                ResolveTerritorySelectionDto::rules(),
                ResolveTerritorySelectionDto::messages()
            );

            // 2. Creamos el DTO con los datos ya limpios
            $dto    = ResolveTerritorySelectionDto::fromArray($validated);

            // 3. Llamamos al servicio para procesar la selección
            $result = $this->service->resolveSelection($dto);

            return $this->successResponse(
                data: [
                    'success'   => true,
                    'existed'   => $result['existed'],
                    'territory' => $result['territory'],
                ],
                message: $result['existed']
                    ? "El territorio '{$result['territory']->name}' ya existe."
                    : "Territorio creado exitosamente."
            );

        } catch (\Throwable $e) {
            return $this->handleException($e, 'TerritoryManagementController@resolveSelection');
        }
    }


//============================  FUNCIONES AUXILIARES  ===================================

}
