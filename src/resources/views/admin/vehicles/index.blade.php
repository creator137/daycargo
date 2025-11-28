@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">
        Автомобили</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <form method="get" class="flex flex-wrap items-end gap-2">
                    <x-form.select name="status" :options="[
                        'active' => 'Активные',
                        'blocked' => 'Заблокированные',
                        'pending' => 'Неактивированные',
                    ]" :value="$status" />
                    <x-form.select name="city" :options="['' => 'Все города'] + $cities" :value="request('city')" />
                    <x-form.select name="vehicle_type_id" :options="['' => 'Все классы'] + $vehicleTypes->toArray()" :value="request('vehicle_type_id')" />
                    <x-form.select name="owner_type" :options="[
                        '' => 'Любой владелец',
                        'company' => 'Компания',
                        'private' => 'Частник',
                        'rent' => 'Аренда',
                    ]" :value="request('owner_type')" />
                    <x-form.select name="is_rent" :options="['' => 'Аренда: не важно', '1' => 'Да', '0' => 'Нет']" :value="request('is_rent')" />
                    <x-form.input name="search" placeholder="Марка/модель/номер/водитель" :value="request('search')" />
                    <x-ui.button type="submit" size="sm">Фильтр</x-ui.button>
                </form>

                @can('create', App\Models\Vehicle::class)
                    <x-ui.button :href="route('admin.vehicles.create')" size="sm" variant="primary">Добавить авто</x-ui.button>
                @endcan
            </div>
        </x-slot:actions>

        <div class="mb-3 flex gap-4 text-sm">
            <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'active'])) }}"
                class="{{ $status === 'active' ? 'font-semibold' : '' }}">
                Активные ({{ $tabs['active'] }})
            </a>
            <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'blocked'])) }}"
                class="{{ $status === 'blocked' ? 'font-semibold' : '' }}">
                Заблокированные ({{ $tabs['blocked'] }})
            </a>
            <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
                class="{{ $status === 'pending' ? 'font-semibold' : '' }}">
                Неактивированные ({{ $tabs['pending'] }})
            </a>
        </div>

        @if ($items->isEmpty())
            <x-ui.alert>Пока пусто.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Автомобиль',
                'Исполнитель',
                'Класс',
                'Город',
                'Госномер',
                'Статус',
                ['label' => 'Действия', 'align' => 'right', 'width' => '260px'],
            ]">
                @foreach ($items as $v)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if ($v->photo_url)
                                    <img src="{{ $v->photo_url }}" class="w-10 h-10 rounded object-cover" alt="">
                                @else
                                    <div class="w-10 h-10 rounded bg-slate-200 flex items-center justify-center text-xs">no
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium">{{ $v->brand }} {{ $v->model }} @if ($v->year)
                                            ({{ $v->year }})
                                        @endif
                                    </div>
                                    <div class="text-slate-500 text-xs">{{ $v->owner_type }}@if ($v->is_rent)
                                            , аренда
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($v->driver)
                                <div class="font-medium">{{ $v->driver->name }}</div>
                                <div class="text-slate-500 text-xs">{{ $v->driver->phone }}</div>
                            @else
                                <span class="text-slate-400 text-xs">не назначен</span>
                            @endif
                        </td>
                        <td>{{ $v->vehicleType?->name ?? '—' }}</td>
                        <td>{{ $v->city }}</td>
                        <td class="font-mono text-sm">{{ $v->license_plate }}</td>
                        <td>
                            @if ($v->status === 'active')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Активен</span>
                            @elseif($v->status === 'blocked')
                                <span class="px-2 py-1 text-xs rounded bg-rose-100 text-rose-700">Заблокирован</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-700">Неакт.</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $v)
                                    <x-ui.button :href="route('admin.vehicles.edit', $v)" size="sm" variant="primary">Править</x-ui.button>
                                @endcan

                                @can('toggle', $v)
                                    <form action="{{ route('admin.vehicles.toggle', $v) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" :variant="$v->status === 'active' ? 'danger' : 'success'">
                                            {{ $v->status === 'active' ? 'Заблокировать' : 'Активировать' }}
                                        </x-ui.button>
                                    </form>
                                @endcan

                                @can('delete', $v)
                                    <form action="{{ route('admin.vehicles.destroy', $v) }}" method="post"
                                        onsubmit="return confirm('Удалить авто?');">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="ghost">Удалить</x-ui.button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </x-ui.card>
@endsection
