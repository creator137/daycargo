@props([
    // Если задан href — рендерим <a role="button">, иначе <button type="...">
    'href' => null,
    'type' => 'button',

    // Варианты на все CRUD-кейсы:
    // primary, secondary, success, warning, danger, gray, ghost (прозрачная с рамкой), link (ссылка-кнопка)
    'variant' => 'primary',

    // sm | md | lg
    'size' => 'md',

    // тянуться на всю ширину
    'block' => false,
])

@php
    $base = 'inline-flex items-center gap-2 rounded-lg font-medium transition
             focus:outline-none focus:ring-2 focus:ring-offset-1
             disabled:opacity-60 disabled:cursor-not-allowed';

    $sizes = [
        'sm' => 'text-sm px-3 py-1.5',
        'md' => 'text-sm px-4 py-2',
        'lg' => 'text-base px-5 py-2.5',
    ];

    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm',
        'secondary' => 'bg-slate-700 text-white hover:bg-slate-800 focus:ring-slate-500 shadow-sm',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 shadow-sm',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-400 shadow-sm',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-sm',
        'gray' => 'bg-slate-100 text-slate-900 hover:bg-slate-200 focus:ring-slate-400 border border-slate-200',
        'ghost' => 'bg-transparent text-slate-700 hover:bg-slate-100 border border-slate-300',
        'link' => 'bg-transparent text-indigo-600 hover:text-indigo-700 p-0', // без паддингов
    ];

    $classes = trim(
        $base .
            ' ' .
            ($sizes[$size] ?? $sizes['md']) .
            ' ' .
            ($variants[$variant] ?? $variants['primary']) .
            ($block ? ' w-full justify-center' : ''),
    );
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes)->merge(['role' => 'button']) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
