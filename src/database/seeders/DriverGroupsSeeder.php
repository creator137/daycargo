<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DriverGroup;
use App\Models\VehicleType;
use App\Models\ClientTariff;

class DriverGroupsSeeder extends Seeder
{
    public function run(): void
    {
        // Подтянем типы авто (по коду), если кодов нет — возьмём любой первый
        $vtByCode = VehicleType::all()->keyBy('code'); // S/M/L/XL/XXL по твоему сидеру
        $fallback = VehicleType::orderBy('capacity_kg')->first();

        if (! $fallback) {
            $this->command?->warn('⚠️ Нет VehicleType — сначала прогоните сидер VehicleTypeSeeder');
            return;
        }

        // Возьмём клиентские тарифы, если уже созданы (иначе прикреплять будет нечего)
        $allClientTariffs = ClientTariff::orderBy('sort')->pluck('id')->all();

        $rows = [
            [
                'name'        => 'Грузовые — Москва',
                'city'        => 'Москва',
                'profession'  => 'Грузовое такси',
                'vt_code'     => 'L',     // если нет L — станет $fallback
                'priority'    => 5,
                'sort'        => 10,
                'description' => 'Основная группа для Москвы',
                'active'      => true,
                'attach'      => 'all',   // all | first2 | none
            ],
            [
                'name'        => 'Грузовые — СПб',
                'city'        => 'Санкт-Петербург',
                'profession'  => 'Грузовое такси',
                'vt_code'     => 'M',
                'priority'    => 10,
                'sort'        => 20,
                'description' => 'Группа для СПб',
                'active'      => true,
                'attach'      => 'first2',
            ],
            [
                'name'        => 'Переезды — Москва',
                'city'        => 'Москва',
                'profession'  => 'Переезды',
                'vt_code'     => 'XL',
                'priority'    => 7,
                'sort'        => 30,
                'description' => 'Спецгруппа для переездов',
                'active'      => true,
                'attach'      => 'none',
            ],
        ];

        foreach ($rows as $r) {
            $vehicleType = $vtByCode[$r['vt_code']] ?? $fallback;

            $group = DriverGroup::updateOrCreate(
                ['name' => $r['name']],
                [
                    'city'            => $r['city'],
                    'profession'      => $r['profession'],
                    'vehicle_type_id' => $vehicleType->id,
                    'priority'        => $r['priority'],
                    'sort'            => $r['sort'],
                    'description'     => $r['description'],
                    'active'          => (bool) $r['active'],
                ]
            );

            // Привязка клиентских тарифов (если есть)
            $idsToAttach = [];
            if (!empty($allClientTariffs)) {
                if ($r['attach'] === 'all') {
                    $idsToAttach = $allClientTariffs;
                } elseif ($r['attach'] === 'first2') {
                    $idsToAttach = array_slice($allClientTariffs, 0, 2);
                } // 'none' — пусто
            }

            $group->clientTariffs()->sync($idsToAttach);

            $this->command?->info("✔ Группа «{$group->name}» сохранена. Тарифы: " . (count($idsToAttach) ?: 'нет'));
        }
    }
}
