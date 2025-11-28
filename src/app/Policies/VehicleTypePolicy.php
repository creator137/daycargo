<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleType;

class VehicleTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('dicts.vehicle_types.view');
    }
    public function view(User $u, VehicleType $m): bool
    {
        return $u->can('dicts.vehicle_types.view');
    }
    public function create(User $user): bool
    {
        return $user->can('dicts.vehicle_types.create');
    }
    public function update(User $u, VehicleType $m): bool
    {
        return $u->can('dicts.vehicle_types.update');
    }
    public function delete(User $u, VehicleType $m): bool
    {
        return $u->can('dicts.vehicle_types.delete');
    }
    public function toggle(User $u, VehicleType $m): bool
    {
        return $u->can('dicts.vehicle_types.toggle');
    }
}
