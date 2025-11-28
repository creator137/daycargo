@props(['type' => 'success']) {{-- success | info | warning | danger --}}
@php
    $map = [
        'success' => 'bg-emerald-500 text-white',
        'info' => 'bg-sky-500 text-white',
        'warning' => 'bg-amber-500 text-white',
        'danger' => 'bg-rose-500 text-white',
    ];
@endphp
<div {{ $attributes->class("rounded-lg px-4 py-3 shadow-sm {$map[$type]}") }}>
    {{ $slot }}
</div>
