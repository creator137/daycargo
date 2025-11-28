<?php

namespace App\Policies;

use App\Models\ClientTariff;
use App\Models\User;

class ClientTariffPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->can('client_tariffs.view');
    }
    public function view(User $u, ClientTariff $m): bool
    {
        return $u->can('client_tariffs.view');
    }
    public function create(User $u): bool
    {
        return $u->can('client_tariffs.create');
    }
    public function update(User $u, ClientTariff $m): bool
    {
        return $u->can('client_tariffs.update');
    }
    public function delete(User $u, ClientTariff $m): bool
    {
        return $u->can('client_tariffs.delete');
    }
    public function toggle(User $u, ClientTariff $m): bool
    {
        return $u->can('client_tariffs.toggle');
    }
}
