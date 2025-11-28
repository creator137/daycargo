@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Справочники — типы авто</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">Всего: {{ $items->count() }}</span>
                @can('create', \App\Models\VehicleType::class)
                    <x-ui.button :href="route('admin.vehicle_types.create')" variant="primary" size="sm">
                        Добавить
                    </x-ui.button>
                @endcan
            </div>
        </x-slot:actions>

        @if ($items->isEmpty())
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>Данных пока нет.</span>
                @can('create', \App\Models\VehicleType::class)
                    <x-ui.button :href="route('admin.vehicle_types.create')" variant="primary" size="sm">
                        Создать
                    </x-ui.button>
                @endcan
            </div>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                ['label' => 'Код', 'width' => '72px'],
                'Название',
                ['label' => 'Длина, см', 'align' => 'right'],
                ['label' => 'Ширина, см', 'align' => 'right'],
                ['label' => 'Высота, см', 'align' => 'right'],
                ['label' => 'Г/под., кг', 'align' => 'right'],
                ['label' => 'Статус', 'width' => '96px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '260px'],
            ]">
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-right">{{ $item->length_cm }}</td>
                        <td class="text-right">{{ $item->width_cm }}</td>
                        <td class="text-right">{{ $item->height_cm }}</td>
                        <td class="text-right">{{ $item->capacity_kg }}</td>
                        <td>
                            <x-ui.badge :variant="$item->active ? 'success' : 'muted'">
                                {{ $item->active ? 'Активен' : 'Скрыт' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $item)
                                    <x-ui.button :href="route('admin.vehicle_types.edit', $item)" variant="primary" size="sm">
                                        Править
                                    </x-ui.button>
                                @endcan

                                @can('toggle', $item)
                                    <form action="{{ route('admin.vehicle_types.toggle', $item) }}" method="post">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" :variant="$item->active ? 'ghost' : 'success'" size="sm">
                                            {{ $item->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan

                                @can('delete', $item)
                                    <form action="{{ route('admin.vehicle_types.destroy', $item) }}" method="post"
                                        onsubmit="return confirm('Удалить тип авто?');">
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
@endsection
