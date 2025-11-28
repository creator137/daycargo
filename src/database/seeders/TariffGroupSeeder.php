<?php

namespace Database\Seeders;

use App\Models\TariffGroup;
use Illuminate\Database\Seeder;

class TariffGroupSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Грузовое такси',  'sort' => 10,  'active' => true,  'description' => null],
            ['name' => 'Легковое такси',  'sort' => 20,  'active' => true,  'description' => null],
            ['name' => 'Корпоративные',   'sort' => 30,  'active' => true,  'description' => 'Тарифы для юр.лиц'],
        ];

        foreach ($rows as $r) {
            TariffGroup::firstOrCreate(['name' => $r['name']], $r);
        }
    }
}
