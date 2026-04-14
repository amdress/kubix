<?php

namespace App\Kubix\Domains\Organization\Company;

use App\Models\Company;
use App\Models\User;
use App\Kubix\Common\Address\AddressService;
use App\Kubix\Common\Address\DTO\CreateAddressDto;
use App\Kubix\Domains\Identity\Affiliation\AffiliationService;
use App\Kubix\Domains\Identity\User\UserService;
use App\Kubix\Domains\Organization\Branch\BranchRepository;
use App\Kubix\Domains\Organization\Company\DTO\CreateCompanyDto;
use App\Kubix\Domains\Organization\Company\DTO\UpdateCompanyDto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * CompanyService
 *
 * Orquestador de la creación y gestión de Companies.
 *
 * RESPONSABILIDAD:
 *   Coordinar la creación de una Company dentro de un territorio,
 *   vincular al usuario creador como business_owner,
 *   crear la dirección física si aplica,
 *   y gestionar la asignación de staff.
 *
 * FLUJO DE VIDA DEL USUARIO:
 *   1. Usuario se registra en Kubix (customer)
 *   2. Inicia sesión → panel de usuario
 *   3. "Registrar mi empresa" → llega aquí
 *   4. Al crear la Company, el usuario sube de contexto:
 *      customer → business_owner via Affiliation
 *
 * JERARQUÍA DE ASIGNACIÓN:
 *   business_owner → asigna business_staff a su propia Company
 *   superadmin     → puede asignar en cualquier Company
 *   (la validación de autorización vive en el controller)
 *
 * REGLAS DE NEGOCIO:
 *   - type = formal requiere CNPJ obligatoriamente
 *   - La Branch debe existir y estar activa
 *   - El slug se genera automáticamente desde trade_name o name
 *   - Si hay colisión de slug, se agrega sufijo numérico
 *
 * ┌─────────────────────────────────────────────────────────────────────────┐
 * │  Flujo de create()                                                      │
 * │                                                                         │
 * │  FASE 1 — Validaciones (fuera de transacción)                          │
 * │  1. Verificar Branch existe y está activa                               │
 * │  2. Si type = formal → verificar CNPJ presente                         │
 * │  3. Resolver slug único                                                 │
 * │                                                                         │
 * │  FASE 2 — Persistencia (dentro de transacción)                         │
 * │  4. CompanyRepository::create()                                         │
 * │  5. AddressService::createFor()  → solo si hasAddress = true           │
 * │  6. AffiliationService::affiliate() → business_owner                   │
 * └─────────────────────────────────────────────────────────────────────────┘
 */
class CompanyService
{
    public function __construct(
        protected CompanyRepository  $companyRepository,
        protected BranchRepository   $branchRepository,
        protected AddressService     $addressService,
        protected AffiliationService $affiliationService,
        protected UserService        $userService,
    ) {}

    // =========================================================================
    // CREACIÓN
    // =========================================================================

