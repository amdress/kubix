<?php

declare(strict_types=1);

namespace App\Kubix\Core\WebSocket;

use App\Kubix\Core\WebSocket\Events\UserOnlineEvent;
use App\Kubix\Domains\Identity\Context\Contracts\ContextInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class KubeLive
{
    /**
     * Notifica la presencia de un usuario .
     */
    public static function announceOnline(User $user, ContextInterface $context): void
    {
        // --- 🚀 LOG: Inicio de transmisión ---
        Log::info("KubeLive: Iniciando anuncio de Usuario Online", [
            'name' => $user->name,
            'path' => $context->getPath()
        ]);

        try {
            event(new UserOnlineEvent([
                'id'     => $user->id,
                'name'   => $user->name,
                'role'   => $context->getRole(),
                'avatar' => $user->avatar_url ?? null,
            ], $context->getPath()));
        } catch (\Exception $e) {
            // Log de error para que el sistema siga funcionando aunque falle el WS
            Log::error("KubeLive Critical: Error al emitir evento", [
                'msg' => $e->getMessage()
            ]);
        }
    }

    

}