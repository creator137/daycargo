<?php

namespace App\Policies;

use App\Models\TariffGroup;
use App\Models\User;

class TariffGroupPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->can('tariff_groups.view');
    }
    public function view(User $u, TariffGroup $m): bool
    {
        return $u->can('tariff_groups.view');
    }
    public function create(User $u): bool
    {
        return $u->can('tariff_groups.create');
    }
    public function update(User $u, TariffGroup $m): bool
    {
        return $u->can('tariff_groups.update');
    }
    public function delete(User $u, TariffGroup $m): bool
    {
        return $u->can('tariff_groups.delete');
    }
    public function toggle(User $u, TariffGroup $m): bool
    {
        return $u->can('tariff_groups.toggle');
    }
}
