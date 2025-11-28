@props([
    'name',
    'label' => null,
    'value' => null, // Carbon|string (ожидаем YYYY-MM-DD)
    'required' => false,
    'disabled' => false,
    'min' => null,
    'max' => null,
    'hint' => null,
])

@php
    $id = $attributes->get('id') ?: 'd_' . \Illuminate\Support\Str::uuid();
    if ($value instanceof \Carbon\Carbon) {
        $value = $value->format('Y-m-d');
    }
@endphp

<div>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }} @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <input id="{{ $id }}" type="date" lang="ru" name="{{ $name }}" value="{{ old($name, $value) }}"
        @if ($min) min="{{ $min }}" @endif
        @if ($max) max="{{ $max }}" @endif
        {{ $attributes->class('block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm') }}
        @required($required) @disabled($disabled) />

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
