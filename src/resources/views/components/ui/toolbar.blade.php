@props([
    'sticky' => false,
    'class' => '',
])

@php
    $classes = 'flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2';
    if ($sticky) {
        $classes .= ' sticky top-0 z-10';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes . ' ' . $class]) }}>
    {{-- Левая часть (фильтры/заголовки/поиск) --}}
    <div class="flex-1">
        {{ $left ?? '' }}
    </div>

    {{-- Правая часть (кнопки действий) --}}
    <div class="ml-auto flex items-center gap-2">
        {{ $right ?? $slot }}
    </div>
</div>
