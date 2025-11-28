@extends('layouts.admin')

@section('content')
    @php
        $title = 'Заказы';
    @endphp

    <x-page.header :title="$title">
        @can('create', \App\Models\Order::class)
            <x-ui.button :href="route('admin.orders.create')" variant="primary" size="sm">+ Новый заказ</x-ui.button>
        @endcan
    </x-page.header>

    {{-- ФИЛЬТРЫ --}}
    <x-ui.card class="mb-4">
        <form method="get" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <x-form.select name="status" label="Статус" :options="[
                'all' => 'Все',
                'new' => 'Новый',
                'assigning' => 'Назначение',
                'accepted' => 'Принят',
                'arrived' => 'Подача',
                'loading' => 'Погрузка',
                'driving' => 'В пути',
                'waiting' => 'Ожидание',
                'completed' => 'Завершён',
                'cancelled' => 'Отменён',
                'failed' => 'Неуспех',
                'refund' => 'Возврат',
            ]" :value="$filters['status']" />

            <x-form.select name="type" label="Тип" :options="[
                '' => '—',
                'courier' => 'Курьер',
                'now' => 'Срочно',
                'schedule' => 'По времени',
                'cargo' => 'Грузовой',
                'move' => 'Переезд',
                'intercity' => 'Межгород',
            ]" :value="$filters['type']" />

            <x-form.select name="city_id" label="Город" :options="['' => '—'] + $cityOptions" :value="$filters['city_id']" />

            <x-form.select name="payment_method" label="Оплата" :options="[
                '' => '—',
                'cash' => 'Нал',
                'card' => 'Карта',
                'cashless' => 'Безнал',
                'client_balance' => 'Баланс клиента',
                'org_balance' => 'Баланс организации',
            ]" :value="$filters['payment_method']" />

            <x-form.input name="from" type="date" label="Дата с" :value="$filters['from']" />
            <x-form.input name="to" type="date" label="Дата по" :value="$filters['to']" />

            <div class="md:col-span-6 grid grid-cols-1 md:grid-cols-3 gap-3">
                <x-form.input name="search" label="Поиск (№/адрес/клиент)" :value="$filters['search']" />
                <x-form.toggle name="only_new" label="Только новые" :checked="$filters['only_new']" />
                <div class="flex items-end gap-2">
                    <x-ui.button type="submit" variant="primary">Фильтр</x-ui.button>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-600 hover:underline">Сбросить</a>
                </div>
            </div>
        </form>
    </x-ui.card>

    {{-- СПИСОК --}}
    <x-ui.card>
        <x-ui.table :headers="['№', 'Дата', 'Город', 'Тип', 'Статус', 'Клиент', 'Откуда → Куда', 'Сумма', 'Оплата', 'Действия']">
            @forelse($items as $o)
                <tr>
                    <td class="whitespace-nowrap font-medium">{{ $o->number }}</td>
                    <td class="whitespace-nowrap">{{ $o->created_at->format('d.m.Y H:i') }}</td>
                    <td class="whitespace-nowrap">{{ $o->city }}</td>
                    <td class="whitespace-nowrap">{{ strtoupper($o->type) }}</td>
                    <td class="whitespace-nowrap">
                        <x-badges.order-status :status="$o->status" />
                    </td>
                    <td class="whitespace-nowrap">
                        {{ $o->client?->full_name ?? '—' }}
                        @if ($o->organization_id)
                            <div class="text-xs text-slate-500">Орг: {{ $o->organization?->short_name }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="text-sm">{{ $o->from_address }}</div>
                        @if ($o->to_address)
                            <div class="text-xs text-slate-500">→ {{ $o->to_address }}</div>
                        @endif
                    </td>
                    <td class="whitespace-nowrap">{{ number_format((float) $o->price_total, 0, ',', ' ') }}
                        {{ $o->currency }}</td>
                    <td class="whitespace-nowrap">
                        @switch($o->payment_method)
                            @case('cash')
                                Нал
                            @break

                            @case('card')
                                Карта
                            @break

                            @case('cashless')
                                Безнал
                            @break

                            @case('client_balance')
                                Баланс клиента
                            @break

                            @case('org_balance')
                                Баланс орг.
                            @break
                        @endswitch
                    </td>
                    <td class="whitespace-nowrap">
                        <div class="flex gap-2">
                            @can('update', $o)
                                <x-ui.button :href="route('admin.orders.edit', $o)" size="xs" variant="secondary">Править</x-ui.button>
                            @endcan
                            @can('delete', $o)
                                <form method="post" action="{{ route('admin.orders.destroy', $o) }}"
                                    onsubmit="return confirm('Удалить заказ?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="xs" variant="danger">Удалить</x-ui.button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="10"><x-ui.alert tone="muted">Заказов не найдено.</x-ui.alert></td>
                    </tr>
                @endforelse
            </x-ui.table>

            <div class="mt-4">{{ $items->links() }}</div>
        </x-ui.card>
    @endsection
