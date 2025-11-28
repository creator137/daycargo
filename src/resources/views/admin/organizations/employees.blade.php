@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">
        Сотрудники: {{ $organization->short_name ?: $organization->full_name }}
    </h1>

    <x-ui.card>
        <x-slot:actions>
            @can('employees', $organization)
                <form method="post" action="{{ route('admin.organizations.employees.attach', $organization) }}"
                    class="flex flex-wrap items-end gap-2">
                    @csrf
                    <x-form.input name="phone" label="Телефон клиента" placeholder="+7999..." required />
                    <x-form.input name="personal_limit" type="number" step="0.01" label="Перс. лимит, ₽" />
                    <label class="inline-flex items-center gap-2 text-sm mb-2">
                        <input type="checkbox" name="is_admin"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Администратор</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm mb-2">
                        <input type="checkbox" name="active" checked
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Активен</span>
                    </label>
                    <x-ui.button type="submit" size="sm" variant="primary">Добавить</x-ui.button>
                </form>
            @endcan
        </x-slot:actions>

        @if ($employees->isEmpty())
            <x-ui.alert>Сотрудников пока нет.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Клиент',
                'Телефон',
                ['label' => 'Админ', 'align' => 'center', 'width' => '80px'],
                ['label' => 'Активен', 'align' => 'center', 'width' => '90px'],
                ['label' => 'Лимит', 'align' => 'right', 'width' => '120px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '130px'],
            ]">
                @foreach ($employees as $client)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $client->full_name ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $client->email ?? '—' }}</div>
                        </td>
                        <td>{{ $client->phone }}</td>
                        <td class="text-center">{{ $client->pivot?->is_admin ? 'Да' : 'Нет' }}</td>
                        <td class="text-center">{{ $client->pivot?->active ? 'Да' : 'Нет' }}</td>
                        <td class="text-right">
                            {{ number_format((float) ($client->pivot?->personal_limit ?? 0), 2, ',', ' ') }} ₽
                        </td>
                        <td class="text-right">
                            @can('employees', $organization)
                                <form method="post"
                                    action="{{ route('admin.organizations.employees.detach', [$organization, $client]) }}"
                                    onsubmit="return confirm('Убрать сотрудника из организации?');">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="danger">Убрать</x-ui.button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            @if (method_exists($employees, 'links'))
                <div class="mt-4">{{ $employees->links() }}</div>
            @endif
        @endif
    </x-ui.card>
@endsection
