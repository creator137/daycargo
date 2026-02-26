@extends('layouts.admin')

@section('content')
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h1 class="text-lg font-semibold text-slate-900">Сводка</h1>
        </div>

        {{-- ФИЛЬТРЫ --}}
        <form id="filters"
            class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 grid grid-cols-1 md:grid-cols-5 gap-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Период</label>
                <select name="period" id="period" class="w-full rounded-md border-slate-300 text-sm">
                    <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>7 дней</option>
                    <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>30 дней</option>
                    <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>90 дней</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Произвольный</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">С</label>
                <input type="date" name="from" id="from" value="{{ $from->toDateString() }}"
                    class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">По</label>
                <input type="date" name="to" id="to" value="{{ $to->toDateString() }}"
                    class="w-full rounded-md border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Город</label>
                <select name="city_id" id="city_id" class="w-full rounded-md border-slate-300 text-sm">
                    <option value="">Все</option>
                    @foreach ($cities as $cid => $cname)
                        <option value="{{ $cid }}" {{ (string) $cid === (string) $cityId ? 'selected' : '' }}>
                            {{ $cname }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Тип заказа</label>
                <select name="type" id="type" class="w-full rounded-md border-slate-300 text-sm">
                    <option value="">Все</option>
                    <option value="now" {{ $type === 'now' ? 'selected' : '' }}>Срочно</option>
                    <option value="preorder" {{ $type === 'preorder' ? 'selected' : '' }}>По времени</option>
                    <option value="offer" {{ $type === 'offer' ? 'selected' : '' }}>Офер</option>
                </select>
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button id="applyBtn" type="button"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Применить</button>
                <button id="resetBtn" type="button" class="px-4 py-2 rounded-lg border text-sm">Сброс</button>
            </div>
        </form>

        {{-- KPI --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @foreach ([['id' => 'kpi_orders', 'label' => 'Всего заказов'], ['id' => 'kpi_done', 'label' => 'Завершено'], ['id' => 'kpi_rev', 'label' => 'Выручка, ₽'], ['id' => 'kpi_eta', 'label' => 'Средняя длит., мин'], ['id' => 'kpi_fact', 'label' => 'Факт. длит., мин']] as $k)
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                    <div class="text-sm text-slate-500">{{ $k['label'] }}</div>
                    <div id="{{ $k['id'] }}" class="mt-1 text-2xl font-semibold text-slate-900">—</div>
                </div>
            @endforeach
        </div>

        {{-- ГРАФИКИ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm font-medium mb-2">Заказы по дням</div>
                <div class="h-72 md:h-80"><canvas id="chartOrders"></canvas></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm font-medium mb-2">Выручка по дням</div>
                <div class="h-72 md:h-80"><canvas id="chartRevenue"></canvas></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm font-medium mb-2">Распределение статусов</div>
                <div class="h-72 md:h-80"><canvas id="chartStatus"></canvas></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                <div class="text-sm font-medium mb-2">Способы оплаты</div>
                <div class="h-72 md:h-80"><canvas id="chartPay"></canvas></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 lg:col-span-2">
                <div class="text-sm font-medium mb-2">ТОП-10 клиентов (по выручке)</div>
                <div class="h-96 md:h-[28rem]"><canvas id="chartClients"></canvas></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 lg:col-span-2">
                <div class="text-sm font-medium mb-2">ТОП-10 городов (по количеству)</div>
                <div class="h-96 md:h-[28rem]"><canvas id="chartCities"></canvas></div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Chart.js (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Глобальные дефолты: тянемся по высоте контейнера
            if (window.Chart) {
                Chart.defaults.responsive = true;
                Chart.defaults.maintainAspectRatio = false;
            }

            const els = {
                period: document.getElementById('period'),
                from: document.getElementById('from'),
                to: document.getElementById('to'),
                city_id: document.getElementById('city_id'),
                type: document.getElementById('type'),
                apply: document.getElementById('applyBtn'),
                reset: document.getElementById('resetBtn'),
                kpiOrders: document.getElementById('kpi_orders'),
                kpiDone: document.getElementById('kpi_done'),
                kpiRev: document.getElementById('kpi_rev'),
                kpiEta: document.getElementById('kpi_eta'),
                kpiFact: document.getElementById('kpi_fact'),
            };

            els.period.addEventListener('change', () => {
                const now = new Date();
                const toStr = now.toISOString().slice(0, 10);
                let from = new Date(now);
                if (els.period.value === '7d') from.setDate(now.getDate() - 7);
                if (els.period.value === '30d') from.setDate(now.getDate() - 30);
                if (els.period.value === '90d') from.setDate(now.getDate() - 90);
                if (els.period.value !== 'custom') {
                    els.from.value = from.toISOString().slice(0, 10);
                    els.to.value = toStr;
                }
            });

            els.reset.addEventListener('click', () => {
                els.period.value = '30d';
                const now = new Date();
                els.to.value = now.toISOString().slice(0, 10);
                const from = new Date(now);
                from.setDate(now.getDate() - 30);
                els.from.value = from.toISOString().slice(0, 10);
                els.city_id.value = '';
                els.type.value = '';
                load();
            });
            els.apply.addEventListener('click', load);

            let charts = {};

            function destroy(id) {
                if (charts[id]) {
                    charts[id].destroy();
                    delete charts[id];
                }
            }

            function load() {
                const params = new URLSearchParams({
                    from: els.from.value,
                    to: els.to.value,
                    city_id: els.city_id.value || '',
                    type: els.type.value || '',
                }).toString();

                fetch(`{{ route('admin.reports.summary.data') }}?` + params, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.json())
                    .then(draw)
                    .catch(console.error);
            }

            function draw(data) {
                els.kpiOrders.innerText = data.kpi.orders_total.toLocaleString('ru-RU');
                els.kpiDone.innerText = data.kpi.orders_done.toLocaleString('ru-RU');
                els.kpiRev.innerText = Math.round(data.kpi.revenue_total).toLocaleString('ru-RU');
                els.kpiEta.innerText = data.kpi.avg_duration.toLocaleString('ru-RU');
                els.kpiFact.innerText = data.kpi.avg_fact_dur.toLocaleString('ru-RU');

                const labels = (arr, key) => arr.map(i => i[key]);
                const values = (arr, key) => arr.map(i => Number(i[key] || 0));

                destroy('orders');
                charts.orders = new Chart(document.getElementById('chartOrders'), {
                    type: 'line',
                    data: {
                        labels: labels(data.orders_by_day, 'd'),
                        datasets: [{
                            label: 'Заказы',
                            data: values(data.orders_by_day, 'c')
                        }]
                    },
                    options: {
                        maintainAspectRatio: false
                    }
                });

                destroy('revenue');
                charts.revenue = new Chart(document.getElementById('chartRevenue'), {
                    type: 'line',
                    data: {
                        labels: labels(data.revenue_by_day, 'd'),
                        datasets: [{
                            label: '₽',
                            data: values(data.revenue_by_day, 's')
                        }]
                    },
                    options: {
                        maintainAspectRatio: false
                    }
                });

                destroy('status');
                charts.status = new Chart(document.getElementById('chartStatus'), {
                    type: 'doughnut',
                    data: {
                        labels: labels(data.status_dist, 'status'),
                        datasets: [{
                            data: values(data.status_dist, 'c')
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        maintainAspectRatio: false
                    }
                });

                destroy('pay');
                charts.pay = new Chart(document.getElementById('chartPay'), {
                    type: 'bar',
                    data: {
                        labels: labels(data.payment_dist, 'payment_method'),
                        datasets: [{
                                label: 'Кол-во',
                                data: values(data.payment_dist, 'c')
                            },
                            {
                                label: '₽',
                                data: values(data.payment_dist, 's')
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false
                    }
                });

                destroy('clients');
                charts.clients = new Chart(document.getElementById('chartClients'), {
                    type: 'bar',
                    data: {
                        labels: data.top_clients.map(i => i.name),
                        datasets: [{
                            label: '₽',
                            data: data.top_clients.map(i => i.sum)
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false
                    }
                });

                destroy('cities');
                charts.cities = new Chart(document.getElementById('chartCities'), {
                    type: 'bar',
                    data: {
                        labels: data.top_cities.map(i => i.name),
                        datasets: [{
                                label: 'Заказы',
                                data: data.top_cities.map(i => i.cnt)
                            },
                            {
                                label: '₽',
                                data: data.top_cities.map(i => i.sum)
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false
                    }
                });
            }

            load();
        });
    </script>
@endpush
