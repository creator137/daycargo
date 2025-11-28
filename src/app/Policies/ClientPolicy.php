<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('clients.view');
    }
    public function view(User $user, Client $c): bool
    {
        return $user->can('clients.view');
    }
    public function create(User $user): bool
    {
        return $user->can('clients.create');
    }
    public function update(User $user, Client $c): bool
    {
        return $user->can('clients.update');
    }
    public function delete(User $user, Client $c): bool
    {
        return $user->can('clients.delete');
    }
    public function toggle(User $user, Client $c): bool
    {
        return $user->can('clients.toggle');
    } // ЧС
}
