@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Матрица ролей и прав</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2 text-sm">
                <x-ui.button :href="route('admin.acl.roles.index')" size="sm">Роли</x-ui.button>
                <x-ui.button :href="route('admin.acl.permissions.index')" size="sm">Права</x-ui.button>
            </div>
        </x-slot:actions>

        <form method="post" action="{{ route('admin.acl.matrix.update') }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full border border-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-2 text-left border-b border-slate-200">Право</th>
                            @foreach ($roles as $role)
                                <th class="px-3 py-2 text-left border-b border-slate-200">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grouped as $group => $perms)
                            <tr>
                                <td colspan="{{ 1 + $roles->count() }}"
                                    class="px-3 py-2 bg-slate-100 font-medium text-slate-700 border-b border-slate-200">
                                    {{ strtoupper($group) }}
                                </td>
                            </tr>
                            @foreach ($perms as $perm)
                                <tr>
                                    <td class="px-3 py-2 border-b border-slate-200 font-mono">{{ $perm->name }}</td>
                                    @foreach ($roles as $role)
                                        @php $checked = $role->hasPermissionTo($perm->name); @endphp
                                        <td class="px-3 py-2 border-b border-slate-200">
                                            <label class="inline-flex items-center gap-2">
                                                <input type="checkbox"
                                                    name="matrix[{{ $perm->id }}][{{ $role->id }}]"
                                                    @checked($checked)
                                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <x-ui.button type="submit" variant="primary">Сохранить матрицу</x-ui.button>
            </div>
        </form>
    </x-ui.card>
@endsection
