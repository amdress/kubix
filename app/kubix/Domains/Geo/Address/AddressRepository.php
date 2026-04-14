<?php

namespace App\Kubix\Common\Address;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

/**
 * AddressRepository
 *
 * Capa de persistencia para direcciones físicas polimórficas.
 * No contiene lógica de negocio — solo consultas y operaciones sobre la tabla 'addresses'.
 *
 * RESPONSABILIDAD:
 *   Persistir y consultar puntos físicos reales asociados a cualquier modelo
 *   del sistema mediante relación polimórfica (addressable).
 *
 * FUERA DE ALCANCE:
 *   - Filtros por jerarquía territorial (país, estado, ciudad, barrio)
 *   - Consultas por tipo de dirección (point / area)
 *   - Boundaries o polígonos GeoJSON
 *   → Todo eso pertenece a TerritoryRepository
 *
 * COLUMNAS DISPONIBLES EN 'addresses':
 *   - id
 *   - addressable_type / addressable_id  (polimórfica)
 *   - label
 *   - zip_code
 *   - street
 *   - number
 *   - complement
 *   - reference
 *   - latitude / longitude
 *   - is_primary
 *   - created_at / updated_at / deleted_at
 *
 * @see AddressService  Orquestador que consume este repositorio
 * @see Address         Modelo Eloquent con relación polimórfica
 */
class AddressRepository
{
    // =========================================================================
    // ESCRITURA
    // =========================================================================

    /**
     * Persiste una nueva dirección física con todos sus campos resueltos.
     * El payload debe llegar ya enriquecido (coordenadas incluidas si aplica).
     *
     * @param  array   $data  Datos listos para persistir.
     * @return Address        Instancia creada.
     */
    public function create(array $data): Address
    {
        return Address::query()->create($data);
    }

    /**
     * Actualiza una dirección existente y refresca la instancia desde la DB.
     *
     * @param  Address $address  Instancia a actualizar.
     * @param  array   $data     Nuevos datos a aplicar.
     * @return Address           Instancia actualizada y refrescada.
     */
    public function update(Address $address, array $data): Address
    {
        $address->update($data);
        $address->refresh();

        return $address;
    }

    /**
     * Elimina una dirección.
     * Si el modelo tiene SoftDeletes configurado, queda en papelera.
     *
     * @param  Address $address  Instancia a eliminar.
     * @return bool              true si fue eliminada correctamente.
     */
    public function delete(Address $address): bool
    {
        return (bool) $address->delete();
    }

    // =========================================================================
    // LECTURA — Por identificador
    // =========================================================================

    /**
     * Busca una dirección por su ID primario.
     * Retorna null si no existe.
     *
     * @param  int          $id
     * @return Address|null
     */
    public function findById(int $id): ?Address
    {
        return Address::query()->find($id);
    }

    // =========================================================================
    // LECTURA — Por modelo polimórfico
    // =========================================================================

    /**
     * Retorna todas las direcciones asociadas a un modelo específico.
     *
     * Útil para listar múltiples sedes de una Branch o varias
     * direcciones de un User.
     *
     * @param  string                   $type  Clase del modelo (ej: App\Models\Branch)
     * @param  int                      $id    ID del modelo
     * @return Collection<int, Address>
     */
    public function findByAddressable(string $type, int $id): Collection
    {
        return Address::query()
            ->where('addressable_type', $type)
            ->where('addressable_id', $id)
            ->get();
    }

    /**
     * Retorna la dirección principal (is_primary = true) de un modelo.
     * Retorna null si el modelo no tiene dirección principal registrada.
     *
     * @param  string       $type  Clase del modelo
     * @param  int          $id    ID del modelo
     * @return Address|null
     */
    public function findPrimaryByAddressable(string $type, int $id): ?Address
    {
        return Address::query()
            ->where('addressable_type', $type)
            ->where('addressable_id', $id)
            ->where('is_primary', true)
            ->first();
    }
}