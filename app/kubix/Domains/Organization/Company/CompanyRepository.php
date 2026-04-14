<?php

namespace App\Kubix\Domains\Organization\Company;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

/**
 * CompanyRepository
 *
 * Capa de persistencia para Companies.
 * No contiene lógica de negocio — solo consultas sobre la tabla 'companies'.
 *
 * QUIÉN LO CONSUME:
 *   CompanyService — único orquestador que puede llamar a este repository.
 */
class CompanyRepository
{
    // =========================================================================
    // ESCRITURA
    // =========================================================================

    public function create(array $data): Company
    {
        return Company::query()->create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        $company->refresh();

        return $company;
    }

    public function delete(Company $company): bool
    {
        return (bool) $company->delete();
    }

    // =========================================================================
    // LECTURA — Por identificador
    // =========================================================================

    public function findById(int $id): ?Company
    {
        return Company::query()->find($id);
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::query()->where('slug', $slug)->first();
    }

    // =========================================================================
    // LECTURA — Por territorio
    // =========================================================================

    /**
     * Retorna todas las empresas activas de una Branch.
     *
     * Usado para dashboards territoriales y KPIs por zona.
     * Ej: "dame todas las empresas de Pinheirinho"
     *
     * @param  int                      $branchId
     * @return Collection<int, Company>
     */
    public function findByBranch(int $branchId): Collection
    {
        return Company::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Retorna todas las empresas de una Branch filtradas por tipo.
     *
     * Ej: "dame solo las empresas formales de Curitiba"
     *
     * @param  int                      $branchId
     * @param  string                   $type     'informal' | 'formal'
     * @return Collection<int, Company>
     */
    public function findByBranchAndType(int $branchId, string $type): Collection
    {
        return Company::query()
            ->where('branch_id', $branchId)
            ->where('type', $type)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // =========================================================================
    // LECTURA — Por usuario
    // =========================================================================

    /**
     * Retorna todas las empresas donde el usuario tiene alguna afiliación.
     *
     * Incluye empresas donde es business_owner y business_staff.
     * Usado para el panel del usuario: "mis empresas".
     *
     * @param  int                      $userId
     * @return Collection<int, Company>
     */
    public function findByUser(int $userId): Collection
    {
        return Company::query()
            ->whereHas('affiliations', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Retorna todas las empresas donde el usuario es business_owner.
     *
     * Usado para determinar qué empresas puede gestionar el usuario.
     *
     * @param  int                      $userId
     * @return Collection<int, Company>
     */
    public function findOwnedByUser(int $userId): Collection
    {
        return Company::query()
            ->whereHas('affiliations', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('role', 'business_owner')
                      ->where('is_active', true);
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // =========================================================================
    // VALIDACIÓN
    // =========================================================================

    /**
     * Verifica si ya existe una empresa con ese slug.
     * Usado para manejar colisiones antes de persistir.
     *
     * @param  string   $slug
     * @param  int|null $exceptId  Excluir un ID específico (para updates)
     * @return bool
     */
    public function existsBySlug(string $slug, ?int $exceptId = null): bool
    {
        $query = Company::query()->where('slug', $slug);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * Verifica si ya existe una empresa con ese CNPJ.
     *
     * @param  string   $cnpj
     * @param  int|null $exceptId
     * @return bool
     */
    public function existsByCnpj(string $cnpj, ?int $exceptId = null): bool
    {
        $query = Company::query()->where('cnpj', $cnpj);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}