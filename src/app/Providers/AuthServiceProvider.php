<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected \$policies = [
        \\App\\Models\\Tariff::class => \\App\\Policies\\TariffPolicy::class,
        \\App\\Models\\VehicleType::class => \\App\\Policies\\VehicleTypePolicy::class,
        \\App\\Models\\CancelReason::class => \\App\\Policies\\CancelReasonPolicy::class,
        \\App\\Models\\TariffGroup::class => \\App\\Policies\\TariffGroupPolicy::class,
        \\App\\Models\\ClientTariff::class => \\App\\Policies\\ClientTariffPolicy::class,
        \\App\\Models\\DriverGroup::class => \\App\\Policies\\DriverGroupPolicy::class,
        \\App\\Models\\Driver::class => \\App\\Policies\\DriverPolicy::class,
        \\App\\Models\\City::class => \\App\\Policies\\CityPolicy::class,
        \\App\\Models\\Client::class => \\App\\Policies\\ClientPolicy::class,
        \\App\\Models\\Organization::class => \\App\\Policies\\OrganizationPolicy::class,
        \\App\\Models\\Vehicle::class => \\App\\Policies\\VehiclePolicy::class,
        \\App\\Models\\Order::class => \\App\\Policies\\OrderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        \$this->registerPolicies();

        // Суперправа владельца
        Gate::before(function (\$user, \$ability) {
            return \$user->hasRole(owner) ? true : null;
        });
    }
}
