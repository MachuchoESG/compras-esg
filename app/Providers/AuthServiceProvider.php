<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Cotizacion;
use App\Models\Requisicion;
use App\Policies\CotizacionPolicy;
use App\Policies\RequisicionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Requisicion::class => RequisicionPolicy::class,
        Cotizacion::class => CotizacionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
