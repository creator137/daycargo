{{-- resources/views/admin/drivers/index.blade.php --}}

@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Исполнители — водители</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                {{-- Tabs по статусам --}}
                <div class="flex items-center gap-1 text-sm">
                    @php
                        $tabItems = [
                            ['key' => 'active', 'label' => 'Активные'],
                            ['key' => 'blocked', 'label' => 'Заблокированные'],
                            ['key' => 'pending', 'label' => 'Неактивированные'],
                        ];
                    @endphp
                    @foreach ($tabItems as $tab)
                        @php $is = $status === $tab['key']; @endphp
                        <a href="{{ request()->fullUrlWithQuery(['status' => $tab['key']]) }}"
                            class="px-3 py-1.5 rounded-lg border text-slate-700 {{ $is ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
                            {{ $tab['label'] }}
                            <span class="ml-1 text-slate-500">({{ $tabs[$tab['key']] ?? 0 }})</span>
                        </a>
                    @endforeach
                </div>

                @can('create', \App\Models\Driver::class)
                    <x-ui.button :href="route('admin.drivers.create')" variant="primary" size="sm">Добавить</x-ui.button>
                @endcan
            </div>
        </x-slot:actions>

        {{-- Фильтры --}}
        <form method="get" class="mb-4 grid grid-cols-1 md:grid-cols-5 gap-3" x-data>
            <x-form.select name="city" label="Город" :options="$citiesOptions" :value="request('city')" placeholder="Все"
                x-on:change="$root.submit()" />

            <x-form.select name="vehicle_type_id" label="Класс авто" :options="$vehicleTypes" :value="request('vehicle_type_id')" placeholder="Все"
                x-on:change="$root.submit()" />

            <x-form.select name="driver_group_id" label="Группа" :options="$driverGroups" :value="request('driver_group_id')" placeholder="Все"
                x-on:change="$root.submit()" />

            <x-form.input name="partner" label="Партнёр" :value="request('partner')" placeholder="Название компании" />

            <x-form.input name="search" label="Поиск (ФИО/тел/email/позывной)" :value="request('search')"
                placeholder="Например: Иванов или +7999..." />

            <div class="md:col-span-5 flex items-center gap-2">
                <x-ui.button type="submit" variant="primary">Показать</x-ui.button>
                <a href="{{ route('admin.drivers.index', ['status' => $status]) }}"
                    class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                    Сбросить
                </a>
            </div>
        </form>

        @if ($items->isEmpty())
            <x-ui.alert>Пока нет записей.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                ['label' => 'Дата рег.'],
                'Водитель',
                ['label' => 'Позывной', 'width' => '120px'],
                ['label' => 'Документы', 'width' => '140px'],
                ['label' => 'Класс'],
                ['label' => 'Баланс', 'align' => 'right'],
                'Партнёр',
                ['label' => 'Изм.', 'width' => '120px'],
                ['label' => 'Кто', 'width' => '160px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '280px'],
            ]">
                @foreach ($items as $d)
                    @php
                        // аккуратно: если relation files есть — покажем счётчик, иначе не падаем
                        $filesCount = 0;
                        if (method_exists($d, 'files')) {
                            try {
                                $filesCount = $d->relationLoaded('files') ? $d->files->count() : $d->files()->count();
                            } catch (\Throwable $e) {
                                $filesCount = 0;
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ optional($d->created_at)->format('d.m.Y') }}</td>

                        <td>
                            <div class="flex items-center gap-3">
                                @if ($d->avatar_url)
                                    <img src="{{ $d->avatar_url }}" alt=""
                                        class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs">
                                        {{ mb_substr($d->full_name ?? '—', 0, 1) }}
                                    </div>
                                @endif

                                <div>
                                    <div class="font-medium">
                                        {{ $d->full_name ?: trim(($d->last_name ?? '') . ' ' . ($d->first_name ?? '') . ' ' . ($d->second_name ?? '')) ?: '—' }}
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        @if ($d->phone)
                                            <a class="hover:underline"
                                                href="tel:{{ $d->phone }}">{{ $d->phone }}</a>
                                        @endif

                                        @if ($d->email)
                                            · <a class="hover:underline"
                                                href="mailto:{{ $d->email }}">{{ $d->email }}</a>
                                        @endif

                                        @if ($d->main_city)
                                            · {{ $d->main_city }}
                                        @endif
                                    </div>

                                    <div class="text-xs text-slate-500 mt-0.5">
                                        @if (!empty($d->citizenship))
                                            {{ $d->citizenship }}
                                        @endif
                                        @if (!empty($d->employment_type))
                                            @if (!empty($d->citizenship))
                                                ·
                                            @endif {{ $d->employment_type }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>{{ $d->callsign ?: '—' }}</td>

                        <td>
                            @if ($filesCount > 0)
                                <span
                                    class="px-2 py-1 text-xs rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $filesCount }} файл(ов)
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">нет</span>
                            @endif
                        </td>

                        <td>{{ optional($d->vehicleType)->name ?: '—' }}</td>

                        <td class="text-right">{{ number_format((float) ($d->balance ?? 0), 2, ',', ' ') }}</td>

                        <td>{{ $d->partner_name ?: '—' }}</td>

                        <td>{{ optional($d->updated_at)->format('d.m.Y H:i') }}</td>

                        <td>{{ optional($d->updatedBy)->name ?: '—' }}</td>

                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @if ($d->phone)
                                    <a href="tel:{{ $d->phone }}"
                                        class="text-sm text-slate-600 hover:underline">Позвонить</a>
                                @endif
                                @if ($d->email)
                                    <a href="mailto:{{ $d->email }}"
                                        class="text-sm text-slate-600 hover:underline">Письмо</a>
                                @endif

                                @can('update', $d)
                                    <x-ui.button :href="route('admin.drivers.edit', $d)" variant="primary" size="sm">Править</x-ui.button>
                                @endcan

                                @can('toggle', $d)
                                    <form action="{{ route('admin.drivers.toggle', $d) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$d->status === 'active' ? 'ghost' : 'success'" size="sm">
                                            {{ $d->status === 'active' ? 'Заблокировать' : 'Активировать' }}
                                        </x-ui.button>
                                    </form>
                                @endcan

                                @can('delete', $d)
                                    <form action="{{ route('admin.drivers.destroy', $d) }}" method="post"
                                        onsubmit="return confirm('Удалить водителя?');">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">Удалить</x-ui.button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">{{ $items->links() }}</div>
        @endif
    </x-ui.card>
@endsection
