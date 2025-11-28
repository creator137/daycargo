{{-- resources/views/components/crud/fields.blade.php --}}
@props([
    'schema' => null, // null|array — если массив, отрисуем поля автоматически
    'model' => null, // optional: для автозаполнения value из модели
    'grid' => 'grid grid-cols-1 sm:grid-cols-2 gap-4',
])

@if (is_array($schema) && count($schema))
    <div class="{{ $grid }}">
        @foreach ($schema as $field)
            @php
                $component = 'form.' . ($field['as'] ?? 'input'); // form.input / form.select / form.textarea / form.toggle ...
                $name = $field['name'] ?? null;
                $value = array_key_exists('value', $field) ? $field['value'] : $model?->getAttribute($name);
                $col = $field['col'] ?? null;

                // пробрасываем все доп. ключи, кроме служебных
                $attrs = collect($field)
                    ->except(['as', 'col'])
                    ->merge(['value' => $value]);
            @endphp

            <div @class([$col => !empty($col)])>
                <x-dynamic-component :component="$component" {{ $attributes->merge($attrs->all()) }} />
            </div>
        @endforeach
    </div>
@else
    {{-- slot-режим: поля пишешь сам в вызывающем шаблоне --}}
    <div class="{{ $grid }}">
        {{ $slot }}
    </div>
@endif
