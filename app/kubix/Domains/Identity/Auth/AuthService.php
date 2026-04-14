<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Auth;

use App\Kubix\Domains\Identity\Auth\DTO\RegisterDto;
use App\Kubix\Domains\Identity\Auth\DTO\LoginDto;
use App\Kubix\Domains\Identity\User\UserService;
use App\Kubix\Domains\Identity\Context\Contracts\ContextInterface;
use App\Kubix\Core\WebSocket\KubeLive;
use App\Models\User;
use App\Models\Territory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserService $userService,
        protected ContextInterface $context
    ) {}

    public function register(RegisterDto $dto): array
    {
        Log::info("KUBIX AUTH: Iniciando registro", ['email' => $dto->email]);

        // --- VALIDACIÓN DE TERRITORIO ---
        $territory = Territory::where('path', $dto->current_path)->first();
        if (!$territory) {
            Log::error("KUBIX AUTH: Registro fallido - Territorio inexistente", ['path' => $dto->current_path]);
            throw ValidationException::withMessages(['current_path' => ["El territorio especificado no es válido."]]);
        }

        return DB::transaction(function () use ($dto, $territory) {
            try {
                $result = $this->userService->register($dto);
                Log::info("KUBIX AUTH: Usuario creado", ['id' => $result['user']->id]);

                return [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'identities' => [
                        'workspace' => [],
                        'social' => $this->formatSocialIdentity($result['user'], $territory)
                    ],
                ];
            } catch (\Exception $e) {
                Log::critical("KUBIX AUTH: Error en transacción de registro", ['msg' => $e->getMessage()]);
                throw $e;
            }
        });
    }

    public function authenticate(LoginDto $dto): array
    {
        Log::info("KUBIX AUTH: Intento de login", ['email' => $dto->email]);

        // 1. Intento de Autenticación
        if (!Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            Log::warning("KUBIX AUTH: Credenciales incorrectas", ['email' => $dto->email]);
            throw ValidationException::withMessages(['email' => ['Las credenciales no coinciden con nuestros registros.']]);
        }

        /** @var User $user */
        $user = Auth::user();

        // 2. VALIDACIÓN ESTRICTA DE TERRITORIO EN LOGIN
        $territory = $this->validateTerritory($dto->current_path);

        try {
            $token = $user->createToken($dto->device_name ?? 'web-access')->plainTextToken;
            
            $workspaces = $this->buildAccessMap($user);
            $social = $this->formatSocialIdentity($user, $territory);

            // 3. Persistencia en Redis con su propio Try-Catch
            $this->safeRedisStorage($user, $workspaces, $social);

            // 4. Notificación WebSocket con su propio Try-Catch
            $this->safeBroadcast($user, $social);

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
                'identities' => [
                    'workspace' => $workspaces,
                    'social' => $social,
                ],
            ];

        } catch (\Exception $e) {
            Log::emergency("KUBIX AUTH: Fallo catastrófico en flujo de autenticación", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Valida que el territorio exista o busca el raíz.
     */
    protected function validateTerritory(?string $path): Territory
    {
        $territory = null;

        if ($path) {
            $territory = Territory::where('path', $path)->first();
        }

        // Si no se encuentra el solicitado, buscamos el raíz (parent_id null)
        if (!$territory) {
            $territory = Territory::whereNull('parent_id')->first();
        }

        // Si después de todo no hay nada, la DB está corrupta o vacía
        if (!$territory) {
            Log::emergency("KUBIX GEOGRAPHY: No se encontró ningún territorio válido (ni solicitado ni raíz).");
            throw new \RuntimeException("El sistema no tiene una base geográfica configurada.");
        }

        return $territory;
    }

    /**
     * Encapsula Redis para que un fallo de caché no rompa el login si no es deseado,
     * pero dejando logs muy claros del fallo.
     */
    protected function safeRedisStorage(User $user, array $workspaces, array $social): void
    {
        try {
            $cacheKey = "user_context:{$user->id}";
            $data = json_encode([
                'workspace' => $workspaces,
                'social' => $social,
                'mode' => count($workspaces) > 0 ? 'business' : 'nomad',
            ]);

            $stored = Redis::setex($cacheKey, 7200, $data);

            if (!$stored) {
                Log::warning("KUBIX REDIS: Redis respondió FALSE al intentar guardar contexto", ['user' => $user->id]);
            } else {
                Log::info("KUBIX REDIS: Gafete guardado exitosamente", ['user' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::error("KUBIX REDIS: Error de conexión", ['msg' => $e->getMessage()]);
            // Nota: Aquí podrías lanzar excepción si Redis es obligatorio para tu Middleware
        }
    }

    /**
     * Encapsula el Broadcast para que errores de red/socket no maten la respuesta del login.
     */
    protected function safeBroadcast(User $user, array $social): void
    {
        try {
            // Hidratamos el contexto actual para el anuncio
            $this->context->hydrateFromPayload($user, $social);
            
            KubeLive::announceOnline($user, $this->context);
            Log::info("KUBIX WS: Aviso 'UserOnline' disparado");
        } catch (\Exception $e) {
            Log::error("KUBIX WS ERROR: Fallo al emitir a través de KubeLive", ['msg' => $e->getMessage()]);
        }
    }

    public function logout(User $user): void
    {
        try {
            $user->tokens()->delete();
            Redis::del("user_context:{$user->id}");
            Log::info("KUBIX AUTH: Logout exitoso", ['user' => $user->id]);
        } catch (\Exception $e) {
            Log::error("KUBIX AUTH: Error parcial en Logout", ['msg' => $e->getMessage()]);
        }
    }

    protected function buildAccessMap(User $user): array
    {
        return $user->affiliations()
            ->where('is_active', true)
            ->with(['affiliatable'])
            ->get()
            ->map(fn($aff) => [
                'label'       => strtoupper($aff->affiliatable->name ?? 'SISTEMA'),
                'path'        => $aff->path,
                'role'        => $aff->role,
                'permissions' => $aff->permissions ?? [],
            ])->values()->toArray();
    }

    protected function formatSocialIdentity(User $user, Territory $territory): array
    {
        return [
            'label'       => "Ciudadano {$territory->name}",
            'path'        => $territory->path,
            'role'        => $user->getRoleNames()->first() ?? 'nomad',
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ];
    }
}