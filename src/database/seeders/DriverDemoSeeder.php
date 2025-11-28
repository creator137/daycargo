<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Driver;
use App\Models\DriverGroup;
use App\Models\VehicleType;
use App\Models\Tariff;

class DriverDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Города берём из тарифов, либо подставим дефолтные
        $cities = Tariff::query()
            ->whereNotNull('city')->where('city', '<>', '')
            ->distinct()->pluck('city')->all();
        if (!$cities) {
            $cities = ['Москва', 'Санкт-Петербург', 'Казань'];
        }

        // Базовый тип авто (если нет — создадим минимальный)
        $vt = VehicleType::orderBy('capacity_kg')->first();
        if (!$vt) {
            $vt = VehicleType::firstOrCreate(
                ['code' => 'S'],
                [
                    'name'        => 'Малый',
                    'capacity_kg' => 500,
                    'length_cm'   => 200,
                    'width_cm'    => 150,
                    'height_cm'   => 120,
                    'sort'        => 100,
                    'active'      => true,
                ]
            );
        }

        // Группа водителей (если нет — создадим)
        $dg = DriverGroup::first() ?? DriverGroup::create([
            'name'            => 'Группа S',
            'vehicle_type_id' => $vt->id,
            'priority'        => 10,
            'sort'            => 100,
            'active'          => true,
        ]);

        // Общий тестовый пароль для входа в приложение/ЛК
        $hashed = Hash::make('driver123');

        $rows = [
            [
                'full_name'             => 'Иван Петров',
                'callsign'              => '1001',
                'status'                => 'active',
                'vehicle_type_id'       => $vt->id,
                'driver_group_id'       => $dg->id,
                'supports_terminal'     => true,
                'phone'                 => '+79990000001',
                'email'                 => 'driver1@example.com',
                'birth_date'            => Carbon::parse('1990-05-10'),
                'main_city'             => $cities[0],
                'cities'                => array_values(array_unique([$cities[0], $cities[1] ?? $cities[0]])),
                'partner_name'          => 'ООО Ромашка',
                'payout_card'           => '5559000011112222',
                'payout_first_name_en'  => 'Ivan',
                'payout_last_name_en'   => 'Petrov',
                'yandex_wallet'         => null,
                'sms_fixed_code'        => null,
                'sort'                  => 100,
                'comment'               => 'Демо водитель',
                'app_password'          => $hashed,
                'avatar_path'           => null,
                'updated_by'            => null,
                'balance'               => 0,
            ],
            [
                'full_name'             => 'Сергей Кузнецов',
                'callsign'              => '1002',
                'status'                => 'blocked',
                'vehicle_type_id'       => $vt->id,
                'driver_group_id'       => $dg->id,
                'supports_terminal'     => false,
                'phone'                 => '+79990000002',
                'email'                 => 'driver2@example.com',
                'birth_date'            => Carbon::parse('1987-03-22'),
                'main_city'             => $cities[1] ?? $cities[0],
                'cities'                => array_values(array_unique([$cities[1] ?? $cities[0], $cities[0]])),
                'partner_name'          => null,
                'payout_card'           => '5559000099998888',
                'payout_first_name_en'  => 'Sergey',
                'payout_last_name_en'   => 'Kuznetsov',
                'yandex_wallet'         => null,
                'sms_fixed_code'        => '4321',
                'sort'                  => 100,
                'comment'               => 'Заблокирован для примера',
                'app_password'          => $hashed,
                'avatar_path'           => null,
                'updated_by'            => null,
                'balance'               => -350.50,
            ],
            [
                'full_name'             => 'Алексей Смирнов',
                'callsign'              => '1003',
                'status'                => 'pending',
                'vehicle_type_id'       => $vt->id,
                'driver_group_id'       => $dg->id,
                'supports_terminal'     => true,
                'phone'                 => '+79990000003',
                'email'                 => 'driver3@example.com',
                'birth_date'            => Carbon::parse('1995-09-01'),
                'main_city'             => $cities[2] ?? $cities[0],
                'cities'                => [$cities[2] ?? $cities[0]],
                'partner_name'          => 'ИП Смирнов',
                'payout_card'           => '5559000077776666',
                'payout_first_name_en'  => 'Alexey',
                'payout_last_name_en'   => 'Smirnov',
                'yandex_wallet'         => null,
                'sms_fixed_code'        => null,
                'sort'                  => 120,
                'comment'               => null,
                'app_password'          => $hashed,
                'avatar_path'           => null,
                'updated_by'            => null,
                'balance'               => 0,
            ],
        ];

        foreach ($rows as $row) {
            $driver = Driver::firstOrNew(['email' => $row['email']]);
            $driver->fill($row);
            $driver->save();
        }
    }
}
