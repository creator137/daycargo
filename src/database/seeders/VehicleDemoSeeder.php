<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $vtSmall = VehicleType::where('code', 'S')->first() ?? VehicleType::orderBy('capacity_kg')->first();
        $vtLarge = VehicleType::orderByDesc('capacity_kg')->first();

        $driver1 = Driver::first();
        $driver2 = Driver::skip(1)->first();

        $data = [
            [
                'city' => 'Москва',
                'vehicle_type_id' => $vtSmall?->id,
                'driver_id' => $driver1?->id,
                'owner_type' => 'private',
                'is_rent' => false,
                'brand' => 'Hyundai',
                'model' => 'Solaris',
                'year' => 2021,
                'color' => 'Белый',
                'license_plate' => 'А123ВС777',
                'vin' => null,
                'photo_path' => null,
                'options' => ['child_seat' => false, 'wagon' => false],
                'status' => 'active',
                'comment' => 'Городской легковой',
            ],
            [
                'city' => 'Санкт-Петербург',
                'vehicle_type_id' => $vtLarge?->id,
                'driver_id' => $driver2?->id,
                'owner_type' => 'company',
                'is_rent' => false,
                'brand' => 'Газель',
                'model' => 'Некст',
                'year' => 2019,
                'color' => 'Серый',
                'license_plate' => 'В456НЕ178',
                'vin' => null,
                'photo_path' => null,
                'options' => ['tent' => true, 'board' => true],
                'status' => 'active',
                'comment' => 'Грузовой',
            ],
            [
                'city' => 'Москва',
                'vehicle_type_id' => $vtLarge?->id,
                'driver_id' => null,
                'owner_type' => 'rent',
                'is_rent' => true,
                'brand' => 'Ford',
                'model' => 'Transit',
                'year' => 2018,
                'color' => 'Синий',
                'license_plate' => 'С789КХ777',
                'vin' => null,
                'photo_path' => null,
                'options' => ['refrigerator' => true],
                'status' => 'pending',
                'comment' => 'Ожидает проверки',
            ],
        ];

        foreach ($data as $row) {
            Vehicle::updateOrCreate(
                ['license_plate' => $row['license_plate']],
                $row
            );
        }
    }
}
