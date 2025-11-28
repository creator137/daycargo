<?php

namespace Database\Seeders;

use App\Models\ClientTariff;
use App\Models\TariffGroup;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class ClientTariffSeeder extends Seeder
{
    public function run(): void
    {
        $group = TariffGroup::first();
        $vt    = VehicleType::first(); // возьмём любой класс для примера

        $rows = [
            ['name' => 'Грузовое такси (Москва)', 'city' => 'Москва', 'tariff_group_id' => optional($group)->id, 'vehicle_type_id' => optional($vt)->id, 'sort' => 10, 'active' => true],
            ['name' => 'Легковое такси (СПб)', 'city' => 'Санкт-Петербург', 'tariff_group_id' => optional($group)->id, 'vehicle_type_id' => optional($vt)->id, 'sort' => 20, 'active' => true],
        ];

        foreach ($rows as $r) {
            if ($r['vehicle_type_id']) {
                ClientTariff::firstOrCreate(
                    ['name' => $r['name']],
                    $r
                );
            }
        }
    }
}
