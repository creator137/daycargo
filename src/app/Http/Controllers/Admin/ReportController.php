<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        // Для селектов
        $cities = City::where('active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        // Значения по умолчанию фильтров
        $period = $request->string('period', '30d')->toString(); // 7d|30d|90d|custom
        $from   = $request->date('from') ?: Carbon::now()->subDays($period === '7d' ? 7 : ($period === '90d' ? 90 : 30))->startOfDay();
        $to     = $request->date('to')   ?: Carbon::now()->endOfDay();
        $cityId = $request->integer('city_id') ?: null;
        $type   = $request->string('type')->toString(); // now|schedule|...

        return view('admin.reports.summary', compact('cities', 'period', 'from', 'to', 'cityId', 'type'));
    }

    public function summaryData(Request $request)
    {
        // Валидация входящих фильтров
        $request->validate([
            'from'    => 'required|date',
            'to'      => 'required|date|after_or_equal:from',
            'city_id' => 'nullable|integer',
            'type'    => 'nullable|string',
        ]);

        $from = Carbon::parse($request->input('from'))->startOfDay();
        $to   = Carbon::parse($request->input('to'))->endOfDay();
        $cityId = $request->integer('city_id') ?: null;
        $type   = $request->string('type')->toString();

        // Ключ кэша (30 сек) — чтобы не долбить БД на каждом изменении вкладок
        $cacheKey = 'reports.summary.' . md5(json_encode([$from, $to, $cityId, $type]));
        return Cache::remember($cacheKey, 30, function () use ($from, $to, $cityId, $type) {

            $base = Order::query()
                ->when($cityId, fn($q) => $q->where('city_id', $cityId))
                ->when($type,   fn($q) => $q->where('type', $type))
                ->whereBetween('created_at', [$from, $to]);

            // KPI
            $kpi = (clone $base)
                ->selectRaw('COUNT(*) as orders_total')
                ->selectRaw("SUM(CASE WHEN status IN ('completed') THEN 1 ELSE 0 END) as orders_done")
                ->selectRaw("SUM(price_total) as revenue_total")
                ->selectRaw("AVG(duration_min_est) as avg_duration_min")
                ->first();

            // заказы по дням
            $ordersByDay = (clone $base)
                ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
                ->groupBy('d')->orderBy('d')
                ->get();

            // выручка по дням
            $revenueByDay = (clone $base)
                ->selectRaw("DATE(created_at) as d, SUM(price_total) as s")
                ->groupBy('d')->orderBy('d')
                ->get();

            // распределение по статусам
            $statusDist = (clone $base)
                ->selectRaw('status, COUNT(*) as c')
                ->groupBy('status')->orderBy('c', 'desc')->get();

            // распределение по способам оплаты
            $payDist = (clone $base)
                ->selectRaw('payment_method, COUNT(*) as c, SUM(price_total) as s')
                ->groupBy('payment_method')->orderBy('s', 'desc')->get();

            // ТОП-10 клиентов
            $topClients = (clone $base)
                ->selectRaw('client_id, COUNT(*) as cnt, SUM(price_total) as sum')
                ->whereNotNull('client_id')
                ->groupBy('client_id')->orderBy('sum', 'desc')->limit(10)->get();

            // Подтянуть имена клиентов одним запросом
            $clientNames = [];
            if ($topClients->isNotEmpty()) {
                $clientIds = $topClients->pluck('client_id')->all();
                $pairs = DB::table('clients')
                    ->selectRaw("id, COALESCE(NULLIF(full_name,''), phone, email) as name")
                    ->whereIn('id', $clientIds)->pluck('name', 'id');
                $clientNames = $pairs->toArray();
            }

            // ТОП-10 городов (если не зафиксирован city_id)
            $topCities = Order::query()
                ->whereBetween('created_at', [$from, $to])
                ->when($type, fn($q) => $q->where('type', $type))
                ->selectRaw('city_id, COUNT(*) as cnt, SUM(price_total) as sum')
                ->whereNotNull('city_id')
                ->groupBy('city_id')->orderBy('cnt', 'desc')->limit(10)->get();

            $cityNames = [];
            if ($topCities->isNotEmpty()) {
                $cityIds = $topCities->pluck('city_id')->all();
                $pairs = DB::table('cities')->whereIn('id', $cityIds)->pluck('name', 'id');
                $cityNames = $pairs->toArray();
            }

            // Среднее время выполнения (по факту) — если есть finished_at/started_at
            $avgFactDuration = (clone $base)
                ->whereNotNull('started_at')->whereNotNull('finished_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_min')
                ->value('avg_min');

            return response()->json([
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
                'kpi'  => [
                    'orders_total'   => (int) ($kpi->orders_total ?? 0),
                    'orders_done'    => (int) ($kpi->orders_done ?? 0),
                    'revenue_total'  => (float)($kpi->revenue_total ?? 0),
                    'avg_duration'   => round((float)($kpi->avg_duration_min ?? 0), 1),
                    'avg_fact_dur'   => round((float)($avgFactDuration ?? 0), 1),
                ],
                'orders_by_day'  => $ordersByDay,
                'revenue_by_day' => $revenueByDay,
                'status_dist'    => $statusDist,
                'payment_dist'   => $payDist,
                'top_clients'    => $topClients->map(function ($r) use ($clientNames) {
                    return [
                        'id'   => $r->client_id,
                        'name' => $clientNames[$r->client_id] ?? ('#' . $r->client_id),
                        'cnt'  => (int)$r->cnt,
                        'sum'  => (float)$r->sum,
                    ];
                }),
                'top_cities'     => $topCities->map(function ($r) use ($cityNames) {
                    return [
                        'id'   => $r->city_id,
                        'name' => $cityNames[$r->city_id] ?? ('#' . $r->city_id),
                        'cnt'  => (int)$r->cnt,
                        'sum'  => (float)$r->sum,
                    ];
                }),
            ]);
        });
    }
}
