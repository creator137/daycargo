@props([
    'title' => null,
    'subtitle' => null,
])

<div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        @if ($title)
            <h1 class="text-xl font-semibold text-slate-900">{{ $title }}</h1>
        @endif
        @if ($subtitle)
            <div class="text-sm text-slate-600 mt-0.5">{{ $subtitle }}</div>
        @endif
    </div>

    {{-- правый слот для кнопок/действий --}}
    <div class="flex items-center gap-2">
        {{ $slot }}
    </div>
</div>
