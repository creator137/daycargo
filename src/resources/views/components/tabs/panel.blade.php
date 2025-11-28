@props([
    'name' => null, // ключ панели, например 'main'
    'active' => null, // строка с ключом активной вкладки ИЛИ bool
    'class' => '',
])

@php
    // SSR-фолбэк: если active — строка, показываем только совпадающую панель.
    // если active — bool, true = показать
    $isActive = is_string($active) ? $active === $name : (bool) $active;
@endphp

<section id="{{ $name }}" data-tab-panel="{{ $name }}"
    {{ $attributes->merge(['class' => trim(($isActive ? '' : 'hidden') . ' ' . $class)]) }}>
    {{ $slot }}
</section>
