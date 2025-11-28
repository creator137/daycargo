@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">База клиентов</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                {{-- Табы --}}
                @php
                    $tabs = [
                        ['key' => 'registered', 'label' => 'Зарегистрированные', 'cnt' => $counts['registered'] ?? 0],
                        [
                            'key' => 'unregistered',
                            'label' => 'Незарегистрированные',
                            'cnt' => $counts['unregistered'] ?? 0,
                        ],
                    ];
                @endphp
                <div class="flex items-center gap-1 text-sm">
                    @foreach ($tabs as $t)
                        @php $is = ($tab === $t['key']); @endphp
                        <a href="{{ request()->fullUrlWithQuery(['tab' => $t['key']]) }}"
                            class="px-3 py-1.5 rounded-lg border {{ $is ? 'bg-indigo-50 border-indigo-200 text-slate-800' : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                            {{ $t['label'] }} <span class="text-slate-500">({{ $t['cnt'] }})</span>
                        </a>
                    @endforeach
                </div>

                @can('create', \App\Models\Client::class)
                    @if ($tab === 'registered')
                        <x-ui.button :href="route('admin.clients.create')" size="sm" variant="primary">Добавить</x-ui.button>
                    @endif
                @endcan
            </div>
        </x-slot:actions>

        {{-- Фильтры --}}
        <form method="get" class="mb-4 grid grid-cols-1 md:grid-cols-7 gap-3">
            <input type="hidden" name="tab" value="{{ $tab }}" />

            <x-form.select name="city" label="Город" :options="['' => '—'] + $citiesOptions" :value="request('city')" />
            <x-form.input name="search" label="Поиск (ФИО/тел/email)" :value="request('search')" />

            @if ($tab === 'registered')
                <x-form.select name="client_type" label="Тип" :options="['' => 'Все', 'person' => 'Физ', 'company' => 'Юр']" :value="request('client_type')" />
                <x-form.select name="role" label="Категория" :options="['' => 'Все', 'users' => 'Пользователи', 'agents' => 'Агенты']" :value="request('role')" />
                <x-form.select name="black" label="Чёрный список" :options="['' => 'Все', 'black' => 'В ЧС', 'white' => 'Не в ЧС']" :value="request('black')" />
            @endif

            <x-form.select name="period" label="Период" :options="['' => '—', 'today' => 'Сегодня', '7d' => '7 дней', '30d' => '30 дней']" :value="request('period')" />
            <x-form.date name="created_from" label="С даты" :value="request('created_from')" />
            <x-form.date name="created_to" label="По дату" :value="request('created_to')" />

            <div class="flex items-end gap-2 md:col-span-7">
                <x-ui.button type="submit">Показать</x-ui.button>
                <a href="{{ route('admin.clients.index', ['tab' => $tab]) }}"
                    class="text-sm text-slate-600 hover:underline">Сброс</a>
            </div>
        </form>

        {{-- Таблица --}}
        @if ($tab === 'unregistered')
            @if ($items->isEmpty())
                <x-ui.alert>Пока пусто.</x-ui.alert>
            @else
                <x-ui.table tone="bold" bordered hover compact :headers="[
                    ['label' => 'Дата регистрации'],
                    ['label' => 'Филиал/город'],
                    ['label' => 'Устройство'],
                    ['label' => 'Платформа'],
                    ['label' => 'Пуш токен'],
                ]">
                    @foreach ($items as $d)
                        <tr>
                            <td>{{ optional($d->created_at)->format('d.m.Y H:i') }}</td>
                            <td>{{ $d->city ?: '—' }}</td>
                            <td>{{ $d->device_model ?: '—' }}</td>
                            <td>{{ $d->platform ?: '—' }}</td>
                            <td class="truncate max-w-[280px]">
                                {{ \Illuminate\Support\Str::limit($d->push_token ?? '—', 28) }}</td>
                        </tr>
                    @endforeach
                </x-ui.table>
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        @else
            @if ($items->isEmpty())
                <x-ui.alert>Пока нет записей.</x-ui.alert>
            @else
                <x-ui.table tone="bold" bordered hover compact :headers="[
                    ['label' => 'Дата рег.'],
                    'Клиент',
                    ['label' => 'Заказы', 'align' => 'right', 'width' => '120px'],
                    'Город',
                    ['label' => 'ЧС', 'width' => '80px'],
                    ['label' => 'Действия', 'align' => 'right', 'width' => '260px'],
                ]">
                    @foreach ($items as $c)
                        <tr>
                            <td>{{ optional($c->created_at)->format('d.m.Y H:i') }}</td>
                            <td>
                                <div class="flex items-center gap-3">
                                    @if ($c->photo_url)
                                        <img src="{{ $c->photo_url }}" class="w-8 h-8 rounded-full object-cover border">
                                    @else
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs">
                                            {{ mb_substr($c->full_name ?? '—', 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $c->full_name ?: '—' }}</div>
                                        <div class="text-xs text-slate-500">
                                            <a href="tel:{{ $c->phone }}"
                                                class="hover:underline">{{ $c->phone }}</a>
                                            @if ($c->email)
                                                · <a href="mailto:{{ $c->email }}"
                                                    class="hover:underline">{{ $c->email }}</a>
                                            @endif
                                            @if ($c->client_type === 'company')
                                                · юр.лицо
                                            @endif
                                            @if ($c->is_agent)
                                                · агент
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-right">0 / 0</td> {{-- заменим на реальные счетчики когда появятся заказы --}}
                            <td>{{ $c->city ?: '—' }}</td>
                            <td>
                                <x-ui.badge :variant="$c->blacklisted ? 'danger' : 'muted'">
                                    {{ $c->blacklisted ? 'Да' : 'Нет' }}
                                </x-ui.badge>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @can('update', $c)
                                        <x-ui.button :href="route('admin.clients.edit', $c)" size="sm" variant="primary">Править</x-ui.button>
                                    @endcan
                                    @can('toggle', $c)
                                        <form method="post" action="{{ route('admin.clients.toggleBlacklist', $c) }}">
                                            @csrf @method('PATCH')
                                            <x-ui.button type="submit" size="sm" :variant="$c->blacklisted ? 'success' : 'danger'">
                                                {{ $c->blacklisted ? 'Убрать из ЧС' : 'В ЧС' }}
                                            </x-ui.button>
                                        </form>
                                    @endcan
                                    @can('delete', $c)
                                        <form method="post" action="{{ route('admin.clients.destroy', $c) }}"
                                            onsubmit="return confirm('Удалить клиента?');">
                                            @csrf @method('DELETE')
                                            <x-ui.button type="submit" size="sm" variant="ghost">Удалить</x-ui.button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
                <div class="mt-4">{{ $items->links() }}</div>
            @endif
        @endif
    </x-ui.card>
@endsection
