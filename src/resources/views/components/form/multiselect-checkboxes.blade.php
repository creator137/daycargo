@props([
    'name', // базовое имя поля, БЕЗ [] (напр. cities)
    'label' => null,
    'options' => [], // [value => label]
    'values' => [], // выбранные значения (array|Collection|string|null)
    'columns' => 2, // 1..4
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Collection;

    $uid = (string) Str::uuid();

    // Нормализуем выбранные значения к массиву строк
    $selectedRaw = old($name, $values);
    if ($selectedRaw instanceof Collection) {
        $selectedRaw = $selectedRaw->all();
    }
    if (!is_array($selectedRaw)) {
        $selectedRaw = $selectedRaw !== null ? [$selectedRaw] : [];
    }
    $selected = array_map('strval', $selectedRaw);

    // Безопасный класс грида
    $cols = (int) $columns;
    if ($cols < 1 || $cols > 4) {
        $cols = 2;
    }
    $gridClass = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
    ][$cols];
@endphp

<div x-data x-id="['ms-{{ $uid }}']" data-ms="{{ $uid }}">
    @if ($label)
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-slate-700">
                {{ $label }}
                @if ($required)
                    <span class="text-rose-500">*</span>
                @endif
            </label>
            <div class="flex gap-3 text-xs">
                <button type="button" class="text-indigo-600 hover:underline"
                    onclick="(r=>{r.querySelectorAll('input[type=checkbox][data-ms]').forEach(cb=>{cb.checked=true;cb.dispatchEvent(new Event('change',{bubbles:true}))})})(document.querySelector('[data-ms={{ $uid }}]'))">
                    Выбрать все
                </button>
                <button type="button" class="text-slate-600 hover:underline"
                    onclick="(r=>{r.querySelectorAll('input[type=checkbox][data-ms]').forEach(cb=>{cb.checked=false;cb.dispatchEvent(new Event('change',{bubbles:true}))})})(document.querySelector('[data-ms={{ $uid }}]'))">
                    Очистить
                </button>
            </div>
        </div>
    @endif

    <div class="grid {{ $gridClass }} gap-2 p-3 rounded-md border border-slate-200 bg-slate-50">
        @foreach ($options as $val => $text)
            @php $id = $uid.'-'.md5((string) $val); @endphp
            <label for="{{ $id }}" class="inline-flex items-center gap-2 text-sm">
                <input id="{{ $id }}" data-ms type="checkbox" name="{{ $name }}[]"
                    value="{{ $val }}" @checked(in_array((string) $val, $selected, true)) @disabled($disabled)
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                <span>{{ $text }}</span>
            </label>
        @endforeach
    </div>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    {{-- Общая ошибка для поля-массива --}}
    @error($name)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
