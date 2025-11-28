@props([
    'cancel' => null, // URL "Отмена"
    'submitLabel' => 'Сохранить', // подпись основной кнопки
    'submitVariant' => 'primary', // primary|success|danger|...
    'cancelVariant' => 'ghost', // ghost|link|gray|...
    'align' => 'left', // left|right
])

<div class="mt-6 flex items-center gap-3 {{ $align === 'right' ? 'justify-end' : '' }}">
    @if ($cancel)
        <x-ui.button :href="$cancel" variant="{{ $cancelVariant }}">
            Отмена
        </x-ui.button>
    @endif

    <x-ui.button type="submit" variant="{{ $submitVariant }}">
        {{ $submitLabel }}
    </x-ui.button>
</div>
