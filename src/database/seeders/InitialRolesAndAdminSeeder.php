<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitialRolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // базовые роли
        $roles = ['owner', 'admin', 'accountant', 'viewer', 'driver'];
        foreach ($roles as $r) {
            Role::findOrCreate($r, 'web'); // guard 'web' по умолчанию
        }

        // первый владелец
        $user = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'), // поменяешь позже
            ]
        );

        $user->syncRoles(['owner']);
    }
}
