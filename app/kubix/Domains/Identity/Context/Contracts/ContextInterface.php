<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Context\Contracts;

use App\Models\User;
use App\Models\Affiliation;

interface ContextInterface
{
    /**
     * Hidratación desde Base de Datos (SQL).
     * Se usa generalmente en el proceso de Login/Register inicial
     * para construir el contexto desde los modelos de Eloquent.
     */
    public function hydrate(User $user, Affiliation $affiliation): void;

    /**
     * Hidratación desde Payload (Redis).
     * Es el motor de alta velocidad que usa el Middleware en cada Request
     * para evitar consultas a la base de datos leyendo el JSON del "Gafete".
     */
    public function hydrateFromPayload(User $user, array $payload): void;

    /**
     * Información de Identidad.
     */
    public function user(): ?User;

    /**
     * Devuelve la afiliación si fue cargada desde DB. 
     * Nota: Puede ser null si el contexto viene de Redis.
     */
    public function affiliation(): ?Affiliation;

    /**
     * Información Geográfica (El Corazón de KUBIX).
     */
    public function getPath(): string;    // Ejemplo: "/1/5/10/"
    public function getDepth(): int;      // Ejemplo: 3
    public function getScope(): string;   // Para queries: "/1/5/10/%"

    /**
     * Resolutores de IDs (Extraídos del Path).
     */
    public function getAccountId(): ?int;
    public function getCompanyId(): ?int;
    public function getBranchId(): ?int;

    /**
     * Lógica de Negocio.
     */
    public function getRole(): string;
    public function hasSolution(string $slug): bool;
    public function getBranding(): array;
    
    /**
     * Verifica si el contexto tiene los datos mínimos para operar.
     * Reemplaza al antiguo hasActive().
     */
    public function isOperational(): bool;
}