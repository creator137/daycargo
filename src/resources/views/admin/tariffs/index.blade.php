@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Тарифы</h1>

    <div class="grid grid-cols-1 gap-4">
        @foreach ($types as $type)
            @php $list = $tariffs->get($type->id) ?? collect(); @endphp

            <x-ui.card :title="$type->name" :subtitle="$type->code .
                ', ' .
                $type->capacity_kg .
                ' кг, ' .
                $type->length_cm .
                '×' .
                $type->width_cm .
                '×' .
                $type->height_cm .
                ' см'">
                <x-slot:actions>
                    @can('create', \App\Models\Tariff::class)
                        <x-ui.button :href="route('admin.tariffs.create', ['vehicle_type' => $type->id])" variant="primary" size="sm">
                            Добавить тариф
                        </x-ui.button>
                    @endcan
                </x-slot:actions>

                @if ($list->isEmpty())
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span>Тарифов пока нет.</span>
                        @can('create', \App\Models\Tariff::class)
                            <x-ui.button :href="route('admin.tariffs.create', ['vehicle_type' => $type->id])" variant="primary" size="sm">
                                Создать
                            </x-ui.button>
                        @endcan
                    </div>
                @else
                    <x-ui.table tone="bold" bordered hover compact :headers="[
                        'Область',
                        'Город',
                        ['label' => 'База', 'align' => 'right'],
                        ['label' => '₽/км', 'align' => 'right'],
                        ['label' => '₽/мин', 'align' => 'right'],
                        ['label' => 'Мин. заказ', 'align' => 'right'],
                        ['label' => 'Беспл. ожид., мин', 'align' => 'right'],
                        ['label' => '₽/мин ожид.', 'align' => 'right'],
                        ['label' => 'Статус', 'width' => '96px'],
                        ['label' => 'Действия', 'align' => 'right', 'width' => '280px'],
                    ]">
                        @foreach ($list as $tariff)
                            <tr>
                                <td>
                                    {{ \App\Models\Tariff::SCOPE_TYPES[$tariff->scope_type] ?? $tariff->scope_type }}
                                    @if ($tariff->scope_id)
                                        <span class="text-xs text-slate-500">#{{ $tariff->scope_id }}</span>
                                    @endif
                                </td>
                                <td>{{ $tariff->city ?: '—' }}</td>

                                <td class="text-right">{{ number_format($tariff->base_price, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($tariff->per_km, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($tariff->per_min, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ number_format($tariff->min_price, 2, ',', ' ') }}</td>
                                <td class="text-right">{{ $tariff->wait_free_min }}</td>
                                <td class="text-right">{{ number_format($tariff->wait_per_min, 2, ',', ' ') }}</td>

                                <td>
                                    <x-ui.badge :variant="$tariff->active ? 'success' : 'muted'">
                                        {{ $tariff->active ? 'Активен' : 'Скрыт' }}
                                    </x-ui.badge>
                                </td>

                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        {{-- Править --}}
                                        @can('update', $tariff)
                                            <x-ui.button :href="route('admin.tariffs.edit', $tariff)" variant="primary" size="sm">
                                                Править
                                            </x-ui.button>
                                        @endcan

                                        {{-- Вкл/Выкл (PATCH!) --}}
                                        @can('toggle', $tariff)
                                            <form action="{{ route('admin.tariffs.toggle', $tariff) }}" method="post">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" :variant="$tariff->active ? 'ghost' : 'success'" size="sm">
                                                    {{ $tariff->active ? 'Выключить' : 'Включить' }}
                                                </x-ui.button>
                                            </form>
                                        @endcan

                                        {{-- Удалить --}}
                                        @can('delete', $tariff)
                                            <form action="{{ route('admin.tariffs.destroy', $tariff) }}" method="post"
                                                onsubmit="return confirm('Удалить тариф?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="danger" size="sm">
                                                    Удалить
                                                </x-ui.button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                @endif
            </x-ui.card>
        @endforeach
    </div>
@endsection
