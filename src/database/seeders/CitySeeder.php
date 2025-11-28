<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'Москва',            'slug' => 'moskva'],
            ['name' => 'Санкт-Петербург',   'slug' => 'sankt-peterburg'],
            ['name' => 'Казань',            'slug' => 'kazan'],
            ['name' => 'Новосибирск',       'slug' => 'novosibirsk'],
            ['name' => 'Екатеринбург',      'slug' => 'ekaterinburg'],
            ['name' => 'Нижний Новгород',   'slug' => 'nizhniy-novgorod'],
            ['name' => 'Челябинск',         'slug' => 'chelyabinsk'],
            ['name' => 'Самара',            'slug' => 'samara'],
            ['name' => 'Ростов-на-Дону',    'slug' => 'rostov-na-donu'],
            ['name' => 'Краснодар',         'slug' => 'krasnodar'],
            ['name' => 'Уфа',               'slug' => 'ufa'],
            ['name' => 'Пермь',             'slug' => 'perm'],
            ['name' => 'Воронеж',           'slug' => 'voronezh'],
            ['name' => 'Волгоград',         'slug' => 'volgograd'],
            ['name' => 'Красноярск',        'slug' => 'krasnoyarsk'],
        ];

        foreach ($rows as $i => $r) {
            City::updateOrCreate(
                ['slug' => $r['slug']],
                [
                    'name'   => $r['name'],
                    'slug'   => $r['slug'] ?: Str::slug($r['name']),
                    'sort'   => ($i + 1) * 10,
                    'active' => true,
                ]
            );
        }
    }
}
