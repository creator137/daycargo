@props([
    'name',
    'label' => null,
    'options' => [], // ['id' => 'Название'] | ['Москва','СПб'] | Collection
    'value' => null, // scalar | array
    'multiple' => false, // можно true ИЛИ оставить [] в name
    'required' => false,
    'disabled' => false,
    'placeholder' => null, // только для одиночного select
    'hint' => null,
])

@php
    // Привести options к массиву
    $opts = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : (array) $options;

    // Если options вида ['Москва','СПб'] (числовые ключи) — сделаем value==label
    $allNumericKeys = !empty($opts) && array_keys($opts) === range(0, count($opts) - 1);
    if ($allNumericKeys) {
        $opts = collect($opts)->mapWithKeys(fn($text) => [(string) $text => (string) $text])->all();
    }

    // Определить multiple
    $isMultiple = $multiple || str_ends_with($name, '[]');

    // Ключ ошибок без [] в конце
    $errorKey = $isMultiple ? rtrim($name, '[]') : $name;

    // Нормализовать выбранные значения
    if ($isMultiple) {
        $selectedValues = collect($value ?? [])
            ->map(fn($v) => (string) $v)
            ->all();
    } else {
        $selectedValue = isset($value) ? (string) $value : null;
    }

    // id без квадратных скобок, чтобы HTML id был валидным
    $autoId = str_replace(['[', ']'], '_', $name);

    $selectClasses =
        'block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
@endphp

<div>
    @if ($label)
        <label for="{{ $autoId }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }} @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <select id="{{ $autoId }}" name="{{ $name }}" {{ $attributes->class([$selectClasses]) }}
        @if ($isMultiple) multiple @endif @required($required) @disabled($disabled)>
        @if (!$isMultiple && $placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($opts as $k => $text)
            @php
                $val = (string) $k;
                $selected = $isMultiple
                    ? in_array($val, $selectedValues ?? [], true)
                    : $selectedValue !== null && $val === $selectedValue;
            @endphp
            <option value="{{ $val }}" @selected($selected)>{{ $text }}</option>
        @endforeach
    </select>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @error($errorKey)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
