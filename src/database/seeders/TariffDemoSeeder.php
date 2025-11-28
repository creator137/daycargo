<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tariff;
use App\Models\VehicleType;

class TariffDemoSeeder extends Seeder
{
    public function run(): void
    {
        $types = VehicleType::orderBy('capacity_kg')->get();

        foreach ($types as $t) {
            // Примеры цифр: масштабируем базовые от грузоподъёмности
            $factor = max(1, min(4, round($t->capacity_kg / 700, 1))); // 1…4

            $data = [
                'vehicle_type_id' => $t->id,
                'scope_type'      => 'global',   // общий тариф
                'scope_id'        => null,       // не привязан к клиенту/интеграции
                'city'            => null,       // общий (без города)
                'base_price'      => 400 * $factor,
                'per_km'          => 20 * $factor,
                'per_min'         => 3 * $factor,
                'min_price'       => 500 * $factor,
                'wait_free_min'   => 10,
                'wait_per_min'    => 6 * $factor,
                'active'          => true,
            ];

            // чтобы не плодить дублей — обновляем/создаём
            Tariff::updateOrCreate(
                [
                    'vehicle_type_id' => $t->id,
                    'scope_type'      => 'global',
                    'scope_id'        => null,
                    'city'            => null,
                ],
                $data
            );

            // При желании можно раскомментировать — добавит пример для Москвы
            /*
            Tariff::updateOrCreate(
                [
                    'vehicle_type_id' => $t->id,
                    'scope_type'      => 'global',
                    'scope_id'        => null,
                    'city'            => 'Москва',
                ],
                array_merge($data, ['city' => 'Москва', 'base_price' => $data['base_price'] * 1.1])
            );
            */
        }
    }
}
