<?php

namespace App\Policies;

use App\Models\DriverGroup;
use App\Models\User;

class DriverGroupPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->can('driver_groups.view');
    }
    public function view(User $u, DriverGroup $m): bool
    {
        return $u->can('driver_groups.view');
    }
    public function create(User $u): bool
    {
        return $u->can('driver_groups.create');
    }
    public function update(User $u, DriverGroup $m): bool
    {
        return $u->can('driver_groups.update');
    }
    public function delete(User $u, DriverGroup $m): bool
    {
        return $u->can('driver_groups.delete');
    }
    public function toggle(User $u, DriverGroup $m): bool
    {
        return $u->can('driver_groups.toggle');
    }
}
