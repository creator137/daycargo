@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Тарифы для клиентов</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">Всего: {{ $items->count() }}</span>
                @if ($items->isNotEmpty())
                    @can('create', \App\Models\ClientTariff::class)
                        <x-ui.button :href="route('admin.client_tariffs.create')" variant="primary" size="sm">Добавить</x-ui.button>
                    @endcan
                @endif
            </div>
        </x-slot:actions>

        @if ($items->isEmpty())
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>Пока пусто.</span>
                @can('create', \App\Models\ClientTariff::class)
                    <x-ui.button :href="route('admin.client_tariffs.create')" variant="primary" size="sm">Создать</x-ui.button>
                @endcan
            </div>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                'Название',
                ['label' => 'Группа'],
                ['label' => 'Класс авто'],
                ['label' => 'Город'],
                ['label' => 'Каналы'],
                ['label' => 'Сорт', 'align' => 'right', 'width' => '80px'],
                ['label' => 'Статус', 'width' => '100px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '280px'],
            ]">
                @foreach ($items as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ optional($row->group)->name ?: '—' }}</td>
                        <td>{{ optional($row->vehicleType)->name ?: '—' }}</td>
                        <td>{{ $row->city ?: '—' }}</td>
                        <td class="text-xs">
                            <div class="flex flex-wrap gap-1">
                                @if ($row->available_site)
                                    <x-ui.badge variant="secondary">Сайт</x-ui.badge>
                                @endif
                                @if ($row->available_app)
                                    <x-ui.badge variant="secondary">Приложение</x-ui.badge>
                                @endif
                                @if ($row->available_dispatcher)
                                    <x-ui.badge variant="secondary">Диспетчер</x-ui.badge>
                                @endif
                                @if ($row->available_driver)
                                    <x-ui.badge variant="secondary">Водитель</x-ui.badge>
                                @endif
                                @if ($row->available_cabinet)
                                    <x-ui.badge variant="secondary">ЛК</x-ui.badge>
                                @endif
                            </div>
                        </td>
                        <td class="text-right">{{ $row->sort }}</td>
                        <td>
                            <x-ui.badge :variant="$row->active ? 'success' : 'muted'">
                                {{ $row->active ? 'Активен' : 'Выключен' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $row)
                                    <x-ui.button :href="route('admin.client_tariffs.edit', $row)" variant="primary" size="sm">Править</x-ui.button>
                                @endcan
                                @can('toggle', $row)
                                    <form action="{{ route('admin.client_tariffs.toggle', $row) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$row->active ? 'ghost' : 'success'" size="sm">
                                            {{ $row->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan
                                @can('delete', $row)
                                    <form action="{{ route('admin.client_tariffs.destroy', $row) }}" method="post"
                                        onsubmit="return confirm('Удалить тариф?');">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">Удалить</x-ui.button>
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
