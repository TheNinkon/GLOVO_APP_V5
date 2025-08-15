<?php

namespace App\Providers;

use App\Models\Rider;          // <-- Añadir esta línea
use App\Policies\RiderPolicy;   // <-- Añadir esta línea
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Rider::class => RiderPolicy::class, // <-- Añadir esta línea
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
