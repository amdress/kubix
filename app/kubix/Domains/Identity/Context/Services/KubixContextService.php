<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Context\Services;

use App\Kubix\Domains\Identity\Context\Contracts\ContextInterface;
use App\Models\User;
use App\Models\Affiliation;
use Illuminate\Support\Facades\Config;

class KubixContextService implements ContextInterface
{
    protected ?User $user = null;
    protected ?Affiliation $affiliation = null;
    protected array $pathIds = [];
    
    /**
     * Datos crudos del contexto (cuando venimos de Redis)
     */
    protected array $payload = [];

    /**
     * HIDRATACIÓN SQL: Se usa en el Login/Register inicial.
     */
    public function hydrate(User $user, Affiliation $affiliation): void
    {
        $this->user = $user;
        $this->affiliation = $affiliation;
        $this->setPathIds($affiliation->path);
    }

    /**
     * HIDRATACIÓN REDIS: El Middleware usa esto para evitar ir a la DB.
     */
    public function hydrateFromPayload(User $user, array $payload): void
    {
        $this->user = $user;
        $this->payload = $payload;
        $this->setPathIds($payload['path'] ?? '/');
    }

    /**
     * Descompone el path "/1/5/10/" en [1, 5, 10]
     */
    protected function setPathIds(string $path): void
    {
        $this->pathIds = array_values(array_filter(explode('/', $path)));
    }

    public function user(): ?User 
    {
        return $this->user;
    }

    public function affiliation(): ?Affiliation 
    {
        // Nota: Puede ser null si venimos de Redis
        return $this->affiliation;
    }

    public function getPath(): string 
    {
        return $this->payload['path'] ?? $this->affiliation?->path ?? '/';
    }

    public function getDepth(): int 
    {
        // Si es de Redis, calculamos el depth por la cantidad de IDs en el path
        return isset($this->payload['path']) 
            ? count($this->pathIds) 
            : ($this->affiliation?->depth ?? 0);
    }

    public function getScope(): string 
    {
        return $this->getPath() . '%';
    }

    public function getAccountId(): ?int 
    {
        return isset($this->pathIds[0]) ? (int)$this->pathIds[0] : null;
    }

    public function getCompanyId(): ?int 
    {
        return isset($this->pathIds[1]) ? (int)$this->pathIds[1] : null;
    }

    public function getBranchId(): ?int  
    {
        return isset($this->pathIds[2]) ? (int)$this->pathIds[2] : null;
    }

    public function getRole(): string 
    {
        return $this->payload['role'] ?? $this->affiliation?->role ?? 'guest';
    }

    public function isOperational(): bool 
    {
        // Es operacional si tiene usuario y algún path definido
        return $this->user !== null && ($this->affiliation !== null || !empty($this->payload));
    }

    public function getBranding(): array
    {
        return Config::get('branding.default', [
            'name'          => 'Kubix Platform',
            'primary_color' => '#1e3a8a',
            'logo'          => null,
        ]);
    }

    public function hasSolution(string $slug): bool
    {
        // 1. Si venimos de Redis, las soluciones ya deberían estar en el payload (más rápido)
        if (isset($this->payload['permissions'])) {
            return in_array($slug, $this->payload['permissions']);
        }

        // 2. Fallback a la base de datos si es la primera vez (Login)
        if (!$this->affiliation) {
            return false;
        }

        return $this->affiliation->affiliatable
            ?->solutions()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->exists() ?? false;
    }
}