<?php

namespace App\Policies;

use App\Models\Tariff;
use App\Models\User;

class TariffPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tariffs.view');
    }

    public function view(User $user, Tariff $tariff): bool
    {
        return $user->can('tariffs.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tariffs.create');
    }

    public function update(User $user, Tariff $tariff): bool
    {
        return $user->can('tariffs.update');
    }

    public function delete(User $user, Tariff $tariff): bool
    {
        return $user->can('tariffs.delete');
    }

    /** Нестандартное действие: включение/выключение тарифа */
    public function toggle(User $user, Tariff $tariff): bool
    {
        return $user->can('tariffs.toggle');
    }
}
