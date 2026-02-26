@props(['status' => 'new'])

@php
    $clsMap = [
        'new' => 'bg-blue-100 text-blue-800',
        'search' => 'bg-indigo-100 text-indigo-800',
        'assigned' => 'bg-emerald-100 text-emerald-800',
        'en_route' => 'bg-teal-100 text-teal-800',
        'loading' => 'bg-amber-100 text-amber-800',
        'in_progress' => 'bg-sky-100 text-sky-800',
        'waiting' => 'bg-slate-100 text-slate-800',
        'paused' => 'bg-violet-100 text-violet-800',
        'completed' => 'bg-green-100 text-green-800',
        'canceled' => 'bg-rose-100 text-rose-800',
        'failed' => 'bg-red-100 text-red-800',
    ];

    $ruMap = [
        'new' => 'Новый',
        'search' => 'Поиск',
        'assigned' => 'Назначен',
        'en_route' => 'К подаче',
        'loading' => 'Погрузка',
        'in_progress' => 'Выполняется',
        'waiting' => 'Ожидание',
        'paused' => 'Пауза',
        'completed' => 'Завершен',
        'canceled' => 'Отменен',
        'failed' => 'Срыв',
    ];

    $cls = $clsMap[$status] ?? 'bg-slate-100 text-slate-800';
    $label = $ruMap[$status] ?? $status;
@endphp

<span class="px-2 py-0.5 rounded text-xs font-medium {{ $cls }}">
    {{ $label }}
</span>
