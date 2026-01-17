@extends('layouts.admin')

@section('content')
    @php
        $client = $user->client_profile;
    @endphp

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Карточка пользователя</h1>
            <p class="text-sm text-slate-600 mt-1">
                Подробная информация о пользователе и его клиентском профиле
            </p>
        </div>

        <div class="flex gap-2">
            <x-ui.button :href="route('admin.acl.users.edit', $user)" variant="primary" size="sm">
                Редактировать роли
            </x-ui.button>
            <x-ui.button :href="route('admin.acl.users.index')" size="sm">
                К списку
            </x-ui.button>
        </div>
    </div>

    {{-- Основная информация в 3 колонки --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Пользователь --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">Пользователь</div>
                <div
                    class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                    {{ mb_substr($user->name ?: $user->email, 0, 1) }}
                </div>
            </div>

            <div class="space-y-3">
                <div>
                    <div class="font-semibold text-slate-900 text-lg">{{ $user->name ?: 'Без имени' }}</div>
                    <div class="text-slate-600 text-sm mt-1">{{ $user->email }}</div>
                </div>

                <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">User ID:</span>
                        <span class="font-mono text-slate-900 font-medium">{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Client ID:</span>
                        <span class="font-mono text-slate-900 font-medium">{{ $user->client_id ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Создан:</span>
                        <span class="font-mono text-slate-900">{{ $user->created_at?->format('d.m.Y H:i') }}</span>
                    </div>
                    @if ($user->updated_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Обновлен:</span>
                            <span class="font-mono text-slate-900">{{ $user->updated_at?->format('d.m.Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.card>

        {{-- Клиент --}}
        <x-ui.card>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Клиентский профиль</div>

            @if ($client)
                <div class="space-y-3">
                    <div>
                        <div class="font-semibold text-slate-900 text-lg">{{ $client->full_name ?: 'Без имени' }}</div>
                        <div class="text-slate-600 text-sm mt-1">{{ $client->phone ?: 'Телефон не указан' }}</div>
                        @if ($client->email)
                            <div class="text-slate-500 text-sm mt-1">{{ $client->email }}</div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Тип клиента:</span>
                            <span class="text-slate-900 font-medium">
                                @switch($client->client_type)
                                    @case('person')
                                        Физ. лицо
                                    @break

                                    @case('org')
                                        Юр. лицо
                                    @break

                                    @case('guest')
                                        Гость
                                    @break

                                    @default
                                        —
                                @endswitch
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Client ID:</span>
                            <span class="font-mono text-slate-900 font-medium">{{ $client->id }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex flex-wrap gap-2">
                        @if ($client->blacklisted)
                            <x-ui.badge variant="danger">В чёрном списке</x-ui.badge>
                        @else
                            <x-ui.badge variant="success">Активен</x-ui.badge>
                        @endif

                        @if ($client->is_agent)
                            <x-ui.badge variant="primary">Агент</x-ui.badge>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8">
                    <div class="text-slate-400 text-4xl mb-3">👤</div>
                    <div class="text-slate-600 font-medium">Клиент не привязан</div>
                    <p class="text-xs text-slate-400 mt-2 text-center">
                        Ни по clients.user_id, ни по users.client_id
                    </p>
                </div>
            @endif
        </x-ui.card>

        {{-- Роли и права --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">Роли и права</div>
                <x-ui.button :href="route('admin.acl.users.edit', $user)" size="sm" variant="ghost">
                    Изменить →
                </x-ui.button>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="text-sm text-slate-600 mb-2">Назначенные роли:</div>
                    <div class="flex flex-wrap gap-2">
                        @forelse($user->getRoleNames() as $r)
                            <x-ui.badge variant="secondary">{{ $r }}</x-ui.badge>
                        @empty
                            <span class="text-slate-400 text-sm">Роли не назначены</span>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200">
                    <div class="text-sm text-slate-600">Система прав:</div>
                    <div class="text-sm text-slate-900 mt-1">Spatie Permission</div>
                    <div class="text-xs text-slate-400 mt-1">
                        Права управляются через роли
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Дополнительная информация --}}
    @if ($client)
        <x-ui.card>
            <div class="text-lg font-semibold text-slate-900 mb-4">Детальная информация о клиенте</div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {{-- Контакты --}}
                <div>
                    <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Контакты</div>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-slate-600">Телефон:</span>
                            <div class="font-medium text-slate-900">{{ $client->phone ?: '—' }}</div>
                        </div>
                        @if ($client->email)
                            <div>
                                <span class="text-slate-600">Email:</span>
                                <div class="font-medium text-slate-900">{{ $client->email }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Статус --}}
                <div>
                    <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Статус</div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            @if ($client->blacklisted)
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                                <span class="text-sm text-slate-900">В чёрном списке</span>
                            @else
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-sm text-slate-900">Активный клиент</span>
                            @endif
                        </div>
                        @if ($client->is_agent)
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                <span class="text-sm text-slate-900">Агент</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Системные данные --}}
                <div>
                    <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Системные данные</div>
                    <div class="space-y-2 text-sm">
                        <div>
                            <span class="text-slate-600">Тип:</span>
                            <div class="font-medium text-slate-900">{{ $client->client_type ?? '—' }}</div>
                        </div>
                        <div>
                            <span class="text-slate-600">Client ID:</span>
                            <div class="font-mono text-slate-900">{{ $client->id }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>
    @endif

    {{-- Действия --}}
    <x-ui.card>
        <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Доступные действия</div>

        <div class="flex flex-wrap gap-3">
            <x-ui.button :href="route('admin.acl.users.edit', $user)" variant="primary">
                Управление ролями
            </x-ui.button>

            <x-ui.button :href="route('admin.acl.users.index')">
                Вернуться к списку
            </x-ui.button>

            @if ($client)
                {{-- Можно добавить ссылку на карточку клиента, если она есть --}}
                {{-- <x-ui.button :href="route('admin.clients.show', $client)">Карточка клиента</x-ui.button> --}}
            @endif
        </div>
    </x-ui.card>
@endsection
