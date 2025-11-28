@props([
    'title' => null,
    'subtitle' => null,
    'stickyToolbar' => false,
])

<div class="space-y-4">
    @if ($title || $subtitle)
        <div class="px-2">
            <h1 class="text-lg font-semibold text-slate-800">{{ $title }}</h1>
            @isset($subtitle)
                <p class="text-slate-500 text-sm mt-1">{{ $subtitle }}</p>
            @endisset
        </div>
    @endif

    {{-- Тулбар (опционально) --}}
    @if (trim($toolbar ?? '') !== '')
        <x-ui.toolbar :sticky="$stickyToolbar">
            {{ $toolbar }}
        </x-ui.toolbar>
    @endif

    {{-- Контент листинга/таблица/фильтры --}}
    <x-ui.card class="p-0">
        {{ $slot }}
    </x-ui.card>

    {{-- Доп.панель ниже (например, пагинация) --}}
    @if (trim($footer ?? '') !== '')
        <div>
            {{ $footer }}
        </div>
    @endif
</div>
