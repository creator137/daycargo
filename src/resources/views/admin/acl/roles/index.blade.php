{{-- resources/views/admin/acl/roles/index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Роли и доступ</h1>

    <x-ui.card>
        <x-slot:actions>
            <x-ui.button :href="route('admin.acl.roles.create')" variant="primary" size="sm">
                Новая роль
            </x-ui.button>
        </x-slot:actions>

        @if ($roles->isEmpty())
            <x-ui.alert>Пока нет ролей.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Код (тех.)',
                'Название',
                'Описание',
                ['label' => 'Права', 'align' => 'right', 'width' => '100px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '220px'],
            ]">
                @foreach ($roles as $r)
                    <tr>
                        <td class="font-mono text-xs">{{ $r->name }}</td>
                        <td>{{ $r->display_name ?? '—' }}</td>
                        <td class="text-slate-500">{{ $r->description ?? '—' }}</td>
                        <td class="text-right">{{ $r->permissions()->count() }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <x-ui.button :href="route('admin.acl.roles.edit', $r)" size="sm" variant="primary">Править</x-ui.button>
                                <form action="{{ route('admin.acl.roles.destroy', $r) }}" method="post"
                                    onsubmit="return confirm('Удалить роль?');">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="danger">Удалить</x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>
@endsection
