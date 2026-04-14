<?php

declare(strict_types=1);

namespace App\Kubix\Domains\Identity\Context\Middleware;

use App\Kubix\Domains\Identity\Context\Contracts\ContextInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class KubixContextHandler
{
    public function __construct(
        protected ContextInterface $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Sin usuario no hay gafete
        if (!$user) {
            return $next($request);
        }

        $requestedPath = $request->header('X-Context-Path');

        // 1. EL ASISTENTE BUSCA EL GAFETE (REDIS)
        // Ya no preguntamos a la base de datos, buscamos el JSON que guardó el AuthService
        $cachedContext = Redis::get("user_context:{$user->id}");

        if ($cachedContext) {
            $payload = json_decode($cachedContext, true);

            // 2. RESOLVER EL MODO (SOCIAL O BUSINESS)
            // Buscamos dentro del gafete si Juan tiene permiso para el pasillo que pide
            $identity = $this->resolveIdentityFromPayload($payload, $requestedPath);

            if ($identity) {
                // HIDRATAMOS EL SERVICIO (El cerebro ya no necesita modelos SQL)
                $this->context->hydrateFromPayload($user, $identity);

                Log::info("KUBIX Context: Gafete validado desde REDIS", [
                    'user_id' => $user->id,
                    'path'    => $this->context->getPath(),
                    'role'    => $this->context->getRole()
                ]);
            } else {
                Log::warning("KUBIX Context: Juan intentó entrar a un pasillo sin llave", [
                    'user_id' => $user->id,
                    'path_intentado' => $requestedPath
                ]);
                return response()->json(['error' => 'No tienes permiso para este territorio.'], 403);
            }
        } else {
            // 3. FALLBACK (Plan de Rescate)
            // Si Redis falló o expiró, aquí podrías mandar a re-login o reconstruir.
            Log::error("KUBIX Context: Gafete no encontrado en Redis para User #{$user->id}");
            return response()->json(['error' => 'Sesión de contexto expirada.'], 401);
        }

        // Lógica de Onboarding (Se mantiene igual)
        if (!$this->context->isOperational() && $this->isOperationalRoute($request)) {
            $request->attributes->set('onboarding_required', true);
        }

        return $next($request);
    }

    /**
     * Busca en el mapa de identidades del JSON de Redis el path solicitado.
     */
    protected function resolveIdentityFromPayload(array $payload, ?string $requestedPath): ?array
    {
        // Si no pide path, por defecto le damos su identidad Social (Nómada)
        if (!$requestedPath) {
            return $payload['social'];
        }

        // Si pide un path, verificamos si es su Social o uno de sus Workspaces
        if ($payload['social']['path'] === $requestedPath) {
            return $payload['social'];
        }

        // Buscamos en el array de Workspaces
        return collect($payload['workspace'])->firstWhere('path', $requestedPath);
    }

    protected function isOperationalRoute(Request $request): bool
    {
        return $request->is('api/v1/solutions/*') || $request->is('api/v1/dashboard/*');
    }
}