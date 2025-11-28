@props([
    'variant' => null, // success|muted|danger|warning|info|slate|emerald|rose|amber|indigo|green|gray
    'color' => null, // синоним
    'solid' => false, // если true — заливка плотнее
])

@php
    $k = $variant ?? ($color ?? 'slate');

    // нормализуем алиасы
    $aliases = [
        'success' => 'emerald',
        'danger' => 'rose',
        'warning' => 'amber',
        'info' => 'indigo',
        'muted' => 'slate',
        'green' => 'emerald',
        'gray' => 'slate',
    ];
    $tone = $aliases[$k] ?? $k;

    $soft = [
        'slate' => 'bg-slate-100 text-slate-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'amber' => 'bg-amber-100 text-amber-800',
        'indigo' => 'bg-indigo-100 text-indigo-700',
    ];
    $hard = [
        'slate' => 'bg-slate-600 text-white',
        'emerald' => 'bg-emerald-600 text-white',
        'rose' => 'bg-rose-600 text-white',
        'amber' => 'bg-amber-600 text-white',
        'indigo' => 'bg-indigo-600 text-white',
    ];

    $cls = $solid ? $hard[$tone] ?? $hard['slate'] : $soft[$tone] ?? $soft['slate'];
@endphp

<span {{ $attributes->class('inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full ' . $cls) }}>
    {{ $slot }}
</span>
