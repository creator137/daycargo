<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            InitialRolesAndAdminSeeder::class,
            VehicleTypeSeeder::class,
            TariffDemoSeeder::class,
            DriverGroupsSeeder::class,
            DriverDemoSeeder::class,
            RolesAndPermissionsSeeder::class,
            CitySeeder::class,
            ClientsDemoSeeder::class,
            OrganizationsDemoSeeder::class,
            VehicleDemoSeeder::class,
            LoyaltyDemoSeeder::class,
        ]);

        // Если нужен тестовый пользователь — можешь вернуть фабрику ниже:
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
