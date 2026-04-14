<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Organization\Account;

use App\Models\Account;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================================
 * AccountRepository — Persistencia de Contratos
 * ============================================================================
 *
 * Capa de acceso a datos exclusiva para el modelo Account.
 * No contiene lógica de negocio — solo operaciones de lectura y escritura
 * contra la base de datos. Toda la inteligencia vive en AccountService.
 *
 * Posición en la arquitectura:
 *   AccountService → AccountRepository → Eloquent (Account Model)
 *
 * Convención de retorno:
 *  - Operaciones de escritura (create/update) → retornan el Account actualizado.
 *  - Operaciones de lectura (find/by*)        → retornan Account|null o Collection.
 *  - El consumidor (Service) es responsable de manejar los nulls.
 *
 * Relación con los Contextos:
 *  Los contextos ActiveAccount y ActiveCompany leen Accounts directamente
 *  vía Affiliation. Este Repository es usado exclusivamente por AccountService
 *  para operaciones de escritura y consultas administrativas.
 * ============================================================================
 */
class AccountRepository
{
    /**
     * Crea un nuevo Account con los atributos provistos.
     *
     * Los atributos deben incluir todos los campos requeridos por el modelo.
     * La validación y lógica previa son responsabilidad de AccountService.
     */
    public function create(array $attributes): Account
    {
        return Account::query()->create($attributes);
    }

    /**
     * Actualiza un Account existente con los atributos provistos.
     *
     * Usa el método update() de Eloquent para garantizar que los
     * observers y eventos (updating, updated, saved) se disparen
     * correctamente. Esto es crítico si hay listeners de auditoría
     * o sincronización de caché sobre el modelo Account.
     */
    public function update(Account $account, array $attributes): Account
    {
        $account->update($attributes);

        return $account->fresh(); // Retorna el modelo refrescado desde BD
    }

    /**
     * Busca un Account por su ID primario.
     * Retorna null si no existe (sin lanzar excepción).
     */
    public function findById(int $id): ?Account
    {
        return Account::query()->find($id);
    }

    /**
     * Verifica si ya existe un Account activo para una combinación
     * específica de Branch + Owner + Solución.
     *
     * Usado por AccountService antes de crear para prevenir duplicados.
     * Un mismo owner no puede tener dos contratos activos para la misma
     * solución en la misma Branch.
     */
    public function existsActive(Branch $branch, Model $owner, int $solutionId): bool
    {
        return Account::query()
            ->where('branch_id', $branch->id)
            ->where('accountable_type', $owner::class)
            ->where('accountable_id', $owner->id)
            ->where('solution_id', $solutionId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Todos los Accounts (activos e inactivos) de una Branch.
     * Útil para vistas administrativas y auditoría.
     */
    public function byBranch(Branch $branch): Collection
    {
        return Account::query()
            ->where('branch_id', $branch->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Solo los Accounts ACTIVOS de una Branch.
     * Usado por el contexto ActiveAccount y reportes operacionales.
     */
    public function activeByBranch(Branch $branch): Collection
    {
        return Account::query()
            ->where('branch_id', $branch->id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Todos los Accounts de un Owner (Company, User, etc.) sin importar estado.
     * El Owner es polimórfico: puede ser cualquier Model del sistema.
     */
    public function byOwner(Model $owner): Collection
    {
        return Account::query()
            ->where('accountable_type', $owner::class)
            ->where('accountable_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Solo los Accounts ACTIVOS de un Owner.
     * Usado para verificar si el Owner ya tiene contratos vigentes
     * antes de crear uno nuevo.
     */
    public function activeByOwner(Model $owner): Collection
    {
        return Account::query()
            ->where('accountable_type', $owner::class)
            ->where('accountable_id', $owner->id)
            ->where('is_active', true)
            ->get();
    }
}