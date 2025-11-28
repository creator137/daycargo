<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverGroup;
use App\Models\Organization;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\OrderEvent;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\OrderRating;
use App\Models\OrderStatusLog;
use App\Models\Tariff;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Базовые справочники/сущности
        $cities        = City::orderBy('sort')->pluck('name', 'id')->all();
        if (empty($cities)) {
            $this->command->warn('Нет городов — сидер заказов пропущен.');
            return;
        }

        $clients       = Client::inRandomOrder()->take(50)->get();
        if ($clients->isEmpty()) {
            $this->command->warn('Нет клиентов — сидер заказов пропущен.');
            return;
        }

        $orgs          = Organization::inRandomOrder()->take(10)->get();
        $drivers       = class_exists(Driver::class) ? Driver::inRandomOrder()->take(40)->get() : collect();
        $vehicles      = class_exists(Vehicle::class) ? Vehicle::inRandomOrder()->take(40)->get() : collect();
        $vehicleTypes  = class_exists(VehicleType::class) ? VehicleType::inRandomOrder()->take(8)->get() : collect();
        $driverGroups  = class_exists(DriverGroup::class) ? DriverGroup::inRandomOrder()->take(8)->get() : collect();
        $tariffs       = class_exists(Tariff::class) ? Tariff::inRandomOrder()->take(10)->get() : collect();

        // Наборы значений
        $statusBuckets = [
            'new'        => ['new', 'search'],
            'inwork'     => ['assigned', 'en_route', 'loading', 'in_progress', 'waiting', 'paused'],
            'preorders'  => ['new', 'search', 'assigned'],
            'done'       => ['completed'],
            'canceled'   => ['canceled', 'failed'],
        ];
        $sources = ['operator', 'client_app', 'site', 'api', 'partner'];
        $payMethods = ['cash', 'card', 'org_balance', 'client_balance', 'cashless'];

        // Карта примерных адресов по городам
        $addressesByCity = [];
        foreach (array_keys($cities) as $cityId) {
            $name = $cities[$cityId];
            $addressesByCity[$cityId] = [
                "{$name}, Тверская ул., д. 1",
                "{$name}, Невский проспект, д. 10",
                "{$name}, Ленина, д. 5",
                "{$name}, Профсоюзная, д. 45",
                "{$name}, Проспект Мира, д. 150",
                "{$name}, Большая Садовая, д. 3",
            ];
        }

        // Сколько заказов насыпать
        $total = 120;

        $this->command->info("Создаём {$total} заказов…");

        DB::transaction(function () use (
            $total,
            $clients,
            $orgs,
            $drivers,
            $vehicles,
            $vehicleTypes,
            $driverGroups,
            $tariffs,
            $cities,
            $addressesByCity,
            $statusBuckets,
            $sources,
            $payMethods
        ) {

            for ($i = 0; $i < $total; $i++) {
                $client   = $clients->random();
                $cityId   = Arr::random(array_keys($cities));
                $cityName = $cities[$cityId];

                $isPreorder = random_int(1, 5) === 1; // ~20%
                $type = $isPreorder ? 'preorder' : (random_int(0, 10) === 0 ? 'offer' : 'now');

                // адреса
                $addrPool = $addressesByCity[$cityId];
                [$from, $to] = [Arr::random($addrPool), Arr::random($addrPool)];
                while ($to === $from && count($addrPool) > 1) {
                    $to = Arr::random($addrPool);
                }

                // Время
                $createdAt = now()->subDays(random_int(0, 20))->subMinutes(random_int(0, 1440));
                $arrivalFrom = $isPreorder ? (clone $createdAt)->addDays(random_int(1, 3))->setTime(random_int(7, 20), [0, 15, 30, 45][random_int(0, 3)]) : null;
                $arrivalTo   = $arrivalFrom ? (clone $arrivalFrom)->addMinutes(60) : null;

                // Исполнитель
                $driver   = $drivers->isNotEmpty() && random_int(0, 1) ? $drivers->random() : null;
                $vehicle  = $driver ? ($vehicles->firstWhere('id', $driver->vehicle_id) ?? $vehicles->random()) : null;

                // Категории
                $vt      = $vehicleTypes->random() ?? null;
                $dgroup  = $driverGroups->random() ?? null;
                $tariff  = $tariffs->random() ?? null;

                // Финансы (оценка)
                $base    = random_int(200, 900);
                $opts    = random_int(0, 1) ? random_int(50, 200) : 0;
                $wait    = random_int(0, 1) ? random_int(50, 150) : 0;
                $other   = random_int(0, 1) ? random_int(20, 120) : 0;
                $discount = random_int(0, 3) ? 0 : random_int(20, 200);
                $promo   = random_int(0, 4) ? 0 : random_int(50, 150);
                $bonus   = random_int(0, 4) ? 0 : random_int(20, 70);

                $totalPrice = max(0, $base + $opts + $wait + $other - $discount - $promo - $bonus);

                // Генерация номера
                $number = 'ORD-' . now()->format('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);

                // Статусная цель
                $bucketKey = Arr::random(array_keys($statusBuckets));
                $finalStatus = Arr::random($statusBuckets[$bucketKey]);

                $order = Order::create([
                    'number'   => $number,
                    'city_id'  => $cityId,
                    'city'     => $cityName,
                    'type'     => $type,
                    'source'   => Arr::random($sources),
                    'priority' => [0, 0, 0, 1, 2][random_int(0, 4)],

                    'status'   => 'new', // стартовый; актуальный проставим ниже

                    'client_id'       => $client->id,
                    'organization_id' => (random_int(0, 3) === 0 && $orgs->isNotEmpty()) ? $orgs->random()->id : null,
                    'payer_type'      => (random_int(0, 1) ? 'client' : 'organization'),
                    'contact_name'    => $client->full_name ?: null,
                    'contact_phone'   => $client->phone,
                    'blacklist_check' => false,

                    'from_address' => $from,
                    'to_address'   => $to,
                    'from_comment' => random_int(0, 3) ? null : 'КПП 3, пропуск оформлен',
                    'to_comment'   => random_int(0, 3) ? null : 'Звонить за 10 мин',
                    'arrival_window_from' => $arrivalFrom,
                    'arrival_window_to'   => $arrivalTo,
                    'via_points'         => random_int(0, 3) ? null : [
                        ['address' => $from . ' (склад)', 'comment' => 'забрать документ'],
                    ],

                    'tariff_id'        => $tariff?->id,
                    'vehicle_type_id'  => $vt?->id,
                    'driver_group_id'  => $dgroup?->id,

                    'options' => [
                        'child_seat' => (bool)random_int(0, 1),
                        'wagon'      => (bool)random_int(0, 1),
                        'refrigerator' => false,
                    ],
                    'distance_km_est'  => random_int(5, 40),
                    'duration_min_est' => random_int(15, 90),

                    'driver_id'   => $driver?->id,
                    'vehicle_id'  => $vehicle?->id,
                    'assign_strategy' => $driver ? 'manual' : (random_int(0, 1) ? 'auto_broadcast' : 'manual'),
                    'broadcast_radius_km' => 5.0,

                    'calc_schema'   => 'by_tariff',
                    'price_base'    => $base,
                    'price_surge'   => 0,
                    'price_options' => $opts,
                    'price_waiting' => $wait,
                    'price_loading' => 0,
                    'price_other'   => $other,
                    'price_discount' => $discount,
                    'promo_discount' => $promo,
                    'bonus_spent'   => $bonus,
                    'price_total'   => $totalPrice,
                    'currency'      => 'RUB',

                    'payment_method' => Arr::random($payMethods),
                    'prepaid_amount' => 0,
                    'paid_amount'    => 0,
                    'debt_amount'    => 0,

                    'need_terminal' => (bool)random_int(0, 1),
                    'need_docs'     => (bool)random_int(0, 1),
                    'fragile'       => (bool)random_int(0, 1),
                    'lift_required' => (bool)random_int(0, 1),
                    'helper_count'  => random_int(0, 2),
                    'is_return_trip' => (bool)random_int(0, 1),

                    'comment' => random_int(0, 2) ? null : 'Заказ из демо-сидера',

                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Логи статусов (прогоним путь)
                $path = $this->buildStatusPath($order->type, $finalStatus);
                $prev = null;
                foreach ($path as $step) {
                    OrderStatusLog::create([
                        'order_id'    => $order->id,
                        'status_from' => $prev,
                        'status_to'   => $step,
                        'actor_type'  => $step === 'search' ? 'system' : 'user',
                        'actor_id'    => null,
                        'comment'     => null,
                        'created_at'  => $createdAt->addMinutes(5),
                        'updated_at'  => $createdAt->addMinutes(5),
                    ]);
                    $order->status = $step;
                    if ($step === 'assigned')  $order->assigned_at = now()->subDays(random_int(0, 2));
                    if ($step === 'in_progress') $order->started_at = now()->subDays(random_int(0, 2));
                    $prev = $step;
                }

                // Позиции калькуляции
                $items = [
                    ['code' => 'base', 'title' => 'Базовый тариф', 'qty' => 1, 'price' => $base],
                ];
                if ($opts) $items[] = ['code' => 'options', 'title' => 'Опции', 'qty' => 1, 'price' => $opts];
                if ($wait) $items[] = ['code' => 'waiting', 'title' => 'Ожидание', 'qty' => 1, 'price' => $wait];
                if ($other) $items[] = ['code' => 'other', 'title' => 'Прочее', 'qty' => 1, 'price' => $other];
                foreach ($items as $it) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'code'     => $it['code'],
                        'title'    => $it['title'],
                        'qty'      => $it['qty'],
                        'price'    => $it['price'],
                        'total'    => $it['qty'] * $it['price'],
                    ]);
                }

                // События
                OrderEvent::create([
                    'order_id' => $order->id,
                    'type'     => 'created',
                    'payload'  => ['by' => 'seeder', 'source' => $order->source],
                    'created_at' => $order->created_at,
                ]);

                // Если завершён — оплатим и проставим финиш/оценку
                if ($order->status === 'completed') {
                    $order->finished_at = (clone $order->created_at)->addHours(random_int(1, 6));
                    $order->paid_amount = $order->price_total;
                    $order->debt_amount = 0;

                    OrderPayment::create([
                        'order_id' => $order->id,
                        'method'   => match ($order->payment_method) {
                            'org_balance'   => 'org_balance',
                            'client_balance' => 'client_balance',
                            'card'          => 'card',
                            default         => 'cash',
                        },
                        'amount'   => $order->price_total,
                        'currency' => 'RUB',
                        'status'   => 'captured',
                        'provider' => $order->payment_method === 'card' ? 'demo-gateway' : null,
                        'provider_txn_id' => $order->payment_method === 'card' ? Str::uuid()->toString() : null,
                        'created_at' => $order->finished_at,
                    ]);

                    if (random_int(0, 1)) {
                        OrderRating::create([
                            'order_id' => $order->id,
                            'client_id' => $order->client_id,
                            'rating'   => random_int(4, 5),
                            'comment'  => random_int(0, 1) ? 'Всё ок' : null,
                            'created_at' => $order->finished_at->addMinutes(15),
                        ]);
                    }
                }

                // Если отменён — долг 0, отметка времени
                if (in_array($order->status, ['canceled', 'failed'], true)) {
                    $order->canceled_at = (clone $order->created_at)->addMinutes(random_int(10, 60));
                    $order->paid_amount = 0;
                    $order->debt_amount = 0;
                }

                $order->save();
            }
        });

        $this->command->info('Готово: демо-заказы созданы.');
    }

    /**
     * Возвращает осмысленный путь статусов до итога.
     */
    private function buildStatusPath(string $type, string $final): array
    {
        $base = ['new'];
        // Для предзаказов до поездки может быть search позже
        $search = ['search'];
        $assigned = ['assigned', 'en_route'];
        $work = ['loading', 'in_progress'];
        $tail = [];

        return match ($final) {
            'new'       => ['new'],
            'search'    => array_merge($base, $search),
            'assigned'  => array_merge($base, $search, ['assigned']),
            'en_route'  => array_merge($base, $search, $assigned),
            'loading'   => array_merge($base, $search, $assigned, ['loading']),
            'in_progress' => array_merge($base, $search, $assigned, $work),
            'waiting'   => array_merge($base, $search, $assigned, $work, ['waiting']),
            'paused'    => array_merge($base, $search, $assigned, $work, ['paused']),
            'completed' => array_merge($base, $search, $assigned, $work, ['completed']),
            'canceled'  => array_merge($base, $search, ['canceled']),
            'failed'    => array_merge($base, $search, ['failed']),
            default     => ['new'],
        };
    }
}
