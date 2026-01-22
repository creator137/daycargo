@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Справочник: Виды погрузки</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <x-ui.button :href="route('admin.vehicle_loading_types.create')" variant="primary" size="sm">
                    Добавить
                </x-ui.button>
            </div>
        </x-slot:actions>

        @if ($items->isEmpty())
            <x-ui.alert>Пока пусто.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Название',
                ['label' => 'Сорт', 'width' => '120px'],
                ['label' => 'Активен', 'width' => '120px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '320px'],
            ]">
                @foreach ($items as $it)
                    <tr>
                        <td class="font-medium">{{ $it->name }}</td>
                        <td>{{ $it->sort }}</td>
                        <td>{{ $it->active ? 'Да' : 'Нет' }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $it)
                                    <x-ui.button :href="route('admin.vehicle_loading_types.edit', $it)" variant="primary" size="sm">
                                        Править
                                    </x-ui.button>
                                @endcan

                                @can('toggle', $it)
                                    <form action="{{ route('admin.vehicle_loading_types.toggle', $it) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$it->active ? 'ghost' : 'success'" size="sm">
                                            {{ $it->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan

                                @can('delete', $it)
                                    <form action="{{ route('admin.vehicle_loading_types.destroy', $it) }}" method="post"
                                        onsubmit="return confirm('Удалить запись?');">
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
