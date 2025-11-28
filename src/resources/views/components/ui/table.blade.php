@props([
    'headers' => null, // ['Колонка', ['label'=>'Кол','align'=>'right','width'=>'80px']]
    'caption' => null,

    'striped' => true,
    'bordered' => true,
    'hover' => true,
    'dense' => false,
    'compact' => false, // алиас dense
    'sticky' => false,
    'maxHeight' => null,

    'tone' => 'mid', // soft | mid | bold
    'borderThick' => false, // НОВОЕ: сделать линии толще (2px)
])

@php
    $dense = $dense || $compact;

    $pal = [
        'soft' => [
            'border' => 'border-slate-200',
            'divider' => 'divide-slate-200',
            'theadBg' => 'bg-slate-50',
            'theadTx' => 'text-slate-600',
            'zebra' => 'bg-slate-50/40',
            'hover' => 'bg-slate-50/80',
        ],
        'mid' => [
            'border' => 'border-slate-300',
            'divider' => 'divide-slate-300',
            'theadBg' => 'bg-slate-100',
            'theadTx' => 'text-slate-700',
            'zebra' => 'bg-slate-100/50',
            'hover' => 'bg-slate-100/80',
        ],
        'bold' => [
            'border' => 'border-slate-400',
            'divider' => 'divide-slate-400',
            'theadBg' => 'bg-slate-200',
            'theadTx' => 'text-slate-800',
            'zebra' => 'bg-slate-200/50',
            'hover' => 'bg-slate-200/70',
        ],
    ][$tone] ?? [
        'border' => 'border-slate-300',
        'divider' => 'divide-slate-300',
        'theadBg' => 'bg-slate-100',
        'theadTx' => 'text-slate-700',
        'zebra' => 'bg-slate-100/50',
        'hover' => 'bg-slate-100/80',
    ];

    $pad = $dense ? 'px-3 py-2' : 'px-4 py-3';

    $wrapperClass = 'overflow-x-auto' . ($sticky || $maxHeight ? ' relative' : '');

    // Толщина линий
    $B_OUT = $borderThick ? 'border-2' : 'border';
    $B_X = $borderThick ? 'border-x-2' : 'border-x';
    $B_B = $borderThick ? 'border-b-2' : 'border-b';

    $tableClass = 'w-full min-w-full table-auto text-sm';
    if ($bordered) {
        $tableClass .= " $B_OUT {$pal['border']}";
        $tableClass .= " [&>thead>tr>th]:$B_B [&>thead>tr>th]:{$pal['border']}";
        $tableClass .= " [&>tbody>tr>td]:$B_B [&>tbody>tr>td]:{$pal['border']}";
        $tableClass .= " [&>thead>tr>th]:$B_X [&>tbody>tr>td]:$B_X";
    }

    $theadClass =
        'text-xs uppercase tracking-wide ' .
        $pal['theadTx'] .
        ' ' .
        $pal['theadBg'] .
        ' ' .
        $pad .
        ($sticky ? ' sticky top-0 z-10' : '');

    $tbodyClass = 'text-slate-800 divide-y ' . $pal['divider'];
    if ($striped) {
        $tbodyClass .= ' [&>tr:nth-child(even)]:' . $pal['zebra'];
    }
    if ($hover) {
        $tbodyClass .= ' [&>tr:hover]:' . $pal['hover'];
    }

    $tbodyPad = $dense ? ' [&>tr>td]:px-3 [&>tr>td]:py-2' : ' [&>tr>td]:px-4 [&>tr>td]:py-3';

    $thPad = $pad . ' text-left whitespace-nowrap';
@endphp

<div class="{{ $wrapperClass }}" @if ($maxHeight) style="max-height: {{ $maxHeight }};" @endif>
    <table {{ $attributes->except(['headers', 'tone', 'compact'])->class($tableClass) }}>
        @if ($caption)
            <caption class="text-left text-sm text-slate-500 mt-2 mb-3">{{ $caption }}</caption>
        @endif

        @if ($headers)
            <thead class="{{ $theadClass }}">
                <tr>
                    @foreach ($headers as $h)
                        @php
                            $label = is_array($h) ? $h['label'] ?? '' : (string) $h;
                            $align = is_array($h) ? $h['align'] ?? 'left' : 'left';
                            $width = is_array($h) ? $h['width'] ?? null : null;
                            $cls = $thPad . ' text-' . $align;
                        @endphp
                        <th class="{{ $cls }}"
                            @if ($width) style="width: {{ $width }};" @endif>
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="{{ $tbodyClass }}{{ $tbodyPad }}">
            {{ $slot }}
        </tbody>
    </table>
</div>
