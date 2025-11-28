<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsPatchForDriversSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        $newPerms = [
            'drivers.view',
            'drivers.create',
            'drivers.update',
            'drivers.delete',
            'drivers.toggle',
        ];

        foreach ($newPerms as $p) {
            Permission::findOrCreate($p, $guard);
        }

        if ($owner = Role::where('name', 'owner')->first()) {
            $owner->givePermissionTo($newPerms);
        }
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo($newPerms);
        }
        if ($viewer = Role::where('name', 'viewer')->first()) {
            $viewer->givePermissionTo('drivers.view');
        }
        if ($accountant = Role::where('name', 'accountant')->first()) {
            $accountant->givePermissionTo('drivers.view');
        }
        if ($driver = Role::where('name', 'driver')->first()) {
            $driver->givePermissionTo('drivers.view');
        }
    }
}