    /**
     * Orquesta la creación completa de una Company.
     *
     * @param  User                  $owner
     * @param  CreateCompanyDto      $dto
     * @param  bool                  $hasAddress
     * @param  CreateAddressDto|null $addressDto
     * @return Company
     *
     * @throws \DomainException
     */
    public function create(
        User               $owner,
        CreateCompanyDto   $dto,
        bool               $hasAddress = false,
        ?CreateAddressDto  $addressDto = null,
    ): Company {

        // ── FASE 1: Validaciones (fuera de transacción) ───────────────────────

        $branch = $this->branchRepository->findById($dto->branchId);

        if (! $branch) {
            throw new \DomainException("Branch #{$dto->branchId} no encontrada.");
        }

        if (! $branch->is_active) {
            throw new \DomainException("Branch #{$dto->branchId} no está activa.");
        }

        if ($dto->type === 'formal' && empty($dto->cnpj)) {
            throw new \DomainException(
                "Empresas formales requieren CNPJ. Proporciona el CNPJ o registra como informal."
            );
        }

        $slug = $this->resolveSlug($dto->tradeName ?? $dto->name);

        // ── FASE 2: Persistencia (dentro de transacción) ──────────────────────

        return DB::transaction(function () use ($owner, $dto, $slug, $hasAddress, $addressDto) {

            $company = $this->companyRepository->create([
                ...$dto->toArray(),
                'slug'      => $slug,
                'is_active' => true,
            ]);

            Log::info('CompanyService: company creada', [
                'company_id'   => $company->id,
                'company_name' => $company->name,
                'type'         => $company->type,
                'branch_id'    => $company->branch_id,
                'owner_id'     => $owner->id,
            ]);

            if ($hasAddress && $addressDto) {
                $this->addressService->createFor($company, $addressDto);
                Log::info('CompanyService: dirección creada', [
                    'company_id' => $company->id,
                ]);
            }

            // Usuario sube de contexto: customer → business_owner
            $this->affiliationService->affiliate($owner, $company, 'business_owner');

            Log::info('CompanyService: owner afiliado', [
                'company_id' => $company->id,
                'owner_id'   => $owner->id,
                'role'       => 'business_owner',
            ]);

            return $company;
        });
    }

    // =========================================================================
    // ASIGNACIÓN DE STAFF
    // =========================================================================

    /**
     * Asigna un business_staff a una Company existente.
     *
     * QUIÉN PUEDE LLAMAR ESTO:
     *   business_owner de esa Company o superadmin.
     *   La validación de autorización vive en el controller.
     *
     * @param  int $companyId
     * @param  int $userId
     * @return void
     *
     * @throws \DomainException
     */
    public function assignStaff(int $companyId, int $userId): void
    {
        $company = $this->companyRepository->findById($companyId);

        if (! $company) {
            throw new \DomainException("Empresa #{$companyId} no encontrada.");
        }

        if (! $company->is_active) {
            throw new \DomainException("Empresa #{$companyId} no está activa.");
        }

        $user = $this->userService->findById($userId);

        if (! $user) {
            throw new \DomainException("Usuario #{$userId} no encontrado.");
        }

        // AffiliationService lanza DomainException si ya es staff activo
        $this->affiliationService->affiliate($user, $company, 'business_staff');

        Log::info('CompanyService: staff asignado a company', [
            'company_id' => $company->id,
            'user_id'    => $user->id,
            'role'       => 'business_staff',
        ]);
    }

    // =========================================================================
    // ACTUALIZACIÓN
    // =========================================================================

    /**
     * Actualiza los datos de una Company existente.
     * branch_id y type no son actualizables directamente aquí.
     */
    public function update(Company $company, UpdateCompanyDto $dto): Company
    {
        $data = $dto->toArray();

        if (isset($data['name']) || isset($data['trade_name'])) {
            $baseName     = $data['trade_name'] ?? $data['name'] ?? $company->trade_name ?? $company->name;
            $data['slug'] = $this->resolveSlug($baseName, $company->id);
        }

        return $this->companyRepository->update($company, $data);
    }

    // =========================================================================
    // ELIMINACIÓN
    // =========================================================================

    public function delete(Company $company): bool
    {
        return $this->companyRepository->delete($company);
    }

    // =========================================================================
    // LECTURA
    // =========================================================================

    public function findById(int $id): ?Company
    {
        return $this->companyRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Company
    {
        return $this->companyRepository->findBySlug($slug);
    }

    // =========================================================================
    // PRIVADO
    // =========================================================================

    /**
     * Resuelve un slug único para la Company.
     * Si hay colisión agrega sufijo numérico: "don-juan" → "don-juan-2"
     */
    private function resolveSlug(string $name, ?int $exceptId = null): string
    {
        $base    = Str::slug($name);
        $slug    = $base;
        $counter = 2;

        while ($this->companyRepository->existsBySlug($slug, $exceptId)) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}