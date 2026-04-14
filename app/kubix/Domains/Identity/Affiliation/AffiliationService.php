<?php

declare (strict_types = 1);

namespace App\Kubix\Domains\Identity\Affiliation;

use App\Kubix\Domains\Identity\Affiliation\AffiliationRepository;
use App\Models\Affiliation;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * ============================================================================
 * AffiliationService — Lógica de Negocio de Afiliaciones
 * ============================================================================
 *
 * Gestiona el ciclo de vida completo de las Affiliations: el vínculo que
 * une a los usuarios con las entidades del sistema bajo un rol específico.
 *
 * ¿Por qué importa tanto este Service?
 *  Las Affiliations son la columna vertebral del sistema de contexto.
 *  Los tres middlewares de contexto (ActiveAccount, ActiveBranch, ActiveCompany)
 *  leen Affiliations para saber a qué entidades tiene acceso el usuario
 *  en cada request. Si una Affiliation está mal creada o duplicada,
 *  el contexto se rompe y el usuario puede acceder a datos incorrectos.
 *
 * Posición en la arquitectura:
 *   Orquestadores / Controllers
 *     └── AffiliationService          ← este archivo
 *           └── AffiliationRepository ← solo persistencia
 *
 * Flujo típico de Onboarding:
 *   1. AccountService::create()       → crea el contrato.
 *   2. AffiliationService::affiliate() → vincula el Owner al Account.
 *   3. AffiliationService::affiliate() → vincula el Owner a la Branch.
 *   4. En el siguiente request, ContextHandler hidrata los 3 contextos
 *      correctamente a partir de las Affiliations recién creadas.
 *
 * Reglas de negocio clave:
 *  - Un User NO puede tener dos Affiliations activas con el MISMO ROL
 *    sobre la MISMA entidad. Esto garantiza resolución determinista en los contextos.
 *  - La desactivación es siempre lógica (is_active = false), nunca física,
 *    para mantener historial de acceso y cumplimiento de auditoría.
 * ============================================================================
 */
class AffiliationService
{
    public function __construct(
        protected AffiliationRepository $repository,
    ) {}

    /**
     * Crea una nueva Affiliation vinculando un User con una entidad bajo un rol.
     *
     * Flujo:
     *  1. Verifica que no exista ya una Affiliation ACTIVA con el mismo
     *     User + Entidad + Rol (prevención de duplicados).
     *  2. Si existe, lanza DomainException — el Orquestador decide si
     *     es un error o si debe usar la existente.
     *  3. Si no existe, crea la Affiliation activa.
     *
     * La entidad es polimórfica: puede ser cualquier Model del sistema
     * (Company, Account, Branch, o cualquier futura entidad).
     *
     * @param User     $user       Usuario a afiliar.
     * @param Model    $entity     Entidad destino (polimórfica).
     * @param string   $role       Rol del usuario en esta entidad.
     * @param int|null $assignedBy ID del usuario que ejecuta la acción (auditoría).
     *
     * @throws \DomainException Si ya existe una Affiliation activa para esa combinación.
     */
    public function affiliate(
        User $user,
        Model $entity,
        string $role,
        ?int $assignedBy = null,
    ): Affiliation {
        // ── Guardia de duplicados ─────────────────────────────────────────────
        // Crítico: si permitimos duplicados, los contextos ActiveAccount y
        // ActiveBranch tomarán el first() de forma no determinista, lo que
        // puede resultar en que el usuario vea datos de otra entidad.
        // Extraemos ::class a variable — PHP no resuelve ::class dentro de strings interpolados "{}".
        $entityType = $entity::class;

        if ($this->repository->exists($user, $entity, $role)) {
            Log::warning('AffiliationService: intento de afiliación duplicada', [
                'user_id'     => $user->id,
                'entity_type' => $entityType,
                'entity_id'   => $entity->id,
                'role'        => $role,
                'assigned_by' => $assignedBy,
            ]);

            throw new \DomainException(
                "El usuario [{$user->id}] ya tiene el rol [{$role}] activo en {$entityType} [{$entity->id}]."
            );
        }

        $affiliation = $this->repository->create([
            'user_id'           => $user->id,
            'affiliatable_type' => $entity::class,
            'affiliatable_id'   => $entity->id,
            'role'              => $role,
            'is_active'         => true,
            'assigned_by'       => $assignedBy,
        ]);

        Log::info('AffiliationService: usuario afiliado', [
            'affiliation_id' => $affiliation->id,
            'user_id'        => $user->id,
            'entity_type'    => $entityType,
            'entity_id'      => $entity->id,
            'role'           => $role,
            'assigned_by'    => $assignedBy,
        ]);

        return $affiliation;
    }

