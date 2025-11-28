@extends('layouts.admin')

@section('content')
    <x-page.header>
        <x-slot name="title">Промокоды</x-slot>
        <x-slot name="actions">
            <x-ui.button :href="route('admin.loyalty.promocodes.create')" variant="primary" size="sm">Создать</x-ui.button>
        </x-slot>
    </x-page.header>

    <x-ui.card>
        <form class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
            <x-form.input name="s" :value="request('s')" placeholder="Поиск по коду/комменту" />
            <x-form.select name="type" :options="[
                '' => 'Все',
                'bonus_fixed' => 'Фикс.бонус',
                'bonus_percent' => '% бонус',
                'free_delivery' => 'Бесплатная подача',
            ]" :value="request('type')" />
            <x-form.select name="active" :options="['' => 'Все', '1' => 'Активные', '0' => 'Выключенные']" :value="request('active')" />
            <x-ui.button type="submit" size="sm">Фильтр</x-ui.button>
        </form>

        <x-ui.table :headers="['Код', 'Тип', 'Значение', 'Период', 'Лимиты', 'Статус', 'Действия']">
            @forelse($items as $pc)
                <tr>
                    <td class="font-mono">{{ $pc->code }}</td>
                    <td class="whitespace-nowrap">{{ $pc->type }}</td>
                    <td class="whitespace-nowrap">{{ $pc->value ?? '—' }}</td>
                    <td class="whitespace-nowrap">
                        {{ $pc->starts_at?->format('d.m.Y') ?? '—' }} — {{ $pc->ends_at?->format('d.m.Y') ?? '—' }}
                    </td>
                    <td class="whitespace-nowrap">
                        общ: {{ $pc->usage_limit ?? '∞' }}, на клиента: {{ $pc->per_client_limit ?? '∞' }}
                    </td>
                    <td class="whitespace-nowrap">{{ $pc->active ? 'Активен' : 'Выключен' }}</td>
                    <td class="whitespace-nowrap flex gap-2">
                        <x-ui.button :href="route('admin.loyalty.promocodes.edit', $pc)" size="xs" variant="secondary">Править</x-ui.button>
                        <form method="post" action="{{ route('admin.loyalty.promocodes.toggle', $pc) }}">
                            @csrf @method('PATCH')
                            <x-ui.button type="submit"
                                size="xs">{{ $pc->active ? 'Отключить' : 'Включить' }}</x-ui.button>
                        </form>
                        <form method="post" action="{{ route('admin.loyalty.promocodes.destroy', $pc) }}"
                            onsubmit="return confirm('Удалить промокод?')">
                            @csrf @method('DELETE')
                            <x-ui.button type="submit" size="xs" variant="danger">Удалить</x-ui.button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7"><x-ui.alert tone="muted">Пока ничего.</x-ui.alert></td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-3">{{ $items->links() }}</div>
    </x-ui.card>
@endsection
