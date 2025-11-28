{{-- resources/views/components/form/select.blade.php --}}
@props([
    'name',
    'label' => null,
    'options' => [], // ожидаем массив/коллекцию вида [key => label]
    'value' => null, // выбранное значение
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }} @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <select id="{{ $name }}" name="{{ $name }}"
        {{ $attributes->class([
            'block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm',
        ]) }}
        @required($required) @disabled($disabled)>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
