@extends('layouts.admin')

@section('content')
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Пользователи</h1>
            <p class="text-sm text-slate-600 mt-1">
                Управление ролями и привязкой к клиентским данным
            </p>
        </div>

        <x-ui.button :href="route('admin.acl.roles.index')" size="sm">
            Управление ролями
        </x-ui.button>
    </div>

    {{-- Статистика --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Всего</div>
            <div class="text-2xl font-semibold text-slate-900 mt-1">{{ $stats['total'] }}</div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">С ролями</div>
            <div class="text-2xl font-semibold text-indigo-600 mt-1">{{ $stats['with_roles'] }}</div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Без ролей</div>
            <div class="text-2xl font-semibold text-amber-600 mt-1">{{ $stats['without_roles'] }}</div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">С клиентом</div>
            <div class="text-2xl font-semibold text-emerald-600 mt-1">{{ $stats['with_client'] }}</div>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">В ЧС</div>
            <div class="text-2xl font-semibold text-rose-600 mt-1">{{ $stats['blacklisted'] }}</div>
        </div>
    </div>

    {{-- Фильтры --}}
    <x-ui.card class="mb-4">
        <form method="get" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Поиск --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Поиск</label>
                    <x-form.input name="search" :value="request('search')" placeholder="Имя, email, ФИО клиента, телефон..."
                        class="w-full" />
                </div>

                {{-- Роль --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Роль</label>
                    <select name="role_id"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Все роли</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}" @selected((string) request('role_id') === (string) $r->id)>
                                {{ $r->display_name ?? $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Тип --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Тип пользователя</label>
                    <select name="type"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="any" @selected(request('type', 'any') === 'any')>Любой</option>
                        <option value="physical" @selected(request('type') === 'physical')>Клиент (физ.)</option>
                        <option value="legal" @selected(request('type') === 'legal')>Клиент (юр.)</option>
                        <option value="admin" @selected(request('type') === 'admin')>Админ</option>
                        <option value="owner" @selected(request('type') === 'owner')>Владелец</option>
                        <option value="accountant" @selected(request('type') === 'accountant')>Бухгалтер</option>
                        <option value="viewer" @selected(request('type') === 'viewer')>Наблюдатель</option>
                        <option value="driver" @selected(request('type') === 'driver')>Водитель</option>
                        <option value="none" @selected(request('type') === 'none')>Без ролей</option>
                    </select>
                </div>

                {{-- Сортировка --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Сортировка</label>
                    <div class="flex gap-2">
                        <select name="sort_by"
                            class="flex-1 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="id" @selected(request('sort_by', 'id') === 'id')>ID</option>
                            <option value="name" @selected(request('sort_by') === 'name')>Имя</option>
                            <option value="email" @selected(request('sort_by') === 'email')>Email</option>
                            <option value="created_at" @selected(request('sort_by') === 'created_at')>Дата создания</option>
                        </select>
                        <select name="sort_dir"
                            class="w-24 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="desc" @selected(request('sort_dir', 'desc') === 'desc')>↓</option>
                            <option value="asc" @selected(request('sort_dir') === 'asc')>↑</option>
                        </select>
                    </div>
                </div>

                {{-- Чекбоксы --}}
                <div class="lg:col-span-3 flex flex-wrap items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="has_client" value="1"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            @checked(request()->boolean('has_client'))>
                        <span>Только с клиентом</span>
                    </label>

                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="blacklisted" value="1"
                            class="rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                            @checked(request()->boolean('blacklisted'))>
                        <span>В чёрном списке</span>
                    </label>
                </div>
            </div>

            {{-- Кнопки --}}
            <div class="flex items-center gap-2">
                <x-ui.button type="submit" variant="primary" size="sm">
                    Применить фильтры
                </x-ui.button>

                @if (request()->query())
                    <x-ui.button :href="route('admin.acl.users.index')" size="sm">
                        Сбросить
                    </x-ui.button>
                @endif

                <div class="ml-auto text-sm text-slate-500">
                    Найдено: {{ $users->total() }}
                </div>
            </div>
        </form>
    </x-ui.card>

    {{-- Список пользователей --}}
    @if ($users->isEmpty())
        <x-ui.card>
            <div class="text-center py-12">
                <div class="text-slate-400 text-4xl mb-3">👤</div>
                <div class="text-slate-600 font-medium">Пользователи не найдены</div>
                <p class="text-sm text-slate-500 mt-1">Попробуйте изменить параметры фильтрации</p>
            </div>
        </x-ui.card>
    @else
        <div class="space-y-3">
            @foreach ($users as $u)
                @php
                    $client = $u->client_profile;
                    $clientType = $client?->client_type;
                    $typeLabel = match ($clientType) {
                        'person' => 'Физ. лицо',
                        'org' => 'Юр. лицо',
                        'guest' => 'Гость',
                        default => null,
                    };
                    $roleNames = $u->roles->map(fn($r) => $r->display_name ?? $r->name)->values();
                @endphp

                <div
                    class="bg-white border border-slate-200 rounded-lg p-4 hover:shadow-md hover:border-slate-300 transition-all">
                    <div class="flex items-start justify-between gap-4">
                        {{-- Основная информация --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <h3 class="font-semibold text-slate-900 truncate">
                                    {{ $u->name ?: $u->email }}
                                </h3>

                                @if ($typeLabel)
                                    <x-ui.badge variant="secondary">{{ $typeLabel }}</x-ui.badge>
                                @endif

                                @if ($client?->blacklisted)
                                    <x-ui.badge variant="danger">ЧС</x-ui.badge>
                                @endif

                                @if ($client?->is_agent)
                                    <x-ui.badge variant="primary">Агент</x-ui.badge>
                                @endif
                            </div>

                            <div class="text-sm text-slate-600 mb-3">{{ $u->email }}</div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                {{-- Клиент --}}
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Клиент
                                    </div>

                                    @if ($client)
                                        <div class="font-medium text-slate-900">
                                            {{ $client->full_name ?: '—' }}
                                        </div>
                                        <div class="text-sm text-slate-600 mt-1">
                                            {{ $client->phone ?: '—' }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-2">
                                            ID: {{ $client->id }}
                                        </div>
                                    @else
                                        <div class="text-slate-500 text-sm">Не привязан</div>
                                    @endif
                                </div>

                                {{-- Роли --}}
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Роли</div>

                                    @if ($roleNames->count())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($roleNames as $r)
                                                <x-ui.badge variant="secondary">{{ $r }}</x-ui.badge>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-slate-500 text-sm">Нет ролей</div>
                                    @endif
                                </div>

                                {{-- Системное --}}
                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-2">Системное
                                    </div>
                                    <div class="text-sm text-slate-700 space-y-1">
                                        <div>ID: <span class="font-mono">{{ $u->id }}</span></div>
                                        <div>Создан: <span
                                                class="font-mono">{{ optional($u->created_at)->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Действия --}}
                        <div class="flex flex-col gap-2">
                            <x-ui.button :href="route('admin.acl.users.edit', $u)" size="sm" variant="primary">
                                Роли
                            </x-ui.button>

                            <x-ui.button :href="route('admin.acl.users.show', $u)" size="sm">
                                Карточка
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Пагинация --}}
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
@endsection
