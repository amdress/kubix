<?php

declare(strict_types=1);

namespace App\Kubix\Core\WebSocket\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserOnlineEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public array $userData,
        public string $path
    ) {
        // --- 📡 LOG: Datos listos para volar ---
        Log::debug("WS Event [UserOnline]: Paquete construido", [
            'uid'  => $userData['id'] ?? 'N/A',
            'path' => $path
        ]);
    }

    public function broadcastOn(): PrivateChannel
    {
        // Limpiamos el path para el nombre del canal (ej: territory.1.5.10)
        $channelName = str_replace('/', '.', trim($this->path, '/'));
        return new PrivateChannel("territory.{$channelName}");
    }

    public function broadcastAs(): string
    {
        return 'user.online';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => $this->userData,
            'at'   => now()->toDateTimeString(),
            'path' => $this->path,
        ];
    }
}