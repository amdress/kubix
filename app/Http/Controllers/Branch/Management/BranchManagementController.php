<?php
namespace App\Kubix\Features\Branch\Management;

use App\Http\Controllers\Controller;
use App\Kubix\Common\Address\DTO\CreateAddressDto;
use App\Kubix\Core\Traits\ApiResponse;
use App\Kubix\Domains\Identity\User\DTO\CreateUserDto;
use App\Kubix\Domains\Organization\Branch\DTO\CreateBranchDto;
use App\Kubix\Domains\Organization\Branch\Management\BranchManagementService;
use App\Kubix\Domains\Organization\Territory\DTO\CreateTerritoryDto;
use App\Kubix\Features\Branch\Requests\StoreBranchRequest;
use Illuminate\Http\JsonResponse;

class BranchManagementController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BranchManagementService $branchService,
    ) {}

    /**
     * POST /api/v1/branches
     *
     * Payload esperado:
     * {
     *   "territory": { "country": "Brasil", "state": "Paraná", "city": "Curitiba", "neighborhood": null },
     *   "branchData": { "email": "...", "phone": "...", "branding": {...} },
     *   "branchPhysical": false,
     *   "branchAddress": {...},
     *   "hasManager": false,
     *   "manager": {...},
     *   "hasManagerAddress": false,
     *   "managerAddress": {...}
     * }
     */
    // public function store(StoreBranchRequest $request): JsonResponse
    // {
    //     try {
    //         $payload = $request->validated();

    //         // Materializar DTOs
    //         $territoryDto = CreateTerritoryDto::from($payload['territory']);
    //         $branchDto    = new CreateBranchDto(
    //             email: $payload['branchData']['email'] ?? null,
    //             phone: $payload['branchData']['phone'] ?? null,
    //             branding: $payload['branchData']['branding'] ?? [],
    //         );

    //         // Creación atómica
    //         $branch = $this->branchService->create(
    //             branchDto: $branchDto,
    //             territoryDto: $territoryDto,
    //             branchPhysical: $payload['branchPhysical'] ?? false,
    //             branchAddressDto: $this->mapAddress($payload, 'branchAddress', 'branchPhysical'),
    //             hasManager: $payload['hasManager'] ?? false,
    //             managerDto: $this->mapManager($payload),
    //             hasManagerAddress: $payload['hasManagerAddress'] ?? false,
    //             managerAddressDto: $this->mapAddress($payload, 'managerAddress', 'hasManagerAddress'),
    //         );

    //         return $this->createdResponse($branch, 'Branch creada correctamente');

    //     } catch (\Throwable $e) {
    //         return $this->handleException($e, 'BranchManagementController@store');
    //     }
    // }

    // =========================================================================
    // MAPPERS PRIVADOS
    // =========================================================================

    private function mapAddress(array $payload, string $key, string $flagKey): ?CreateAddressDto
    {
        if (! ($payload[$flagKey] ?? false)) {
            return null;
        }

        return CreateAddressDto::from($payload[$key] ?? []);
    }

    private function mapManager(array $payload): ?CreateUserDto
    {
        if (! ($payload['hasManager'] ?? false)) {
            return null;
        }

        return CreateUserDto::from($payload['manager'] ?? []);
    }
}
