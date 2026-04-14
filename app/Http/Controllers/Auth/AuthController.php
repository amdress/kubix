<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Kubix\Domains\Identity\Auth\AuthService;
use App\Kubix\Domains\Identity\Auth\DTO\RegisterDto;
use App\Kubix\Domains\Identity\Auth\DTO\LoginDto;
use App\Kubix\Core\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginDto $dto): JsonResponse
    {
        // LaravelData valida automáticamente. Si falla, lanza 422.
        $result = $this->authService->authenticate($dto);
        return $this->successResponse($result, 'Login exitoso');
    }

    public function register(RegisterDto $dto): JsonResponse
    {
        $result = $this->authService->register($dto);
        return $this->successResponse($result, '¡Bienvenido al mercado de KUBIX!', 201);
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();
        if ($user) {
            $this->authService->logout($user);
            return $this->successResponse(null, 'Sesión cerrada');
        }
        return $this->errorResponse('No autenticado', 401);
    }
}