<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('drivers.view');
    }
    public function view(User $user, Driver $driver): bool
    {
        return $user->can('drivers.view');
    }
    public function create(User $user): bool
    {
        return $user->can('drivers.create');
    }
    public function update(User $user, Driver $driver): bool
    {
        return $user->can('drivers.update');
    }
    public function delete(User $user, Driver $driver): bool
    {
        return $user->can('drivers.delete');
    }
    public function toggle(User $user, Driver $driver): bool
    {
        return $user->can('drivers.toggle');
    }
}
