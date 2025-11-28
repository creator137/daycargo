@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Группы исполнителей</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                {{-- Табы по статусам --}}
                @php $tabItems = [['key' => 'active', 'label' => 'Активные'], ['key' => 'blocked', 'label' => 'Заблокированные']]; @endphp
                <div class="flex items-center gap-1 text-sm">
                    @foreach ($tabItems as $tab)
                        @php $is = ($status === $tab['key']); @endphp
                        <a href="{{ request()->fullUrlWithQuery(['status' => $tab['key']]) }}"
                            class="px-3 py-1.5 rounded-lg border text-slate-700 {{ $is ? 'bg-indigo-50 border-indigo-200' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
                            {{ $tab['label'] }}
                            <span class="ml-1 text-slate-500">({{ $tabs[$tab['key']] ?? 0 }})</span>
                        </a>
                    @endforeach
                </div>

                <span class="text-sm text-slate-500">Всего: {{ $items->total() }}</span>

                @can('create', \App\Models\DriverGroup::class)
                    @if ($items->count() > 0)
                        <x-ui.button :href="route('admin.driver_groups.create')" variant="primary" size="sm">Добавить</x-ui.button>
                    @endif
                @endcan
            </div>
        </x-slot:actions>

        {{-- Фильтры --}}
        <form method="get" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            <x-form.select name="city" :options="['' => '—'] + $cities" :value="request('city')" label="Город" />
            <x-form.input name="profession" label="Профессия" :value="request('profession')" />
            <x-form.input name="name" label="Название" :value="request('name')" />
            <div class="flex items-end gap-2">
                <x-ui.button type="submit" size="sm">Фильтр</x-ui.button>
                <a href="{{ route('admin.driver_groups.index') }}" class="text-sm text-slate-600 hover:underline">Сброс</a>
            </div>
        </form>

        @if ($items->isEmpty())
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>Пока пусто.</span>
                @can('create', \App\Models\DriverGroup::class)
                    <x-ui.button :href="route('admin.driver_groups.create')" variant="primary" size="sm">Создать</x-ui.button>
                @endcan
            </div>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                'Название',
                ['label' => 'Город'],
                ['label' => 'Профессия'],
                ['label' => 'Класс авто'],
                ['label' => 'Приоритет', 'align' => 'right', 'width' => '100px'],
                ['label' => 'Сорт', 'align' => 'right', 'width' => '80px'],
                ['label' => 'Статус', 'width' => '110px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '280px'],
            ]">
                @foreach ($items as $g)
                    <tr>
                        <td>{{ $g->name }}</td>
                        <td>{{ $g->city ?: '—' }}</td>
                        <td>{{ $g->profession ?: '—' }}</td>
                        <td>{{ optional($g->vehicleType)->name ?: '—' }}</td>
                        <td class="text-right">{{ $g->priority }}</td>
                        <td class="text-right">{{ $g->sort }}</td>
                        <td>
                            <x-ui.badge :variant="$g->active ? 'success' : 'muted'">
                                {{ $g->active ? 'Активна' : 'Выключена' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $g)
                                    <x-ui.button :href="route('admin.driver_groups.edit', $g)" variant="primary" size="sm">Править</x-ui.button>
                                @endcan
                                @can('toggle', $g)
                                    <form action="{{ route('admin.driver_groups.toggle', $g) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$g->active ? 'ghost' : 'success'" size="sm">
                                            {{ $g->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan
                                @can('delete', $g)
                                    <form action="{{ route('admin.driver_groups.destroy', $g) }}" method="post"
                                        onsubmit="return confirm('Удалить группу?');">
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
