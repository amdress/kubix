<?php

declare (strict_types = 1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * ============================================================================
 * AppServiceProvider — Proveedor Principal de la Aplicación
 * ============================================================================
 *
 * Punto central de configuración de Laravel. En Laravel 11 este provider
 * reemplaza y consolida lo que antes estaba disperso en:
 *   - AuthServiceProvider   → Policies y Gate
 *   - RouteServiceProvider  → Rate Limiters
 *
 * Responsabilidades:
 *   register() → binding del Service Container (singletons, interfaces)
 *   boot()     → lógica que corre después de registrar todos los providers
 * ============================================================================
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * ── BINDINGS DEL CONTAINER ───────────────────────────────────────────────
     *
     * Los tres contextos del sistema se registran como singletons para que
     * el ContextHandler hidrate la MISMA instancia que consumen Controllers,
     * Orquestadores y Policies vía inyección de dependencias.
     *
     * Sin singleton: cada inyección crea una instancia nueva vacía
     * y el contexto hidratado por el middleware se pierde.
     */
    public function register(): void
    {
        // Un solo binding para todo el contexto del sistema
        $this->app->singleton(
            \App\Kubix\Domains\Identity\Context\Contracts\ContextInterface::class,
            \App\Kubix\Domains\Identity\Context\Services\KubixContextService::class
        );
    }

    /**
     * ── ARRANQUE DE SERVICIOS ────────────────────────────────────────────────
     *
     * Todo lo que necesita que el container ya esté listo:
     * Policies, Gate, Rate Limiters.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGate();
        $this->registerRateLimiters();
    }

    /**
     * ── POLICIES ─────────────────────────────────────────────────────────────
     *
     * Mapeo de Models → Policies.
     * Agregar aquí cada Policy nueva del sistema.
     *
     * Ejemplo:
     *   Gate::policy(Branch::class, BranchPolicy::class);
     *   Gate::policy(Company::class, CompanyPolicy::class);
     */
    protected function registerPolicies(): void
    {
        // Gate::policy(Branch::class, BranchPolicy::class);
    }

    /**
     * ── GATE ─────────────────────────────────────────────────────────────────
     *
     * Gate::before() corre ANTES de cualquier Policy.
     * Si retorna true  → acceso garantizado sin evaluar la Policy.
     * Si retorna null  → Laravel continúa evaluando la Policy normalmente.
     *
     * SuperAdmin bypasea todas las Policies del sistema.
     * El rol 'superadmin' se gestiona vía Spatie Permission.
     */
    protected function registerGate(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
    }

    /**
     * ── RATE LIMITERS ────────────────────────────────────────────────────────
     *
     * Define los límites de requests por minuto para cada grupo de rutas.
     *
     * 'api': 60 requests/minuto por usuario autenticado (por ID),
     *        o por IP si el request es anónimo.
     */
    protected function registerRateLimiters(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
