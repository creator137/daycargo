@extends('layouts.admin')

@section('content')
    @php
        $client = $user->client_profile;
    @endphp

    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Управление ролями</h1>
            <p class="text-sm text-slate-600 mt-1">
                Редактирование ролей для пользователя <span class="font-medium">{{ $user->name ?: $user->email }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <x-ui.button :href="route('admin.acl.users.index')" size="sm">Список</x-ui.button>
            <x-ui.button :href="route('admin.acl.roles.index')" size="sm">Роли</x-ui.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Левая колонка: информация о пользователе --}}
        <div class="space-y-4">
            {{-- Пользователь --}}
            <x-ui.card>
                <div class="flex items-start justify-between mb-3">
                    <div class="text-sm font-medium text-slate-500 uppercase tracking-wide">Пользователь</div>
                    <x-ui.button :href="route('admin.acl.users.show', $user)" size="sm" variant="ghost">
                        Карточка →
                    </x-ui.button>
                </div>

                <div class="space-y-3">
                    <div>
                        <div class="font-semibold text-slate-900 text-lg">{{ $user->name ?: '—' }}</div>
                        <div class="text-slate-600 text-sm mt-1">{{ $user->email }}</div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">User ID:</span>
                            <span class="font-mono text-slate-900">{{ $user->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Создан:</span>
                            <span class="font-mono text-slate-900">{{ $user->created_at?->format('d.m.Y') }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            {{-- Клиент --}}
            <x-ui.card>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Клиент</div>

                @if ($client)
                    <div class="space-y-3">
                        <div>
                            <div class="font-semibold text-slate-900">{{ $client->full_name ?: '—' }}</div>
                            <div class="text-slate-600 text-sm mt-1">{{ $client->phone ?: '—' }}</div>
                            @if ($client->email)
                                <div class="text-slate-500 text-sm mt-1">{{ $client->email }}</div>
                            @endif
                        </div>

                        <div class="pt-3 border-t border-slate-200 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Тип:</span>
                                <span class="text-slate-900">
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
                                <span class="font-mono text-slate-900">{{ $client->id }}</span>
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
                    <div class="text-slate-500 text-sm">
                        <p>Клиент не привязан к этому пользователю</p>
                        <p class="text-xs text-slate-400 mt-2">
                            (ни по clients.user_id, ни по users.client_id)
                        </p>
                    </div>
                @endif
            </x-ui.card>

            {{-- Текущие роли (превью) --}}
            <x-ui.card>
                <div class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-3">Текущие роли</div>

                <div class="flex flex-wrap gap-2">
                    @forelse ($user->roles as $r)
                        <x-ui.badge variant="secondary">{{ $r->display_name ?? $r->name }}</x-ui.badge>
                    @empty
                        <span class="text-slate-400 text-sm">Роли не назначены</span>
                    @endforelse
                </div>
            </x-ui.card>
        </div>

        {{-- Правая колонка: форма редактирования ролей --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.acl.users.update', $user) }}">
                @csrf
                @method('PUT')

                <x-ui.card>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="font-semibold text-slate-900 text-lg">Назначение ролей</div>
                            <p class="text-sm text-slate-600 mt-1">Выберите роли для пользователя</p>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <button type="button" class="text-indigo-600 hover:text-indigo-700 font-medium"
                                onclick="document.querySelectorAll('#roles-grid input[type=checkbox]').forEach(cb => cb.checked = true)">
                                Выбрать все
                            </button>
                            <span class="text-slate-300">•</span>
                            <button type="button" class="text-slate-600 hover:text-slate-700 font-medium"
                                onclick="document.querySelectorAll('#roles-grid input[type=checkbox]').forEach(cb => cb.checked = false)">
                                Очистить
                            </button>
                        </div>
                    </div>

                    <div id="roles-grid" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach ($roles as $r)
                            @php
                                $label = $r->display_name ?? $r->name;
                                $isSelected = in_array($r->id, old('roles', $selected));
                            @endphp

                            <label
                                class="flex items-start gap-3 rounded-lg border-2 p-4 cursor-pointer transition-all {{ $isSelected ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50' }}">
                                <input type="checkbox" name="roles[]" value="{{ $r->id }}"
                                    class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    @checked($isSelected)>

                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-slate-900">{{ $label }}</div>
                                    <div class="text-xs text-slate-500 mt-1">
                                        <span class="font-mono">{{ $r->name }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @if ($errors->has('roles'))
                        <div class="mt-3 text-sm text-rose-600">
                            {{ $errors->first('roles') }}
                        </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-slate-200 flex items-center justify-between gap-4">
                        <div class="text-sm text-slate-600">
                            Выбрано ролей: <span class="font-semibold" id="selected-count">{{ count($selected) }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <x-ui.button :href="route('admin.acl.users.index')" size="sm">
                                Отмена
                            </x-ui.button>
                            <x-ui.button type="submit" variant="primary" size="sm">
                                Сохранить изменения
                            </x-ui.button>
                        </div>
                    </div>
                </x-ui.card>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Подсчет выбранных ролей
            document.addEventListener('DOMContentLoaded', function() {
                const checkboxes = document.querySelectorAll('#roles-grid input[type=checkbox]');
                const counter = document.getElementById('selected-count');

                function updateCounter() {
                    const count = Array.from(checkboxes).filter(cb => cb.checked).length;
                    counter.textContent = count;
                }

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', updateCounter);
                });
            });
        </script>
    @endpush
@endsection
