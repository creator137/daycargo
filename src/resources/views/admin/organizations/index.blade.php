@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Организации</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex flex-wrap items-center gap-2">
                {{-- Табы по статусу --}}
                @php $status = $status ?? 'active'; @endphp
                <a href="{{ route('admin.organizations.index', ['status' => 'active'] + request()->except('page')) }}"
                    class="px-3 py-1.5 rounded-md text-sm {{ $status === 'active' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Активные <span class="opacity-70">({{ $tabs['active'] ?? 0 }})</span>
                </a>
                <a href="{{ route('admin.organizations.index', ['status' => 'blocked'] + request()->except('page')) }}"
                    class="px-3 py-1.5 rounded-md text-sm {{ $status === 'blocked' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Заблокированные <span class="opacity-70">({{ $tabs['blocked'] ?? 0 }})</span>
                </a>

                <div class="w-px h-6 bg-slate-200 mx-1"></div>

                {{-- Фильтры --}}
                <form method="get" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <x-form.select name="city" :options="['' => 'Все города'] + $cities" :value="request('city')" />
                    <x-form.input name="name" :value="request('name')" placeholder="Название / ИНН / контакт" />
                    <x-ui.button type="submit" size="sm">Показать</x-ui.button>
                    <a href="{{ route('admin.organizations.index', ['status' => $status]) }}"
                        class="text-sm text-slate-600 hover:underline">Сброс</a>
                </form>

                <div class="flex-1"></div>

                @can('create', App\Models\Organization::class)
                    <x-ui.button :href="route('admin.organizations.create')" variant="primary" size="sm">
                        Добавить организацию
                    </x-ui.button>
                @endcan
            </div>
        </x-slot:actions>

        @if ($items->isEmpty())
            <x-ui.alert>Ничего не найдено.</x-ui.alert>
        @else
            <x-ui.table tone="bold" bordered hover compact :headers="[
                'Название',
                'Город',
                'Код ЭДО',
                'Договор',
                ['label' => 'Баланс', 'align' => 'right', 'width' => '120px'],
                ['label' => 'Статус', 'align' => 'center', 'width' => '120px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '300px'],
            ]">
                @foreach ($items as $org)
                    <tr>
                        <td>
                            <div class="font-medium">{{ $org->full_name }}</div>
                            <div class="text-xs text-slate-500">
                                @if ($org->short_name)
                                    {{ $org->short_name }} ·
                                @endif
                                @if ($org->inn)
                                    ИНН: {{ $org->inn }}
                                @else
                                    —
                                @endif
                            </div>
                        </td>
                        <td>{{ $org->city ?? '—' }}</td>
                        <td>{{ $org->edo_code ?? '—' }}</td>
                        <td class="text-sm">
                            @if ($org->contract_number)
                                № {{ $org->contract_number }}
                                <div class="text-xs text-slate-500">
                                    {{ optional($org->contract_from)->format('d.m.Y') ?? '—' }}
                                    —
                                    {{ optional($org->contract_to)->format('d.m.Y') ?? '—' }}
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">
                            {{ number_format((float) $org->balance, 2, ',', ' ') }} ₽
                        </td>
                        <td class="text-center">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $org->active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $org->active ? 'Активна' : 'Заблок.' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex flex-wrap justify-end gap-2">
                                @can('employees', $org)
                                    <x-ui.button :href="route('admin.organizations.employees', $org)" size="sm">Сотрудники</x-ui.button>
                                @endcan
                                @can('update', $org)
                                    <x-ui.button :href="route('admin.organizations.edit', $org)" size="sm" variant="primary">Править</x-ui.button>
                                @endcan
                                @can('toggle', $org)
                                    <form method="post" action="{{ route('admin.organizations.toggle', $org) }}">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" size="sm" :variant="$org->active ? 'ghost' : 'success'">
                                            {{ $org->active ? 'Заблок.' : 'Актив.' }}
                                        </x-ui.button>
                                    </form>
                                @endcan
                                @can('delete', $org)
                                    <form method="post" action="{{ route('admin.organizations.destroy', $org) }}"
                                        onsubmit="return confirm('Удалить организацию?');">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" size="sm" variant="danger">Удалить</x-ui.button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            @if (method_exists($items, 'links'))
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        @endif
    </x-ui.card>
@endsection
