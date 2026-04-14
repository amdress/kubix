<?php
namespace App\Kubix\Domains\Organization\Branch\Management;

use App\Kubix\Domains\Identity\Affiliation\AffiliationService;
use App\Kubix\Domains\Organization\Branch\BranchRepository;
use App\Models\Branch;
use App\Models\Territory;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class BranchManagementService
{
    public function __construct(
        protected BranchRepository $repository,
        protected AffiliationService $affiliationService
    ) {}

//============================  FUNCIONES PRINCIPALES  ===================================

    /**
     * AUTO-SEED: Sincroniza la existencia de una Branch con su Territorio usando el Path.
     */
    public function autoSeed(Territory $territory): Branch
    {
        // 1. Ahora buscamos por el "Golden Path"
        if ($existing = $this->repository->findByPath($territory->path)) {
            Log::info("BranchService: Unidad ya existe para el path", [
                'path'      => $territory->path,
                'branch_id' => $existing->id,
            ]);
            return $existing;
        }

        Log::info("BranchService: Creando unidad en {$territory->name} [{$territory->type}]");

        // 2. Creación con datos heredados. Nota que guardamos el path como clave principal lógica.
        return $this->repository->create([
            'name'            => $territory->name,
            'slug'            => $territory->slug . '-' . Str::lower(Str::random(4)),
            'code'            => $this->generateHierarchicalCode($territory),
            'territory_id'    => $territory->id, // Lo mantenemos por integridad referencial, pero no para búsquedas
            'territory_level' => $territory->type,
            'territory_path'  => $territory->path,
            'latitude'        => $territory->latitude,
            'longitude'       => $territory->longitude,
            'is_physical'     => false,
            'is_active'       => false, // La activamos por defecto en el seed
            'branding'        => [
                'primary_color'   => $territory->primary_color ?? '#3B82F6',
                'splash_image'    => $territory->splash_path ?? 'default_splash.png',
                'welcome_message' => "Bem-vindo a " . $territory->name,
            ],
            'geo_metadata'    => [
                'lat'            => $territory->latitude,
                'lon'            => $territory->longitude,
                'auto_generated' => true,
            ],
        ]);
    }

    /**
     * Vincula un usuario a una sucursal.
     */
    public function linkUser(string $branchPath, int $userId, string $role = 'branch_staff'): void
    {
        try {
            $branch = $this->repository->findByPath($branchPath);
            if (! $branch) {
                throw new \Exception("Branch no encontrada para el path: {$branchPath}");
            }

            $user = User::findOrFail($userId);
            $this->affiliationService->affiliate($user, $branch, $role);

            Log::info("BranchService: Usuario vinculado", ['path' => $branchPath, 'user' => $userId]);
        } catch (Throwable $e) {
            Log::error("BranchService@linkUser: Error", ['error' => $e->getMessage()]);
            throw $e;
        }
    }


//============================  FUNCIONES AUXILIARES  ===================================

    /**
     * RESOLVER CIUDAD POR PATH (El Padre):
     * Si el path es /1/1/1/4/ (Barrio), extrae /1/1/1/ (Ciudad) y busca su Branch.
     */
    public function findCityBranchInPath(string $path): ?Branch
    {
        if (empty($path)) {
            return null;
        }

        $segments = array_filter(explode('/', trim($path, '/')));

        // En nuestra jerarquía: 0:País, 1:Estado, 2:Ciudad
        if (count($segments) >= 3) {
            $citySegments = array_slice($segments, 0, 3);
            $cityPath     = '/' . implode('/', $citySegments) . '/';

            return $this->repository->findByPath($cityPath);
        }

        return null;
    }

    /**
     * Obtiene la Branch por Path exacto.
     */
    public function getBranchByPath(string $path): ?Branch
    {
        return $this->repository->findByPath($path);
    }

    /**
     * Encuentra la unidad activa más cercana (Fallback de proximidad).
     */
    // En BranchService.php
    public function findNearestActive(float $lat, float $lng, string $parentPath, ?int $excludeTerritoryId = null)
    {
        // Asegúrate de pasar los 4 argumentos al repository
        return $this->repository->findNearestActive($lat, $lng, $parentPath, $excludeTerritoryId);
    }

    /**
     * Genera un código basado en el nombre y el índice del territorio.
     */
    protected function generateHierarchicalCode(Territory $territory): string
    {
        $prefix = match ($territory->type) {
            'country'      => strtoupper(substr($territory->name, 0, 3)),
            'state'        => strtoupper(substr($territory->name, 0, 2)),
            'city'         => strtoupper(substr($territory->name, 0, 3)),
            'neighborhood' => 'BRR',
            default        => 'KBX'
        };

        // Usamos el ID solo para el código visual, pero el Path manda en la lógica.
        return $prefix . '-' . str_pad($territory->id, 5, '0', STR_PAD_LEFT);
    }


}
