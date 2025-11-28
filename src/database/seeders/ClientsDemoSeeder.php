<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ClientsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Используем транзакцию, чтобы было атомарно и быстро
        DB::transaction(function () {
            $rows = [
                [
                    'city'               => 'Москва',
                    'client_type'        => 'person', // физ.лицо
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'Иванов Иван',
                    'birth_date'         => '1990-03-14',
                    'phone'              => '+79990000001',
                    'email'              => 'ivanov@example.com',
                    'passport_series'    => '4004',
                    'passport_number'    => '123456',
                    'photo_path'         => null,
                    'comment'            => 'Лояльный клиент',
                    'send_trip_report'   => true,
                    'news_notifications' => true,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 0,
                    'balance'            => 0,
                    'created_at'         => Carbon::now()->subDays(5),
                    'updated_at'         => Carbon::now()->subDays(3),
                ],
                [
                    'city'               => 'Санкт-Петербург',
                    'client_type'        => 'person',
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'Петрова Мария',
                    'birth_date'         => '1992-07-22',
                    'phone'              => '+79990000002',
                    'email'              => 'petrova@example.com',
                    'passport_series'    => '4010',
                    'passport_number'    => '654321',
                    'photo_path'         => null,
                    'comment'            => 'Любит предзаказы',
                    'send_trip_report'   => true,
                    'news_notifications' => false,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 0,
                    'balance'            => 1500.00,
                    'created_at'         => Carbon::now()->subDays(2),
                    'updated_at'         => Carbon::now()->subDay(),
                ],
                [
                    'city'               => 'Казань',
                    'client_type'        => 'person',
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'Сидоров Алексей',
                    'birth_date'         => '1985-11-09',
                    'phone'              => '+79990000003',
                    'email'              => 'sidorov@example.com',
                    'passport_series'    => '4012',
                    'passport_number'    => '111222',
                    'photo_path'         => null,
                    'comment'            => 'Есть долг',
                    'send_trip_report'   => false,
                    'news_notifications' => true,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 5000.00,
                    'balance'            => -350.00,
                    'created_at'         => Carbon::now()->subDays(8),
                    'updated_at'         => Carbon::now()->subDays(6),
                ],
                [
                    'city'               => 'Москва',
                    'client_type'        => 'person',
                    'is_agent'           => true, // агент
                    'lang'               => 'ru',
                    'full_name'          => 'Агентов Сергей',
                    'birth_date'         => '1988-01-02',
                    'phone'              => '+79990000004',
                    'email'              => 'agentov@example.com',
                    'passport_series'    => '4013',
                    'passport_number'    => '333444',
                    'photo_path'         => null,
                    'comment'            => 'Приводит клиентов',
                    'send_trip_report'   => false,
                    'news_notifications' => false,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 0,
                    'balance'            => 0,
                    'created_at'         => Carbon::now()->subHours(10),
                    'updated_at'         => Carbon::now()->subHours(4),
                ],
                [
                    'city'               => 'Екатеринбург',
                    'client_type'        => 'person',
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'Нечаева Татьяна',
                    'birth_date'         => '1996-04-18',
                    'phone'              => '+79990000005',
                    'email'              => 'nechaeva@example.com',
                    'passport_series'    => '4014',
                    'passport_number'    => '555666',
                    'photo_path'         => null,
                    'comment'            => null,
                    'send_trip_report'   => true,
                    'news_notifications' => true,
                    'allow_push'         => true,
                    'blacklisted'        => true, // в ЧС
                    'credit_limit'       => 0,
                    'balance'            => 0,
                    'created_at'         => Carbon::now()->subDays(1),
                    'updated_at'         => Carbon::now()->subHours(2),
                ],
                [
                    'city'               => 'Новосибирск',
                    'client_type'        => 'org', // будущий корпоративный (сотрудник/юр.лицо)
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'ООО «Ромашка» (контакт: Кузнецов П.)',
                    'birth_date'         => null,
                    'phone'              => '+79990000006',
                    'email'              => 'corp-contact@example.com',
                    'passport_series'    => null,
                    'passport_number'    => null,
                    'photo_path'         => null,
                    'comment'            => 'Корпоративный клиент (демо)',
                    'send_trip_report'   => true,
                    'news_notifications' => false,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 20000.00,
                    'balance'            => 0,
                    'created_at'         => Carbon::now()->subDays(12),
                    'updated_at'         => Carbon::now()->subDays(10),
                ],
            ];

            foreach ($rows as $row) {
                // чтобы повторный запуск не плодил дубликаты — ориентируемся на уникальный телефон
                Client::updateOrCreate(
                    ['phone' => $row['phone']],
                    $row
                );
            }
        });
    }
}
