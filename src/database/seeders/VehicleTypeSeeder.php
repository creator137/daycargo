<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'S',   'name' => 'S (до 300 кг, 170×100×90)',   'length_cm' => 170, 'width_cm' => 100, 'height_cm' => 90,  'capacity_kg' => 300],
            ['code' => 'M',   'name' => 'M (до 700 кг, 260×130×150)',  'length_cm' => 260, 'width_cm' => 130, 'height_cm' => 150, 'capacity_kg' => 700],
            ['code' => 'L',   'name' => 'L (до 1400 кг, 380×180×180)', 'length_cm' => 380, 'width_cm' => 180, 'height_cm' => 180, 'capacity_kg' => 1400],
            ['code' => 'XL',  'name' => 'XL (до 2000 кг, 400×190×200)', 'length_cm' => 400, 'width_cm' => 190, 'height_cm' => 200, 'capacity_kg' => 2000],
            ['code' => 'XXL', 'name' => 'XXL (до 4000 кг, 500×200×200)', 'length_cm' => 500, 'width_cm' => 200, 'height_cm' => 200, 'capacity_kg' => 4000],
        ];

        foreach ($rows as $r) {
            VehicleType::updateOrCreate(['code' => $r['code']], $r);
        }
    }
}
