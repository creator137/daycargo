<?php

namespace App\Policies;

use App\Models\CancelReason;
use App\Models\User;

class CancelReasonPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('dicts.cancel_reasons.view');
    }
    public function view(User $u, CancelReason $m): bool
    {
        return $u->can('dicts.cancel_reasons.view');
    }
    public function create(User $user): bool
    {
        return $user->can('dicts.cancel_reasons.create');
    }
    public function update(User $u, CancelReason $m): bool
    {
        return $u->can('dicts.cancel_reasons.update');
    }
    public function delete(User $u, CancelReason $m): bool
    {
        return $u->can('dicts.cancel_reasons.delete');
    }
    public function toggle(User $u, CancelReason $m): bool
    {
        return $u->can('dicts.cancel_reasons.toggle');
    }
}
