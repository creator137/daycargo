@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Справочник: Типы кузова</h1>
        <x-ui.button :href="route('admin.vehicle_body_types.create')" size="sm" variant="primary">Добавить</x-ui.button>
    </div>

    <x-ui.table bordered hover compact :headers="[
        'Название',
        ['label' => 'Сорт', 'width' => '90px'],
        ['label' => 'Активен', 'width' => '90px'],
        ['label' => 'Действия', 'align' => 'right', 'width' => '260px'],
    ]">
        @foreach ($items as $it)
            <tr>
                <td class="font-medium">{{ $it->name }}</td>
                <td>{{ $it->sort }}</td>
                <td>{{ $it->active ? 'Да' : 'Нет' }}</td>
                <td class="text-right">
                    <div class="flex justify-end gap-2">
                        <x-ui.button :href="route('admin.vehicle_body_types.edit', $it)" size="sm" variant="primary">Править</x-ui.button>

                        <form method="post" action="{{ route('admin.vehicle_body_types.toggle', $it) }}">
                            @csrf @method('PATCH')
                            <x-ui.button type="submit" size="sm" :variant="$it->active ? 'ghost' : 'success'">
                                {{ $it->active ? 'Выключить' : 'Включить' }}
                            </x-ui.button>
                        </form>

                        <form method="post" action="{{ route('admin.vehicle_body_types.destroy', $it) }}"
                            onsubmit="return confirm('Удалить?');">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" size="sm" variant="danger">Удалить</x-ui.button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div class="mt-4">{{ $items->links() }}</div>
@endsection
