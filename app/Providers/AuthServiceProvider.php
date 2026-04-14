<?php
namespace App\Providers;

// Modelos y Policies
use App\Models\Branch;
use App\Modules\Branch\BranchPolicy;

// Modelos
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Policy modular
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    // /**
    //  * Mapeo de modelos a policies.
    //  *
    //  * @var array<class-string, class-string>
    //  */
    // protected $policies = [
    //     Branch::class => BranchPolicy::class,
    // ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();

        // /**
        //  * Superadmin global
        //  * Si el usuario tiene rol superadmin, pasa todas las Policies automáticamente
        //  */
        // Gate::before(function ($user, $ability) {
        //     return $user->hasRole('superadmin') ? true : null;
        // });
    }
}
