@props(['name', 'label' => null, 'checked' => false, 'help' => null])

@php
    $id = $attributes->get('id') ?? $name;
    $isChecked = (bool) old($name, $checked);
@endphp

<div {{ $attributes->merge(['class' => '']) }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <label for="{{ $id }}" class="inline-flex items-center gap-3 cursor-pointer select-none">
        <span class="text-sm font-medium text-slate-700">{{ $label }}</span>

        <input id="{{ $id }}" name="{{ $name }}" type="checkbox" value="1"
            @checked($isChecked) class="sr-only peer" />

        <div
            class="w-10 h-6 rounded-full bg-slate-300 transition
                    peer-checked:bg-indigo-600 relative">
            <div
                class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow
                        transition peer-checked:translate-x-4">
            </div>
        </div>
    </label>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if ($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
</div>
