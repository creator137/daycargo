@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Пользователи — роли</h1>

    <x-ui.card>
        <x-slot:actions>
            <form method="get" class="flex items-center gap-2">
                <x-form.input name="search" :value="request('search')" placeholder="Имя или email" />
                <x-ui.button type="submit" size="sm">Найти</x-ui.button>
                <x-ui.button :href="route('admin.acl.roles.index')" size="sm">Роли</x-ui.button>
                <x-ui.button :href="route('admin.acl.matrix.index')" size="sm">Матрица</x-ui.button>
            </form>
        </x-slot:actions>

        @if ($users->isEmpty())
            <x-ui.alert>Пока пусто.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Пользователь',
                'Email',
                'Роли',
                ['label' => 'Действия', 'align' => 'right', 'width' => '180px'],
            ]">
                @foreach ($users as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td class="text-slate-600">{{ $u->email }}</td>
                        <td class="text-xs">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($u->getRoleNames() as $r)
                                    <x-ui.badge variant="secondary">{{ $r }}</x-ui.badge>
                                @empty
                                    <span class="text-slate-400">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-right">
                            <x-ui.button :href="route('admin.acl.users.edit', $u)" size="sm" variant="primary">Править</x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">{{ $users->links() }}</div>
        @endif
    </x-ui.card>
@endsection
