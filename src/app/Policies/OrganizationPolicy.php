<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Владелец имеет полный доступ (до любых проверок).
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('owner') ? true : null;
    }

    /**
     * Просмотр списка.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('organizations.view');
    }

    /**
     * Просмотр карточки.
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->can('organizations.view');
    }

    /**
     * Создание.
     */
    public function create(User $user): bool
    {
        return $user->can('organizations.create');
    }

    /**
     * Обновление.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->can('organizations.update');
    }

    /**
     * Удаление.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->can('organizations.delete');
    }

    /**
     * Вкл/Выкл организации.
     */
    public function toggle(User $user, Organization $organization): bool
    {
        return $user->can('organizations.toggle');
    }

    /**
     * Операции по балансу (пополнение/списание).
     */
    public function balance(User $user, Organization $organization): bool
    {
        return $user->can('organizations.balance');
    }

    /**
     * Управление сотрудниками (привязки клиентов к организации).
     */
    public function employees(User $user, Organization $organization): bool
    {
        return $user->can('organizations.employees');
    }
}
