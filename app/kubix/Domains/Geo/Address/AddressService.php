<?php

namespace App\Kubix\Common\Address;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Kubix\Common\Address\DTO\CreateAddressDto;

/**
 * AddressService
 *
 * Orquesta la creación, actualización y consulta de direcciones físicas polimórficas.
 *
 * RESPONSABILIDAD:
 *   Gestionar exclusivamente puntos físicos reales: sucursales, usuarios, oficinas, etc.
 *   Cada dirección representa un pin exacto en el mapa con calle, número y coordenadas.
 *
 * FUERA DE ALCANCE:
 *   - Regiones geográficas (países, estados, ciudades, barrios)
 *   - Boundaries / polígonos GeoJSON
 *   - Jerarquía territorial
 *   → Todo eso es responsabilidad de TerritoryService
 *
 * ENRIQUECIMIENTO AUTOMÁTICO:
 *   Si el frontend no envía lat/lng, este servicio los resuelve automáticamente
 *   consultando Nominatim (OSM) antes de persistir.
 *   Si OSM no responde o no encuentra resultados, la dirección se persiste
 *   igualmente con lat/lng = null — el flujo nunca se interrumpe.
 *
 * DIFERENCIA CON HasDynamicGeometry:
 *   El trait HasDynamicGeometry espera ISO Alpha-3 para el país (BRA, ARG)
 *   porque Territory trabaja con códigos ISO.
 *   Address recibe el nombre del país en texto plano desde el frontend ("Brasil")
 *   por eso el enriquecimiento de coordenadas se hace directamente aquí,
 *   sin pasar por resolveCountryForOsm().
 *
 * ┌──────────────────────────────────────────────────────────────────────┐
 * │  Flujo de createFor()                                                │
 * │                                                                      │
 * │  CreateAddressDto                                                    │
 * │      ↓                                                               │
 * │  enrichCoordinates()  →  lat/lng desde Nominatim si no vienen        │
 * │      ↓                                                               │
 * │  Repository persiste con coordenadas completas                       │
 * └──────────────────────────────────────────────────────────────────────┘
 */
class AddressService
{
    public function __construct(
        protected AddressRepository $repository
    ) {}

    // =========================================================================
    // ESCRITURA
    // =========================================================================

    /**
     * Crea una dirección física y la asocia a un modelo polimórfico.
     *
     * Si el modelo aún no tiene ninguna dirección registrada,
     * esta se marca automáticamente como primaria (is_primary = true).
     *
     * @param  Model            $model  Modelo dueño de la dirección (Branch, User, Company, etc.)
     * @param  CreateAddressDto $dto    Datos validados del request.
     * @return Address                  Instancia persistida.
     */
    public function createFor(Model $model, CreateAddressDto $dto): Address
    {
        $data = $this->enrichCoordinates($dto->toArray());

        $data['addressable_type'] = get_class($model);
        $data['addressable_id']   = $model->id;
        $data['is_primary']       = $this->shouldBePrimary($model);

        return $this->repository->create($data);
    }

    /**
     * Actualiza una dirección física re-enriqueciendo las coordenadas.
     *
     * @param  Address          $address  Instancia existente a actualizar.
     * @param  CreateAddressDto $dto      Datos validados del request.
     * @return Address                    Instancia actualizada y refrescada.
     */
    public function update(Address $address, CreateAddressDto $dto): Address
    {
        $data = $this->enrichCoordinates($dto->toArray());

        return $this->repository->update($address, $data);
    }

    /**
     * Elimina una dirección física.
     *
     * @param  Address $address
     * @return bool
     */
    public function delete(Address $address): bool
    {
        return $this->repository->delete($address);
    }

    // =========================================================================
    // LECTURA — Por identificador
    // =========================================================================

    public function findById(int $id): ?Address
    {
        return $this->repository->findById($id);
    }

    // =========================================================================
    // LECTURA — Por modelo polimórfico
    // =========================================================================

    public function findByModel(Model $model): Collection
    {
        return $this->repository->findByAddressable(get_class($model), $model->id);
    }

    public function findPrimaryByModel(Model $model): ?Address
    {
        return $this->repository->findPrimaryByAddressable(get_class($model), $model->id);
    }

    // =========================================================================
    // PRIVADO — Enriquecimiento de coordenadas
    // =========================================================================

    /**
     * Enriquece el payload resolviendo lat/lng desde OSM si no vienen del frontend.
     *
     * IMPORTANTE: Address recibe el país como nombre en texto plano ("Brasil", "México")
     * no como ISO Alpha-3 ("BRA", "MEX"). Por eso consultamos OSM directamente aquí
     * sin pasar por resolveCountryForOsm() del trait HasDynamicGeometry.
     *
     * Prioridad:
     *   1. lat/lng explícito del frontend → se usa directamente
     *   2. Consulta Nominatim con dirección completa → automático
     *   3. null → si OSM no responde (no interrumpe el flujo)
     *
     * @param  array $data  Payload del DTO convertido a array.
     * @return array        Payload con coordenadas resueltas.
     */
    private function enrichCoordinates(array $data): array
    {
        if (! empty($data['latitude']) && ! empty($data['longitude'])) {
            return $data;
        }

        $street = $data['street'] ?? null;
        $number = $data['number'] ?? null;

        $parts = array_filter([
            $street && $number ? "{$street}, {$number}" : $street,
            $data['neighborhood'] ?? null,
            $data['city']         ?? null,
            $data['state']        ?? null,
            $data['country']      ?? null, // texto plano: "Brasil", "México"
        ]);

        if (empty($parts)) {
            return $data;
        }

        $query = implode(', ', $parts);

        try {
            $response = Http::withHeaders(['User-Agent' => 'Kubix/1.0 (kontato@kubix.app)'])
                ->timeout(6)
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q'              => $query,
                    'format'         => 'json',
                    'limit'          => 1,
                    'addressdetails' => 0,
                ]);

            if ($response->successful()) {
                $result = $response->json()[0] ?? null;

                if ($result && ! empty($result['lat']) && ! empty($result['lon'])) {
                    $data['latitude']  = (float) $result['lat'];
                    $data['longitude'] = (float) $result['lon'];
                } else {
                    Log::info('AddressService@enrichCoordinates: OSM no retornó coordenadas.', [
                        'query' => $query,
                    ]);
                }
            } else {
                Log::warning('AddressService@enrichCoordinates: error HTTP de OSM.', [
                    'query'  => $query,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('AddressService@enrichCoordinates: excepción al consultar OSM.', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
        }

        return $data;
    }

    /**
     * Determina si la nueva dirección debe ser la principal del modelo.
     * Es primaria automáticamente si el modelo aún no tiene ninguna dirección.
     *
     * @param  Model $model
     * @return bool
     */
    private function shouldBePrimary(Model $model): bool
    {
        return $this->repository
            ->findByAddressable(get_class($model), $model->id)
            ->isEmpty();
    }
}