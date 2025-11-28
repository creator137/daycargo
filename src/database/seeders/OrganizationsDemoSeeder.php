<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Organization;
use App\Models\OrgTransaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Небольшой хелпер: получить клиента по телефону (если нет — создаём простой "незарегистрированный")
            $getClient = function (string $phone, string $city = 'Москва'): Client {
                $c = Client::where('phone', $phone)->first();
                if ($c) return $c;

                return Client::create([
                    'city'               => $city,
                    'client_type'        => 'person',
                    'is_agent'           => false,
                    'lang'               => 'ru',
                    'full_name'          => 'Незарегистрированный клиент',
                    'phone'              => $phone,
                    'send_trip_report'   => true,
                    'news_notifications' => false,
                    'allow_push'         => true,
                    'blacklisted'        => false,
                    'credit_limit'       => 0,
                    'balance'            => 0,
                ]);
            };

            // Набор демо-организаций
            $orgs = [
                [
                    'city'                 => 'Москва',
                    'full_name'            => 'Общество с ограниченной ответственностью «Ромашка-Логистик»',
                    'short_name'           => 'ООО «Ромашка-Логистик»',
                    'legal_address'        => '109000, г. Москва, ул. Большая, д. 1',
                    'postal_address'       => '109000, г. Москва, а/я 123',
                    'director_name'        => 'Иванов И.И.',
                    'director_position'    => 'Генеральный директор',
                    'chief_accountant'     => 'Петрова А.А.',
                    'inn'                  => '7701234567',
                    'kpp'                  => '770101001',
                    'ogrn'                 => '1127746123456',
                    'bank_name'            => 'АО «Банк Демос»',
                    'bank_account'         => '40702810901234567890',
                    'bank_corr'            => '30101810400000000225',
                    'bank_bik'             => '044525225',
                    'phone'                => '+7 (495) 111-22-33',
                    'email'                => 'info@romashka-log.ru',
                    'site'                 => 'https://romashka-log.example',
                    'contact_person'       => 'Кузнецов П.С.',
                    'contact_phone'        => '+7 (495) 222-33-44',
                    'contact_email'        => 'corp@romashka-log.ru',
                    'contract_number'      => 'Д-001/2025',
                    'contract_from'        => Carbon::now()->subMonths(6)->toDateString(),
                    'contract_to'          => Carbon::now()->addYear()->toDateString(),
                    'billing_period_months' => 1,
                    'credit_limit'         => 200000,
                    'active'               => true,
                    'comment'              => 'Ключевой корпоративный клиент (демо)',
                    // сотрудников привяжем ниже
                    'employees'            => [
                        // phone, is_admin, active, personal_limit, city
                        ['+79990000001', true,  true,  50000,  'Москва'],
                        ['+79990000002', false, true,  20000,  'Москва'],
                    ],
                    // входящие операции (пополнения)
                    'topups'               => [
                        ['amount' => 100000, 'comment' => 'Стартовый аванс'],
                        ['amount' => 50000,  'comment' => 'Доп. пополнение'],
                    ],
                ],
                [
                    'city'                 => 'Санкт-Петербург',
                    'full_name'            => 'Общество с ограниченной ответственностью «СеверТранс»',
                    'short_name'           => 'ООО «СеверТранс»',
                    'legal_address'        => '190000, г. Санкт-Петербург, Невский пр., д. 10',
                    'postal_address'       => '190000, г. Санкт-Петербург, а/я 7',
                    'director_name'        => 'Соколов Д.В.',
                    'director_position'    => 'Директор',
                    'chief_accountant'     => 'Ковалева Н.В.',
                    'inn'                  => '7802345678',
                    'kpp'                  => '780201001',
                    'ogrn'                 => '1137847123456',
                    'bank_name'            => 'ПАО «СеверБанк»',
                    'bank_account'         => '40702810555555555555',
                    'bank_corr'            => '30101810500000000777',
                    'bank_bik'             => '044030777',
                    'phone'                => '+7 (812) 333-44-55',
                    'email'                => 'info@severtrans.ru',
                    'site'                 => 'https://severtrans.example',
                    'contact_person'       => 'Морозов Е.С.',
                    'contact_phone'        => '+7 (812) 444-55-66',
                    'contact_email'        => 'corp@severtrans.ru',
                    'contract_number'      => 'Д-SPB/2025-12',
                    'contract_from'        => Carbon::now()->subMonths(3)->toDateString(),
                    'contract_to'          => Carbon::now()->addMonths(18)->toDateString(),
                    'billing_period_months' => 1,
                    'credit_limit'         => 150000,
                    'active'               => true,
                    'comment'              => 'Корп. клиент СПб (демо)',
                    'employees'            => [
                        ['+79990000003', true,  true,  30000, 'Санкт-Петербург'],
                        ['+79990000004', false, true,  15000, 'Санкт-Петербург'],
                    ],
                    'topups'               => [
                        ['amount' => 80000,  'comment' => 'Стартовый аванс'],
                    ],
                ],
                [
                    'city'                 => 'Новосибирск',
                    'full_name'            => 'Индивидуальный предприниматель Петров Алексей Сергеевич',
                    'short_name'           => 'ИП Петров А.С.',
                    'legal_address'        => '630000, г. Новосибирск, ул. Центральная, д. 5',
                    'postal_address'       => '630000, г. Новосибирск, аб. ящик №12',
                    'director_name'        => 'Петров А.С.',
                    'director_position'    => 'ИП',
                    'chief_accountant'     => '—',
                    'inn'                  => '540123456789',
                    'kpp'                  => null,
                    'ogrn'                 => '318547600123456',
                    'bank_name'            => 'АО «СибБанк»',
                    'bank_account'         => '40802810000000001234',
                    'bank_corr'            => '30101810600000000456',
                    'bank_bik'             => '045004456',
                    'phone'                => '+7 (383) 222-33-44',
                    'email'                => 'info@petrov-ip.ru',
                    'site'                 => null,
                    'contact_person'       => 'Петров А.С.',
                    'contact_phone'        => '+7 (913) 111-22-33',
                    'contact_email'        => 'ip@petrov-ip.ru',
                    'contract_number'      => 'ИП-NSK/2025-01',
                    'contract_from'        => Carbon::now()->subMonth()->toDateString(),
                    'contract_to'          => Carbon::now()->addYear()->toDateString(),
                    'billing_period_months' => 1,
                    'credit_limit'         => 50000,
                    'active'               => true,
                    'comment'              => 'Небольшой клиент (демо)',
                    'employees'            => [
                        ['+79990000005', true,  true,  10000, 'Новосибирск'],
                    ],
                    'topups'               => [
                        ['amount' => 20000, 'comment' => 'Стартовый аванс'],
                    ],
                ],
            ];

            foreach ($orgs as $data) {
                // Ключ для идемпотентности — INN (если у ИП он длиннее — всё равно ок)
                $org = Organization::updateOrCreate(
                    ['inn' => $data['inn']],
                    collect($data)->except(['employees', 'topups'])->toArray()
                );

                // Привязка сотрудников (pivot: is_admin, active, personal_limit)
                if (!empty($data['employees'])) {
                    foreach ($data['employees'] as [$phone, $isAdmin, $active, $limit, $city]) {
                        $client = $getClient($phone, $city);
                        $org->employees()->syncWithoutDetaching([
                            $client->id => [
                                'is_admin'       => (bool) $isAdmin,
                                'active'         => (bool) $active,
                                'personal_limit' => (float) $limit,
                            ],
                        ]);
                    }
                }

                // Первичные пополнения (и пересчёт баланса)
                if (!empty($data['topups'])) {
                    foreach ($data['topups'] as $t) {
                        OrgTransaction::create([
                            'organization_id' => $org->id,
                            'type'            => 'credit', // пополнение
                            'amount'          => $t['amount'],
                            'comment'         => $t['comment'] ?? 'Пополнение (демо)',
                            'created_at'      => now()->subDays(rand(1, 30)),
                        ]);
                    }
                }

                // Пересчёт баланса: сумма кредитов минус сумма дебетов
                $sumCredit = $org->transactions()->where('type', 'credit')->sum('amount');
                $sumDebit  = $org->transactions()->where('type', 'debit')->sum('amount');
                $org->balance = round(($sumCredit - $sumDebit), 2);
                $org->save();
            }
        });
    }
}
