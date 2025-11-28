@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Справочники — города</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">Всего: {{ $items->count() }}</span>
                @if ($items->isNotEmpty())
                    @can('create', \App\Models\City::class)
                        <x-ui.button :href="route('admin.cities.create')" variant="primary" size="sm">Добавить</x-ui.button>
                    @endcan
                @endif
            </div>
        </x-slot:actions>

        @if ($items->isEmpty())
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>Пока пусто.</span>
                @can('create', \App\Models\City::class)
                    <x-ui.button :href="route('admin.cities.create')" variant="primary" size="sm">Создать</x-ui.button>
                @endcan
            </div>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                ['label' => 'Сорт', 'align' => 'right', 'width' => '80px'],
                'Название',
                'Слаг',
                ['label' => 'Статус', 'width' => '110px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '260px'],
            ]">
                @foreach ($items as $g)
                    <tr>
                        <td class="text-right">{{ $g->sort }}</td>
                        <td>{{ $g->name }}</td>
                        <td class="text-slate-500">{{ $g->slug }}</td>
                        <td>
                            <x-ui.badge :variant="$g->active ? 'success' : 'muted'">
                                {{ $g->active ? 'Активен' : 'Выключен' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $g)
                                    <x-ui.button :href="route('admin.cities.edit', $g)" variant="primary" size="sm">Править</x-ui.button>
                                @endcan
                                @can('toggle', $g)
                                    <form action="{{ route('admin.cities.toggle', $g) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$g->active ? 'ghost' : 'success'" size="sm">
                                            {{ $g->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan
                                @can('delete', $g)
                                    <form action="{{ route('admin.cities.destroy', $g) }}" method="post"
                                        onsubmit="return confirm('Удалить город?');">
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
