<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\City;
use App\Models\Client;
use App\Models\Organization;
use App\Models\Tariff;
use App\Models\VehicleType;
use App\Models\DriverGroup;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    /** Города: id => name */
    private function cityOptions(): array
    {
        return City::where('active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /** Типы авто: id => name */
    private function vehicleTypeOptions(): array
    {
        return VehicleType::orderBy('capacity_kg')
            ->pluck('name', 'id')
            ->toArray();
    }

    /** Группы водителей: id => name */
    private function driverGroupOptions(): array
    {
        return DriverGroup::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    /** Тарифы c metadata для UI-фильтрации по клиенту */
    private function tariffOptions(): array
    {
        $hasName = Schema::hasColumn('tariffs', 'name');

        return Tariff::query()
            ->orderBy('id')
            ->get()
            ->map(function (Tariff $tariff) use ($hasName) {
                $baseLabel = ($hasName && !empty($tariff->name))
                    ? $tariff->name
                    : ('Тариф #' . $tariff->id);

                $scopeLabel = match ($tariff->scope_type) {
                    'customer' => 'Клиент #' . (int) $tariff->scope_id,
                    'integration' => 'Интеграция #' . (int) $tariff->scope_id,
                    default => 'Глобальный',
                };

                return [
                    'id' => $tariff->id,
                    'label' => $baseLabel . ' · ' . $scopeLabel,
                    'scope_type' => (string) $tariff->scope_type,
                    'scope_id' => $tariff->scope_id ? (int) $tariff->scope_id : null,
                ];
            })
            ->all();
    }

    /** Клиенты: id => display (ФИО/тел/емейл) */
    private function clientOptions(): array
    {
        return Client::query()
            ->selectRaw("id, coalesce(nullif(full_name,''), phone, email) as display")
            ->orderBy('display')
            ->pluck('display', 'id')
            ->toArray();
    }

    /** Организации: id => short_name */
    private function organizationOptions(): array
    {
        return Organization::orderBy('short_name')
            ->pluck('short_name', 'id')
            ->toArray();
    }

    /** Водители: id => display (ФИО/позывной/тел) */
    private function driverOptions(): array
    {
        return Driver::query()
            ->selectRaw("id, coalesce(nullif(full_name,''), callsign, phone) as display")
            ->orderBy('display')
            ->pluck('display', 'id')
            ->toArray();
    }

    /** Авто: id => "Brand Model (A777AA)" */
    private function vehicleOptions(): array
    {
        return Vehicle::query()
            ->selectRaw("id, concat(coalesce(nullif(brand,''),'Авто'), ' ', coalesce(nullif(model,''),'—'), ' (', license_plate, ')') as display")
            ->orderBy('display')
            ->pluck('display', 'id')
            ->toArray();
    }

    /** Список */
    public function index(Request $req)
    {
        $status = $req->string('status')->toString(); // new|search|...|all
        $type   = $req->string('type')->toString();   // now|later|...
        $cityId = $req->integer('city_id');
        $pay    = $req->string('payment_method')->toString();

        $q = Order::query()
            ->with(['client', 'organization', 'driver', 'vehicle', 'tariff', 'vehicleType'])
            ->when($status && $status !== 'all', fn($qq) => $qq->where('status', $status))
            ->when($type,   fn($qq) => $qq->where('type', $type))
            ->when($cityId, fn($qq) => $qq->where('city_id', $cityId))
            ->when($pay,    fn($qq) => $qq->where('payment_method', $pay))
            ->when($req->filled('from'), fn($qq) => $qq->where('created_at', '>=', $req->date('from')))
            ->when($req->filled('to'),   fn($qq) => $qq->where('created_at', '<=', $req->date('to')))
            ->when($req->filled('search'), function ($qq) use ($req) {
                $s = '%' . trim($req->string('search')) . '%';
                $qq->where(function ($w) use ($s) {
                    $w->where('number', 'like', $s)
                        ->orWhere('from_address', 'like', $s)
                        ->orWhere('to_address', 'like', $s)
                        ->orWhereHas('client', fn($c) => $c->where('full_name', 'like', $s)
                            ->orWhere('phone', 'like', $s));
                });
            })
            ->orderByDesc('created_at');

        // Быстрый фильтр «Только новые»
        if ($req->boolean('only_new')) {
            $q->whereIn('status', ['new', 'search']);
        }

        $items = $q->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'items' => $items,
            'filters' => [
                'status'         => $status ?: 'all',
                'type'           => $type ?: '',
                'city_id'        => $cityId ?: '',
                'payment_method' => $pay ?: '',
                'from'           => $req->input('from'),
                'to'             => $req->input('to'),
                'search'         => $req->input('search'),
                'only_new'       => $req->boolean('only_new'),
            ],
            'cityOptions'        => $this->cityOptions(),
            'vehicleTypeOptions' => $this->vehicleTypeOptions(),
        ]);
    }

    /** Форма создания */
    public function create()
    {
        $order = new Order([
            'status'         => 'new',
            'type'           => 'now',
            'payer_type'     => 'client',
            'payment_method' => 'cash',
            'currency'       => 'RUB',
            'priority'       => 0,
            'options'        => [
                'child_seat' => false,
                'wagon' => false,
                'refrigerator' => false,
                'furniture_assembly' => false,
            ],
        ]);

        return view('admin.orders.form', [
            'order'               => $order,
            'cityOptions'         => $this->cityOptions(),
            'clientOptions'       => $this->clientOptions(),
            'organizationOptions' => $this->organizationOptions(),
            'tariffOptions'       => $this->tariffOptions(),
            'vehicleTypeOptions'  => $this->vehicleTypeOptions(),
            'driverGroupOptions'  => $this->driverGroupOptions(),
            'driverOptions'       => $this->driverOptions(),
            'vehicleOptions'      => $this->vehicleOptions(),
        ]);
    }

    /** Сохранение */
    public function store(OrderRequest $request)
    {
        $data = $this->normalizeOrderPayload($request->validated());

        if (empty($data['number'])) {
            $data['number'] = $this->generateOrderNumber();
        }

        Order::create($data);
        return redirect()->route('admin.orders.index')->with('success', 'Заказ создан.');
    }

    /** Форма правки */
    public function edit(Order $order)
    {
        return view('admin.orders.form', [
            'order'               => $order,
            'cityOptions'         => $this->cityOptions(),
            'clientOptions'       => $this->clientOptions(),
            'organizationOptions' => $this->organizationOptions(),
            'tariffOptions'       => $this->tariffOptions(),
            'vehicleTypeOptions'  => $this->vehicleTypeOptions(),
            'driverGroupOptions'  => $this->driverGroupOptions(),
            'driverOptions'       => $this->driverOptions(),
            'vehicleOptions'      => $this->vehicleOptions(),
        ]);
    }

    /** Обновление */
    public function update(OrderRequest $request, Order $order)
    {
        $data = $this->normalizeOrderPayload($request->validated());
        $order->update($data);
        return redirect()->route('admin.orders.index')->with('success', 'Сохранено.');
    }

    /**
     * Совместимость со stage-окружениями, где миграция с service_category
     * еще не применена: не отправляем отсутствующие колонки в insert/update.
     */
    private function normalizeOrderPayload(array $data): array
    {
        if (!Schema::hasColumn('orders', 'service_category')) {
            unset($data['service_category']);
        }

        // В orders много numeric полей NOT NULL с default 0.
        // Пустые инпуты приходят как null и ломают insert/update.
        $zeroIfNull = [
            'priority',
            'helper_count',
            'price_base',
            'price_surge',
            'price_options',
            'price_waiting',
            'price_loading',
            'price_other',
            'price_discount',
            'promo_discount',
            'bonus_spent',
            'price_total',
            'prepaid_amount',
            'paid_amount',
            'debt_amount',
        ];

        foreach ($zeroIfNull as $field) {
            if (array_key_exists($field, $data) && ($data[$field] === null || $data[$field] === '')) {
                $data[$field] = 0;
            }
        }

        $defaultIfNull = [
            'source' => 'operator',
            'currency' => 'RUB',
            'calc_schema' => 'by_tariff',
            'assign_strategy' => 'manual',
        ];

        foreach ($defaultIfNull as $field => $default) {
            if (array_key_exists($field, $data) && ($data[$field] === null || $data[$field] === '')) {
                $data[$field] = $default;
            }
        }

        return $data;
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Y') . '-';

        for ($i = 0; $i < 50; $i++) {
            $number = $prefix . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            if (!Order::where('number', $number)->exists()) {
                return $number;
            }
        }

        // Практически недостижимый fallback.
        return 'ORD-' . now()->format('YmdHis') . '-' . random_int(100000, 999999);
    }

    /** Удаление */
    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Удалено.');
    }
}
