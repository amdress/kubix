<?php

namespace App\Kubix\Domains\Identity\User;

use App\Kubix\Domains\Identity\Auth\DTO\RegisterDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        protected UserRepository $users
    ) {}

    /**
     * Registro de usuario Nómada con contexto geográfico.
     */
    public function register(RegisterDto $dto): array
    {
        // 1. Crear el usuario en SQL
        $user = $this->users->create([
            'name'              => $dto->name,
            'email'             => $dto->email,
            'password'          => Hash::make($dto->password),
            'terms_accepted_at' => now(),
            'terms_version'     => '1.0',
            'last_login_at'     => now(),
            'last_login_ip'     => request()->ip(),
        ]);

        // 2. Asignar Rol Social (Spatie Permissions)
        $user->assignRole('nomad');

        // 3. Sincronizar Contexto a Redis (El Mercado)
        $this->syncNomadContext($user->id, $dto);

        // 4. Generar Token (Sanctum)
        $token = $user->createToken('kubix_access')->plainTextToken;

        Log::info("KUBIX Identity: Nuevo nómada registrado [{$user->id}] en path [{$dto->current_path}]");

        return [
            'user'  => $user,
            'token' => $token,
            'path'  => $dto->current_path
        ];
    }

    /**
     * Guarda el rastro del nómada para que el Middleware sepa qué branding mostrar.
     */
    private function syncNomadContext(int $userId, RegisterDto $dto): void
    {
        $context = [
            'user_id'      => $userId,
            'current_path' => $dto->current_path,
            'lat'          => $dto->lat,
            'lon'          => $dto->lon,
            'mode'         => 'nomad',
            'is_social'    => true,
            'updated_at'   => now()->toDateTimeString(),
        ];

        // TTL de 2 horas (7200 segundos) para la sesión del mercado
        Redis::setex("user_context:{$userId}", 7200, json_encode($context));
    }

    // Métodos estándar de gestión
    public function findById(int $id): ?User { return $this->users->findById($id); }
    public function update(User $user, array $attr): User { return $this->users->update($user, $attr); }
    public function delete(User $user): bool { return $this->users->delete($user); }
}