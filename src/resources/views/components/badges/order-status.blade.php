@props(['status' => 'new'])

@php
    $clsMap = [
        'new' => 'bg-blue-100 text-blue-800',
        'assigning' => 'bg-indigo-100 text-indigo-800',
        'accepted' => 'bg-emerald-100 text-emerald-800',
        'arrived' => 'bg-teal-100 text-teal-800',
        'loading' => 'bg-amber-100 text-amber-800',
        'driving' => 'bg-sky-100 text-sky-800',
        'waiting' => 'bg-slate-100 text-slate-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-rose-100 text-rose-800',
        'failed' => 'bg-red-100 text-red-800',
        'refund' => 'bg-orange-100 text-orange-800',
    ];

    $ruMap = [
        'new' => 'Новый',
        'assigning' => 'Назначение',
        'accepted' => 'Принят',
        'arrived' => 'Подъехал',
        'loading' => 'Погрузка',
        'driving' => 'В пути',
        'waiting' => 'Ожидание',
        'completed' => 'Завершён',
        'cancelled' => 'Отменён',
        'failed' => 'Срыв',
        'refund' => 'Возврат',
    ];

    $cls = $clsMap[$status] ?? 'bg-slate-100 text-slate-800';
    $label = $ruMap[$status] ?? $status;
@endphp

<span class="px-2 py-0.5 rounded text-xs font-medium {{ $cls }}">
    {{ $label }}
</span>
