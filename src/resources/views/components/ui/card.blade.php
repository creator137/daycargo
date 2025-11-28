@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'border' => true,
    'elevated' => true,
])

@php
    $cls = 'rounded-xl bg-white ' . $padding;
    if ($border) {
        $cls .= ' border border-slate-300';
    }
    if ($elevated) {
        $cls .= ' shadow-sm';
    }
@endphp

<div {{ $attributes->class($cls) }}>
    @if ($title || $subtitle || isset($actions))
        <div class="flex items-center justify-between border-b border-slate-300 px-5 py-3">
            <div>
                @if ($title)
                    <h2 class="font-semibold text-slate-900">{{ $title }}</h2>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-slate-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex gap-2">
                    {{ $actions }} {{-- ИСПОЛЬЗУЕМ именованный слот, а не @yield --}}
                </div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
