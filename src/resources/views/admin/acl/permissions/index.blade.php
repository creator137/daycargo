@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Права</h1>

    <x-ui.card>
        <x-slot:actions>
            <form method="get" class="flex items-center gap-2">
                <x-form.input name="search" :value="request('search')" placeholder="Поиск по имени права" />
                <x-ui.button type="submit" size="sm">Найти</x-ui.button>
                <x-ui.button :href="route('admin.acl.permissions.create')" variant="primary" size="sm">Добавить</x-ui.button>
                <x-ui.button :href="route('admin.acl.roles.index')" size="sm">Роли</x-ui.button>
                <x-ui.button :href="route('admin.acl.matrix.index')" size="sm">Матрица</x-ui.button>
            </form>
        </x-slot:actions>

        @if ($items->isEmpty())
            <x-ui.alert>Пока пусто.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="['Право', ['label' => 'Действия', 'align' => 'right', 'width' => '240px']]">
                @foreach ($items as $p)
                    <tr>
                        <td class="font-mono text-sm">{{ $p->name }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <x-ui.button :href="route('admin.acl.permissions.edit', $p)" size="sm" variant="primary">Править</x-ui.button>
                                <form action="{{ route('admin.acl.permissions.destroy', $p) }}" method="post"
                                    onsubmit="return confirm('Удалить право?');">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="danger">Удалить</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">{{ $items->links() }}</div>
        @endif
    </x-ui.card>
@endsection
