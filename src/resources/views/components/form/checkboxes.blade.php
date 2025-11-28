@props([
    'name',
    'label' => null,
    'options' => [], // ['id' => 'Название'] | ['Москва','СПб'] | Collection
    'value' => [], // массив выбранных значений
    'columns' => 2, // кол-во колонок в сетке
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

@php
    $opts = $options instanceof \Illuminate\Support\Collection ? $options->toArray() : (array) $options;

    $allNumericKeys = !empty($opts) && array_keys($opts) === range(0, count($opts) - 1);
    if ($allNumericKeys) {
        $opts = collect($opts)->mapWithKeys(fn($text) => [(string) $text => (string) $text])->all();
    }

    $selected = collect($value ?? [])
        ->map(fn($v) => (string) $v)
        ->all();

    $errorKey = str_ends_with($name, '[]') ? rtrim($name, '[]') : $name;

    $baseId = str_replace(['[', ']'], '_', $name);

    $cols = max(1, (int) $columns);
@endphp

<div x-data="{
    selectAll() {
            this.$refs.wrap.querySelectorAll('input[type=checkbox]:not(:disabled)').forEach(cb => {
                cb.checked = true;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        },
        clearAll() {
            this.$refs.wrap.querySelectorAll('input[type=checkbox]:not(:disabled)').forEach(cb => {
                cb.checked = false;
                cb.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }
}">
    @if ($label)
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-slate-700 mb-1">
                {{ $label }} @if ($required)
                    <span class="text-rose-500">*</span>
                @endif
            </label>
            <div class="flex items-center gap-2 text-xs">
                <button type="button" class="text-indigo-600 hover:underline" @click="selectAll()">Выбрать все</button>
                <span class="text-slate-300">•</span>
                <button type="button" class="text-slate-600 hover:underline" @click="clearAll()">Снять все</button>
            </div>
        </div>
    @endif

    <div x-ref="wrap" class="grid gap-2 grid-cols-1 sm:grid-cols-{{ $cols }}">
        @foreach ($opts as $val => $text)
            @php
                $valStr = (string) $val;
                $id = $baseId . '_' . preg_replace('/[^a-zA-Z0-9_\-]/', '-', $valStr);
            @endphp
            <label for="{{ $id }}"
                class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 bg-white hover:bg-slate-50">
                <input id="{{ $id }}" type="checkbox" name="{{ $name }}" value="{{ $valStr }}"
                    @checked(in_array($valStr, $selected, true)) @disabled($disabled)
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                <span class="text-sm text-slate-800">{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @error($errorKey)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
