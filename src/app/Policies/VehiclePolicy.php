<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\HandlesAuthorization;

class VehiclePolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('owner')) return true;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('vehicles.view');
    }
    public function view(User $user, Vehicle $v): bool
    {
        return $user->can('vehicles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('vehicles.create');
    }
    public function update(User $user, Vehicle $v): bool
    {
        return $user->can('vehicles.update');
    }
    public function delete(User $user, Vehicle $v): bool
    {
        return $user->can('vehicles.delete');
    }

    public function toggle(User $user, Vehicle $v): bool
    {
        return $user->can('vehicles.toggle');
    }
}
