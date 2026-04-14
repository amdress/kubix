<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Organization\Account;

use App\Kubix\Domains\Organization\Account\AccountRepository;
use App\Models\Account;
use App\Models\Affiliation;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


/**
 * ============================================================================
 * AccountService — Lógica de Negocio de Contratos
 * ============================================================================
 *
 * Gestiona el ciclo de vida completo de un Account (contrato técnico).
 * Es la única capa autorizada para crear, modificar o cambiar el estado
 * de un contrato en el sistema.
 *
 * ¿Qué es un Account?
 *  Un Account representa el CONTRATO entre una Branch y un Owner sobre
 *  una solución específica. Es el núcleo del modelo de negocio multi-tenant:
 *    - Branch:   dónde opera el contrato (sucursal).
 *    - Owner:    quién es el titular (polimórfico: Company, User, etc.).
 *    - Solution: qué producto está contratado (POS, WMS, CRM, etc.).
 *
 * Posición en la arquitectura:
 *   Orquestadores / Controllers
 *     └── AccountService          ← este archivo
 *           └── AccountRepository ← solo persistencia
 *
 * Ciclo de vida de un Account:
 *   [create] → active → [suspend] → suspended → [reactivate] → active
 *                                              → [cancel]    → cancelled
 *              active → [cancel]  → cancelled
 *
 * Relación con los Contextos:
 *  - ActiveAccount lee el Account desde la Affiliation del usuario.
 *  - AccountService es invocado por Orquestadores que CREAN o CAMBIAN
 *    el estado del contrato (Onboarding, Admin Panel, etc.).
 *  - Tras create(), el Orquestador debe crear la Affiliation correspondiente
 *    vía AffiliationService para que ActiveAccount pueda resolver el contrato.
 * ============================================================================
 */
class AccountService
{
    public function __construct(
        protected AccountRepository $repository,
    ) {}

    /**
     * Crea un nuevo contrato técnico (Account).
     *
     * Flujo:
     *  1. Verifica que no exista ya un contrato ACTIVO para la misma
     *     combinación Branch + Owner + Solution (prevención de duplicados).
     *  2. Si existe, lanza DomainException — el Orquestador decide cómo manejarlo.
     *  3. Si no existe, crea el Account en estado 'active'.
     *
     * IMPORTANTE: Crear el Account no afilia automáticamente al usuario.
     * El Orquestador que llama a este método es responsable de llamar
     * después a AffiliationService::affiliate() para vincular el Owner/User
     * al Account recién creado. Sin la Affiliation, ActiveAccount no podrá
     * resolver este contrato en los requests subsiguientes.
     *
     * @param Branch $branch      Branch donde opera el contrato.
     * @param Model  $owner       Titular del contrato (polimórfico).
     * @param int    $solutionId  ID de la solución contratada.
     * @param string $solutionSlug Slug de la solución (para lecturas rápidas sin JOIN).
     * @param array  $branding    Configuración visual del contrato (opcional).
     * @param int|null $createdBy ID del usuario que ejecuta la acción (auditoría).
     *
     * @throws \DomainException Si ya existe un contrato activo para esa combinación.
     */
    public function create(
        Branch $branch,
        Model $owner,
        int $solutionId,
        string $solutionSlug,
        array $branding = [],
        ?int $createdBy = null,
    ): Account {
        // ── Guardia de duplicados ─────────────────────────────────────────────
        if ($this->repository->existsActive($branch, $owner, $solutionId)) {
            Log::warning('AccountService: intento de crear contrato duplicado', [
                'branch_id'   => $branch->id,
                'owner_type'  => $owner::class,
                'owner_id'    => $owner->id,
                'solution_id' => $solutionId,
                'created_by'  => $createdBy,
            ]);

            throw new \DomainException(
                "Ya existe un contrato activo para este owner y solución en la Branch [{$branch->id}]."
            );
        }

        $account = $this->repository->create([
            'branch_id'        => $branch->id,
            'solution_id'      => $solutionId,
            'solution_slug'    => $solutionSlug,
            'accountable_type' => $owner::class,
            'accountable_id'   => $owner->id,
            'branding'         => $branding,
            'status'           => 'active',
            'is_active'        => true,
            'activated_at'     => Carbon::now(),
            'created_by'       => $createdBy,
        ]);

        Log::info('AccountService: contrato creado', [
            'account_id'  => $account->id,
            'branch_id'   => $branch->id,
            'owner_type'  => $owner::class,
            'owner_id'    => $owner->id,
            'solution'    => $solutionSlug,
            'created_by'  => $createdBy,
        ]);

        return $account;
    }

