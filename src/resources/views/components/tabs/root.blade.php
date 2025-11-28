@props([
    'active' => null, // ключ активной вкладки, например 'main'
    'class' => '',
])

<div {{ $attributes->merge(['class' => $class]) }} data-tabs-root
    @if ($active) data-active="{{ $active }}" @endif>
    {{ $slot }}
</div>
