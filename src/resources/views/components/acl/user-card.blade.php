@props(['u'])

@php
    $client = $u->client_profile;
    $clientType = $client?->client_type; // person|org|guest|null
    $typeLabel = match ($clientType) {
        'person' => 'Физ. лицо',
        'org' => 'Юр. лицо',
        'guest' => 'Гость',
        default => '—',
    };

    $roleNames = $u->roles->map(fn($r) => $r->display_name ?? $r->name)->values();
@endphp

<div class="border border-slate-200 rounded-xl p-4 bg-white hover:shadow-sm transition">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <div class="font-semibold text-slate-900 truncate">
                    {{ $u->name ?: $u->email }}
                </div>

                @if ($clientType)
                    <x-ui.badge variant="secondary">{{ $typeLabel }}</x-ui.badge>
                @endif

                @if ($client?->blacklisted)
                    <x-ui.badge variant="danger">В чёрном списке</x-ui.badge>
                @endif
            </div>

            <div class="text-sm text-slate-600 mt-1 truncate">
                {{ $u->email }}
            </div>

            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                {{-- Клиент --}}
                <div class="rounded-lg bg-slate-50 p-3">
                    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Клиент</div>

                    @if ($client)
                        <div class="font-medium text-slate-900 truncate">
                            {{ $client->full_name ?: '—' }}
                        </div>
                        <div class="text-slate-600 mt-1">
                            {{ $client->phone ?: '—' }}
                        </div>
                        <div class="text-xs text-slate-400 mt-2">
                            {{ $clientType }} • client_id: {{ $client->id }}
                        </div>
                    @else
                        <div class="text-slate-500">Клиент не привязан</div>
                    @endif
                </div>

                {{-- Роли --}}
                <div class="rounded-lg bg-slate-50 p-3">
                    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Роли</div>
                    @if ($roleNames->count())
                        <div class="flex flex-wrap gap-1">
                            @foreach ($roleNames as $r)
                                <x-ui.badge variant="secondary">{{ $r }}</x-ui.badge>
                            @endforeach
                        </div>
                    @else
                        <div class="text-slate-500">—</div>
                    @endif
                </div>

                {{-- Метаданные --}}
                <div class="rounded-lg bg-slate-50 p-3">
                    <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">Системное</div>
                    <div class="text-slate-700">user_id: <span class="font-mono">{{ $u->id }}</span></div>
                    <div class="text-slate-700">client_id: <span class="font-mono">{{ $u->client_id ?: '—' }}</span>
                    </div>
                    <div class="text-slate-700">created: <span
                            class="font-mono">{{ optional($u->created_at)->format('Y-m-d') }}</span></div>
                </div>
            </div>
        </div>

        <div class="shrink-0 flex flex-col gap-2">
            <x-ui.button :href="route('admin.acl.users.edit', $u)" size="sm" variant="primary">
                Роли
            </x-ui.button>

            <x-ui.button :href="route('admin.acl.users.show', $u)" size="sm">
                Карточка
            </x-ui.button>

            {{-- Опционально: Войти как (добавим позже, если хочешь) --}}
            {{-- <x-ui.button type="submit" size="sm" variant="secondary">Войти как</x-ui.button> --}}
        </div>
    </div>
</div>
