<?php

declare (strict_types = 1);

namespace App\Kubix\Domains\Identity\Affiliation;

use App\Models\Affiliation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================================
 * AffiliationRepository — Persistencia de Afiliaciones
 * ============================================================================
 *
 * Capa de acceso a datos exclusiva para el modelo Affiliation.
 * No contiene lógica de negocio — solo operaciones de lectura y escritura.
 * Toda la inteligencia (validaciones, guardias, reglas) vive en AffiliationService.
 *
 * ¿Qué es una Affiliation?
 *  El vínculo polimórfico que une un User con cualquier entidad del sistema
 *  (Company, Account, Branch) bajo un rol específico. Es la tabla pivote
 *  central del modelo de permisos y contexto de Kubix.
 *
 *  user_id | affiliatable_type | affiliatable_id | role       | is_active
 *  --------|-------------------|-----------------|------------|----------
 *  42      | App\Models\Branch | 7               | manager    | true
 *  42      | App\Models\Account| 3               | owner      | true
 *  15      | App\Models\Company| 1               | staff      | true
 *
 * Posición en la arquitectura:
 *   AffiliationService → AffiliationRepository → Eloquent (Affiliation Model)
 *
 * Relación con los Contextos (ActiveAccount, ActiveBranch, ActiveCompany):
 *  Los tres contextos del middleware leen Affiliations directamente en su
 *  setByUser(). Este Repository es usado por AffiliationService para las
 *  operaciones de escritura (crear/desactivar) y las consultas de apoyo
 *  (verificar existencia, listar afiliaciones de un usuario o entidad).
 * ============================================================================
 */
class AffiliationRepository
{
    /**
     * Crea una nueva Affiliation con los atributos provistos.
     *
     * Los atributos deben incluir: user_id, affiliatable_type,
     * affiliatable_id, role, is_active. La validación previa es
     * responsabilidad de AffiliationService.
     */
    public function create(array $attributes): Affiliation
    {
        return Affiliation::query()->create($attributes);
    }

    /**
     * Desactiva una Affiliation sin eliminarla físicamente.
     *
     * Registra el timestamp de desactivación para auditoría.
     * La Affiliation permanece en BD como historial — no se borra.
     * El usuario pierde acceso a la entidad en el siguiente request.
     */
    public function deactivate(Affiliation $affiliation, ?int $deactivatedBy = null): Affiliation
    {
        $affiliation->update([
            'is_active'      => false,
            'deactivated_at' => now(),
            'deactivated_by' => $deactivatedBy,
        ]);

        return $affiliation->fresh();
    }

    /**
     * Elimina físicamente una Affiliation (soft delete si el modelo lo soporta).
     *
     * Usar con precaución. En la mayoría de casos es preferible deactivate().
     * Reservado para correcciones de datos o eliminación de usuarios en GDPR.
     */
    public function delete(Affiliation $affiliation): bool
    {
        return (bool) $affiliation->delete();
    }

    /**
     * Busca una Affiliation por su ID primario.
     * Retorna null si no existe.
     */
    public function findById(int $id): ?Affiliation
    {
        return Affiliation::query()->find($id);
    }

    /**
     * Verifica si ya existe una Affiliation ACTIVA para una combinación
     * específica de User + Entidad + Rol.
     *
     * Usado por AffiliationService antes de crear para prevenir duplicados.
     * Un usuario no puede tener dos afiliaciones activas con el mismo rol
     * en la misma entidad — esto garantiza que los contextos (ActiveBranch,
     * ActiveAccount) resuelvan siempre un único resultado determinista.
     */
    public function exists(User $user, Model $entity, string $role): bool
    {
        return Affiliation::query()
            ->where('user_id', $user->id)
            ->where('affiliatable_type', $entity::class)
            ->where('affiliatable_id', $entity->id)
            ->where('role', $role)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Busca la Affiliation activa de un User sobre una entidad específica,
     * sin filtrar por rol. Útil para verificaciones de acceso general.
     *
     * Si un usuario tiene múltiples roles sobre la misma entidad (caso raro),
     * retorna la primera encontrada. Para múltiples roles usar activeByUserAndEntity().
     */
    public function findActiveByUserAndEntity(User $user, Model $entity): ?Affiliation
    {
        return Affiliation::query()
            ->where('user_id', $user->id)
            ->where('affiliatable_type', $entity::class)
            ->where('affiliatable_id', $entity->id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Todas las Affiliations ACTIVAS de un User.
     *
     * Retorna todas las entidades a las que tiene acceso el usuario.
     * Usado por paneles de administración para mostrar el árbol de acceso.
     */
    public function activeByUser(User $user): Collection
    {
        return Affiliation::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Todas las Affiliations ACTIVAS sobre una entidad (Branch, Account, Company).
     *
     * Retorna todos los usuarios que tienen acceso activo a esa entidad.
     * Usado para listar miembros de una Branch o Account en el admin.
     */
    public function activeByEntity(Model $entity): Collection
    {
        return Affiliation::query()
            ->where('affiliatable_type', $entity::class)
            ->where('affiliatable_id', $entity->id)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Todas las Affiliations ACTIVAS de un User sobre un tipo de entidad específico.
     *
     * Ejemplo: todas las Branches a las que pertenece el usuario.
     *   $repo->activeByUserAndType($user, Branch::class)
     *
     * Usado por el Branch Switcher para listar las opciones del Owner.
     */
    public function activeByUserAndType(User $user, string $entityType): Collection
    {
        return Affiliation::query()
            ->where('user_id', $user->id)
            ->where('affiliatable_type', $entityType)
            ->where('is_active', true)
            ->with('affiliatable') // Eager load para no generar N+1 en el Switcher
            ->get();
    }

    /**
     * Todas las Affiliations de una entidad sin importar si están activas o no.
     * Usado exclusivamente para borrado en cascada.
     */
    public function allByEntity(Model $entity): Collection
    {
        return Affiliation::query()
            ->where('affiliatable_type', $entity::class)
            ->where('affiliatable_id', $entity->id)
            ->get();
    }

}