    /**
     * Desactiva una Affiliation existente (baja lógica).
     *
     * El usuario pierde acceso a la entidad en el siguiente request.
     * Los contextos (ActiveAccount, ActiveBranch, ActiveCompany) solo
     * resuelven Affiliations con is_active = true, por lo que el efecto
     * es inmediato en el próximo ciclo del middleware.
     *
     * La Affiliation permanece en BD como registro histórico.
     * Usar delete() solo en casos excepcionales (corrección de errores, GDPR).
     */
    public function deactivate(Affiliation $affiliation, ?int $deactivatedBy = null): Affiliation
    {
        if (! $affiliation->is_active) {
            // Idempotente: si ya está inactiva, no hacer nada y retornar.
            Log::info('AffiliationService: intento de desactivar una affiliation ya inactiva', [
                'affiliation_id' => $affiliation->id,
            ]);

            return $affiliation;
        }

        $deactivated = $this->repository->deactivate($affiliation, $deactivatedBy);

        Log::info('AffiliationService: affiliation desactivada', [
            'affiliation_id' => $affiliation->id,
            'user_id'        => $affiliation->user_id,
            'entity_type'    => $affiliation->affiliatable_type,
            'entity_id'      => $affiliation->affiliatable_id,
            'role'           => $affiliation->role,
            'deactivated_by' => $deactivatedBy,
        ]);

        return $deactivated;
    }

    /**
     * Desactiva TODAS las Affiliations activas de un User sobre una entidad.
     *
     * Útil cuando se expulsa a un usuario de una Branch o se cierra su Account.
     * Itera y desactiva una por una para disparar logs individuales por cada vínculo.
     *
     * @return int Número de Affiliations desactivadas.
     */
    public function deactivateAllForEntity(
        User $user,
        Model $entity,
        ?int $deactivatedBy = null,
    ): int {
        // activeByUserAndType() retorna una Collection ya ejecutada (no un QueryBuilder).
        // Filtramos por entity_id usando el where() de Collection en paso separado.
        $affiliations = $this->repository
            ->activeByUserAndType($user, $entity::class)
            ->where('affiliatable_id', $entity->id)
            ->values(); // Re-indexa la colección tras el filtro

        $count = 0;

        foreach ($affiliations as $affiliation) {
            $this->deactivate($affiliation, $deactivatedBy);
            $count++;
        }

        Log::info('AffiliationService: todas las affiliations del usuario desactivadas para entidad', [
            'user_id'        => $user->id,
            'entity_type'    => $entity::class,
            'entity_id'      => $entity->id,
            'total'          => $count,
            'deactivated_by' => $deactivatedBy,
        ]);

        return $count;
    }

    /**
     * Elimina físicamente una Affiliation.
     *
     * USAR CON PRECAUCIÓN.
     * En la mayoría de casos usar deactivate() es la opción correcta.
     * Reservado para: correcciones de datos erróneos, compliance GDPR,
     * o Affiliations creadas por error que no deben quedar en el historial.
     *
     * Si el modelo Affiliation tiene SoftDeletes, esto ejecutará un soft delete.
     */
    public function delete(Affiliation $affiliation, ?int $deletedBy = null): bool
    {
        Log::warning('AffiliationService: eliminación física de affiliation', [
            'affiliation_id' => $affiliation->id,
            'user_id'        => $affiliation->user_id,
            'entity_type'    => $affiliation->affiliatable_type,
            'entity_id'      => $affiliation->affiliatable_id,
            'role'           => $affiliation->role,
            'deleted_by'     => $deletedBy,
        ]);

        return $this->repository->delete($affiliation);
    }

    /**
     * Retorna todas las Affiliations de una entidad (activas e inactivas).
     * Usado por el borrado en cascada para limpiar todos los vínculos.
     */
    public function getAffiliationsByEntity(Model $entity): Collection
    {
        return $this->repository->activeByEntity($entity);
    }
}
