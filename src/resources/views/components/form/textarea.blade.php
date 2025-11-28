@props([
    'name',
    'label' => null,
    'value' => null,
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'help' => null,
])

@php
    $id = $attributes->get('id') ?? $name;
    $val = old($name, $value);
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }} @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif @if ($disabled) disabled @endif
        class="block w-full rounded-lg border-slate-300 text-sm
               focus:border-indigo-500 focus:ring-indigo-500
               disabled:bg-slate-50 disabled:text-slate-400">{{ $val }}</textarea>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if ($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
