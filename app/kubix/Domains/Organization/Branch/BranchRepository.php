<?php
namespace App\Kubix\Domains\Organization\Branch;

use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BranchRepository
{

    private function query()
    {
        // Ya no necesitamos cargar 'territory.parent' porque el path
        // ya nos dice quién es el padre sin hacer consultas extra.
        return Branch::query();
    }

    //============================  FUNCIONES PRINCIPALES  ===================================

    /**
     * CREACIÓN CON PATH
     * El campo 'territory_path' es ahora la clave principal lógica para identificar sucursales.
     */
    public function create(array $data): Branch
    {
        return DB::transaction(function () use ($data) {
            try {
                return Branch::create($data);
            } catch (Throwable $e) {
                Log::error("BranchRepository@create: Error al persistir unidad.", [
                    'path'  => $data['territory_path'] ?? 'N/A',
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * ACTUALIZACIÓN POR PATH
     * Permite modificar cualquier campo de la sucursal identificada por su path.
     */
    public function updateByPath(string $path, array $data): Branch
    {
        return DB::transaction(function () use ($path, $data) {
            $branch = $this->query()->where('territory_path', $path)->firstOrFail();
            $branch->update($data);
            return $branch;
        });
    }

    /**
     * ACTUALIZACIÓN DE BRANDING POR PATH
     * Permite cambiar la identidad visual de una sucursal específica.
     */
    public function updateBranding(string $path, array $brandingData): bool
    {
        return $this->query()
            ->where('territory_path', $path)
            ->update([
                'branding'   => json_encode($brandingData),
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * ACTIVACIÓN/DESACTIVACIÓN EN CASCADA
     * Si desactivas una ciudad (/1/1/1/), desactivas TODAS sus sucursales hijas.
     * ¡Esto es lo que hablábamos de 'cortar el tronco'!
     */
    public function toggleActiveByPath(string $path, bool $status): int
    {
        // Usamos LIKE 'path%' para que afecte al nodo y a toda su descendencia
        return $this->query()
            ->where('territory_path', 'like', "{$path}%")
            ->update([
                'is_active'  => $status,
                'updated_at' => now(),
            ]);
    }

    /**
     * BÚSQUEDA MAESTRA POR PATH
     * Este es ahora el método principal para encontrar sucursales.
     */
    public function findByPath(string $path): ?Branch
    {
        return $this->query()->where('territory_path', $path)->first();
    }

    /**
     * BÚSQUEDA POR PREFIJO (HERMANOS/HIJOS)
     * Útil para traer todas las sucursales de una ciudad o estado.
     */
    public function findByPathPrefix(string $prefix)
    {
        return $this->query()
            ->where('territory_path', 'like', "{$prefix}%")
            ->where('is_active', true)
            ->get();
    }

/**
 * Localiza la sucursal activa más cercana uniendo Branch con su Territory.

 */
    public function findNearestActive(float $lat, float $lng, string $parentPath, ?int $excludeTerritoryId = null): ?Branch
    {
        Log::debug("REPOSITORY: Ejecutando búsqueda espacial", [
            'lat'         => $lat,
            'lng'         => $lng,
            'filtro_path' => $parentPath . '%',
        ]);

        $query = Branch::query()
            ->select('branches.*')
            ->join('territories', 'branches.territory_id', '=', 'territories.id')
            ->where('branches.is_active', 1) 
            ->where('territories.path', 'LIKE', "{$parentPath}%")
            ->whereNull('branches.deleted_at')
            ->whereNull('territories.deleted_at')
            ->when($excludeTerritoryId, function ($q) use ($excludeTerritoryId) {
                $q->where('territories.id', '!=', $excludeTerritoryId);
            })
            ->selectRaw(
                "ST_Distance_Sphere(
                POINT(territories.longitude, territories.latitude),
                POINT(?, ?)
            ) as distance",
                [$lng, $lat]
            )
            ->orderBy('distance');

        Log::debug("REPOSITORY: SQL Generado", [
            'sql'      => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        return $query->first();
    }

/**
 * SINCRONIZACIÓN DE COORDENADAS
 * Si el territorio se mueve o se ajusta, actualizamos la sucursal.
 */
    public function updateLocation(string $path, float $lat, float $lon): bool
    {
        return $this->query()
            ->where('territory_path', $path)
            ->update([
                'latitude'   => $lat,
                'longitude'  => $lon,
                'updated_at' => now(),
            ]) > 0;
    }

    //============================  FUNCIONES AUXILIARES  ===================================

}
