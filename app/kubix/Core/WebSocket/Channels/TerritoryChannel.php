<?php

declare(strict_types=1);

namespace App\Kubix\Core\WebSocket\Channels;

use App\Models\User;
use App\Kubix\Domains\Identity\Context\Contracts\ContextInterface;
use Illuminate\Support\Facades\Log;

class TerritoryChannel
{
    public function join(User $user, string $path): bool
    {
        // Convertimos el path del canal (1-5-10) a formato KUBIX (/1/5/10/)
        $requestedPath = '/' . str_replace('-', '/', $path) . '/';
        
        $context = app(ContextInterface::class);
        $userPath = $context->getPath();

        // El usuario solo puede entrar si su path es "padre" o igual al solicitado
        $isAuthorized = str_starts_with($requestedPath, $userPath);

        // --- 🛡️ LOG: Seguridad de Canal ---
        Log::info("WS Auth: Validación de territorio", [
            'user_id'    => $user->id,
            'user_path'  => $userPath,
            'target'     => $requestedPath,
            'result'     => $isAuthorized ? 'PERMITIDO' : 'DENEGADO'
        ]);

        return $isAuthorized;
    }
}