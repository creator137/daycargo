@props([
    'name',
    'label' => null,
    'accept' => null, // напр. "image/*"
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

@php($id = $attributes->get('id') ?: 'f_' . \Illuminate\Support\Str::uuid())

<div x-data="{ fileName: '' }">
    @if ($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-slate-700 mb-1">
            {{ $label }} @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <input id="{{ $id }}" type="file" name="{{ $name }}" class="sr-only"
        @change="fileName = $event.target.files?.[0]?.name ?? ''" @required($required) @disabled($disabled)
        @if ($accept) accept="{{ $accept }}" @endif>

    <div class="flex items-center gap-3">
        <label for="{{ $id }}"
            class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-700 cursor-pointer">
            Выбрать файл
        </label>
        <span class="text-sm text-slate-600" x-text="fileName || 'Файл не выбран'"></span>
    </div>

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