    /**
     * Suspende un contrato activo temporalmente.
     *
     * Un contrato suspendido puede ser reactivado (a diferencia de cancelado).
     * Casos de uso: falta de pago, violación temporal de términos, mantenimiento.
     *
     * Guardia: no suspende un contrato que ya está suspendido o cancelado.
     *
     * @throws \DomainException Si el contrato no está en estado 'active'.
     */
    public function suspend(Account $account, ?int $suspendedBy = null): Account
    {
        if ($account->status !== 'active') {
            throw new \DomainException(
                "Solo se puede suspender un contrato activo. Estado actual: [{$account->status}]."
            );
        }

        $updated = $this->repository->update($account, [
            'status'       => 'suspended',
            'is_active'    => false,
            'suspended_at' => Carbon::now(),
            'suspended_by' => $suspendedBy,
        ]);

        Log::info('AccountService: contrato suspendido', [
            'account_id'   => $account->id,
            'suspended_by' => $suspendedBy,
        ]);

        return $updated;
    }

    /**
     * Reactiva un contrato previamente suspendido.
     *
     * Devuelve el contrato al estado 'active', limpiando los timestamps
     * de suspensión para mantener el historial limpio.
     * No puede reactivar contratos cancelados — esos requieren uno nuevo.
     *
     * @throws \DomainException Si el contrato no está en estado 'suspended'.
     */
    public function reactivate(Account $account, ?int $reactivatedBy = null): Account
    {
        if ($account->status !== 'suspended') {
            throw new \DomainException(
                "Solo se puede reactivar un contrato suspendido. Estado actual: [{$account->status}]."
            );
        }

        $updated = $this->repository->update($account, [
            'status'          => 'active',
            'is_active'       => true,
            'activated_at'    => Carbon::now(), // Fecha de la reactivación
            'suspended_at'    => null,
            'suspended_by'    => null,
            'reactivated_by'  => $reactivatedBy,
        ]);

        Log::info('AccountService: contrato reactivado', [
            'account_id'     => $account->id,
            'reactivated_by' => $reactivatedBy,
        ]);

        return $updated;
    }

    /**
     * Cancela un contrato de forma definitiva.
     *
     * Un contrato cancelado NO puede reactivarse. Para volver a operar,
     * el Orquestador debe crear un nuevo contrato con create().
     * Casos de uso: fin de relación comercial, cierre de cuenta.
     *
     * Guardia: no cancela un contrato ya cancelado.
     *
     * @throws \DomainException Si el contrato ya está cancelado.
     */
    public function cancel(Account $account, ?int $cancelledBy = null): Account
    {
        if ($account->status === 'cancelled') {
            throw new \DomainException(
                "El contrato [{$account->id}] ya está cancelado."
            );
        }

        $updated = $this->repository->update($account, [
            'status'        => 'cancelled',
            'is_active'     => false,
            'cancelled_at'  => Carbon::now(),
            'cancelled_by'  => $cancelledBy,
            'suspended_at'  => null, // Limpia suspensión previa si existía
            'suspended_by'  => null,
        ]);

        Log::info('AccountService: contrato cancelado', [
            'account_id'   => $account->id,
            'cancelled_by' => $cancelledBy,
        ]);

        return $updated;
    }

    /**
     * Actualiza el branding visual de un contrato existente.
     *
     * Separado del ciclo de vida para no mezclar responsabilidades.
     * Llamado desde paneles de personalización del cliente.
     */
    public function updateBranding(Account $account, array $branding, ?int $updatedBy = null): Account
    {
        $updated = $this->repository->update($account, [
            'branding'   => $branding,
            'updated_by' => $updatedBy,
        ]);

        Log::info('AccountService: branding actualizado', [
            'account_id' => $account->id,
            'updated_by' => $updatedBy,
        ]);

        return $updated;
    }
}