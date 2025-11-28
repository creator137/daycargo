@props([
    'tabs' => [], // [['key'=>'main','label'=>'Основное'], ...]
    'active' => null, // 'main'
])

@php
    $tabs = is_array($tabs) ? $tabs : [];
    $activeKey = is_string($active) ? $active : null;
@endphp

<div class="border-b border-slate-200 mb-4">
    <nav class="-mb-px flex flex-wrap gap-2" role="tablist">
        @foreach ($tabs as $t)
            @php
                $key = $t['key'] ?? Str::slug($t['label'] ?? 'tab');
                $label = $t['label'] ?? $key;
                $on = $activeKey === $key;
            @endphp
            <a href="#{{ $key }}" role="tab" data-tab="{{ $key }}"
                aria-selected="{{ $on ? 'true' : 'false' }}"
                class="px-3 py-2 text-sm border-b-2
                 {{ $on
                     ? 'border-indigo-600 text-indigo-700 font-medium'
                     : 'border-transparent text-slate-600 hover:text-slate-800 hover:border-slate-300' }}">
                {{ $label }}
            </a>
        @endforeach
        {{ $slot }}
    </nav>
</div>
