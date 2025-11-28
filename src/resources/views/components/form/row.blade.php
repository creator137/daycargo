@props([
    'label' => null,
    'for' => null,
    'hint' => null,
    'required' => false,
    'error' => null,
])

<div class="grid grid-cols-12 gap-4 items-start py-2">
    <label for="{{ $for }}" class="col-span-12 md:col-span-3 text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="text-rose-500">*</span>
        @endif
    </label>

    <div class="col-span-12 md:col-span-9 space-y-1">
        {{ $slot }}

        @if ($hint)
            <p class="text-xs text-slate-500">{{ $hint }}</p>
        @endif

        @if ($error)
            <p class="text-xs text-rose-600">{{ $error }}</p>
        @endif
    </div>
</div>
