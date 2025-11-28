<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Client;
use App\Models\PromoCode;
use App\Models\PromoCodeRedemption;
use App\Models\Referral;

class LoyaltyDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // ===== 1) Клиенты =====
            $clientsData = [
                ['full_name' => 'Иван Петров',   'phone' => '+79990000001', 'city' => 'Москва'],
                ['full_name' => 'Анна Смирнова', 'phone' => '+79990000002', 'city' => 'Санкт-Петербург'],
                ['full_name' => 'Павел Романов', 'phone' => '+79990000003', 'city' => 'Москва'],
                ['full_name' => 'Сергей Агентов', 'phone' => '+79990000004', 'city' => 'Москва', 'is_agent' => true],
                ['full_name' => 'Мария Яковлева', 'phone' => '+79990000005', 'city' => 'Казань'],
                ['full_name' => 'Олег Кузьмин',  'phone' => '+79990000006', 'city' => 'Екатеринбург'],
                ['full_name' => 'Наталья Орлова', 'phone' => '+79990000007', 'city' => 'Новосибирск'],
                ['full_name' => 'Дмитрий Носов', 'phone' => '+79990000008', 'city' => 'Москва'],
            ];

            $clients = collect($clientsData)->map(function ($c) {
                return Client::firstOrCreate(
                    ['phone' => $c['phone']],
                    array_merge([
                        'client_type'       => 'person',
                        'lang'              => 'ru',
                        'allow_push'        => true,
                        'send_trip_report'  => false,
                        'news_notifications' => false,
                        'blacklisted'       => false,
                        'credit_limit'      => 0,
                        'balance'           => 0,
                    ], $c)
                );
            })->values();

            // Быстрый доступ по индексу
            [$c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8] = $clients->all();

            // ===== 2) Промокоды =====
            $now = Carbon::now();
            $codes = [
                [
                    'code'            => 'WELCOME10',
                    'type'            => 'percent',  // percent|fixed|bonus
                    'value'           => 10,         // 10%
                    'starts_at'       => $now->copy()->subDays(10),
                    'expires_at'      => $now->copy()->addDays(50),
                    'per_user_limit'  => 1,
                ],
                [
                    'code'            => 'FIX150',
                    'type'            => 'fixed',    // ₽
                    'value'           => 150,
                    'starts_at'       => $now->copy()->subDays(5),
                    'expires_at'      => $now->copy()->addDays(20),
                    'per_user_limit'  => 2,
                ],
                [
                    'code'            => 'BONUS300',
                    'type'            => 'bonus',    // бонусные баллы
                    'value'           => 300,
                    'starts_at'       => $now->copy()->subDays(1),
                    'expires_at'      => $now->copy()->addDays(90),
                    'per_user_limit'  => 1,
                ],
                [
                    'code'            => 'WEEKEND5',
                    'type'            => 'percent',
                    'value'           => 5,
                    'starts_at'       => $now->copy()->subDays(30),
                    'expires_at'      => $now->copy()->addDays(5),
                    'per_user_limit'  => null,
                ],
                [
                    'code'            => 'BIGFIX300',
                    'type'            => 'fixed',
                    'value'           => 300,
                    'starts_at'       => $now->copy()->subDays(2),
                    'expires_at'      => $now->copy()->addDays(30),
                    'per_user_limit'  => 1,
                ],
            ];

            $promoCodes = collect($codes)->map(function ($pc) {
                return PromoCode::firstOrCreate(
                    ['code' => $pc['code']],
                    [
                        'type'           => $pc['type'],
                        'value'          => $pc['value'],
                        'starts_at'      => $pc['starts_at'],
                        'expires_at'     => $pc['expires_at'],
                        'per_user_limit' => $pc['per_user_limit'],
                    ]
                );
            })->keyBy('code');

            // ===== 3) Активации промокодов =====
            $mkRedemption = function (Client $client, PromoCode $code, string $status = 'applied', float $orderAmount = 1000, ?string $createdAt = null) {
                // расчет применённой суммы чисто для демо
                $applied = 0;
                if ($code->type === 'percent') {
                    $applied = round($orderAmount * ($code->value / 100), 2);
                } elseif ($code->type === 'fixed') {
                    $applied = min($orderAmount, (float)$code->value);
                } elseif ($code->type === 'bonus') {
                    $applied = (float)$code->value; // трактуем как начисленные баллы
                }

                return PromoCodeRedemption::create([
                    'promo_code_id'  => $code->id,
                    'client_id'      => $client->id,
                    'status'         => $status, // applied|pending|rejected
                    'applied_amount' => $applied,
                    'created_at'     => $createdAt ? Carbon::parse($createdAt) : Carbon::now()->subMinutes(rand(0, 7200)),
                    'updated_at'     => Carbon::now(),
                ]);
            };

            $mkRedemption($c1, $promoCodes['WELCOME10'], 'applied', 1200, '-2 days');
            $mkRedemption($c2, $promoCodes['FIX150'],    'applied', 800,  '-1 day');
            $mkRedemption($c3, $promoCodes['BONUS300'],  'applied', 0,    '-3 hours');
            $mkRedemption($c4, $promoCodes['WEEKEND5'],  'pending', 950,  '-5 hours');
            $mkRedemption($c5, $promoCodes['FIX150'],    'rejected', 700,  '-9 hours');
            $mkRedemption($c6, $promoCodes['BIGFIX300'], 'applied', 1800, '-30 minutes');
            $mkRedemption($c7, $promoCodes['WELCOME10'], 'applied', 500,  '-20 minutes');
            $mkRedemption($c8, $promoCodes['BONUS300'],  'pending', 0,    '-10 minutes');

            // ===== 4) Рефералы =====
            // статусы: pending|approved|rejected
            $refs = [
                [$c4, $c5, 'approved', 200],
                [$c4, $c6, 'pending',  0],
                [$c2, $c7, 'approved', 150],
                [$c1, $c8, 'rejected', 0],
                [$c3, $c2, 'approved', 100],
            ];

            foreach ($refs as [$referrer, $referee, $status, $reward]) {
                Referral::create([
                    'referrer_id'  => $referrer->id,
                    'referee_id'   => $referee->id,
                    'status'       => $status,
                    'reward_points' => $reward,
                    'created_at'   => Carbon::now()->subDays(rand(0, 15))->subMinutes(rand(0, 1440)),
                    'updated_at'   => Carbon::now(),
                ]);
            }
        });
    }
}
