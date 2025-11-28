@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-semibold mb-4">Справочники — причины отмены</h1>

    <x-ui.card>
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">Всего: {{ $reasons->count() }}</span>
                @can('create', \App\Models\CancelReason::class)
                    <x-ui.button :href="route('admin.cancel_reasons.create')" variant="primary" size="sm">
                        Добавить
                    </x-ui.button>
                @endcan
            </div>
        </x-slot:actions>

        @if ($reasons->isEmpty())
            <div class="flex items-center justify-between text-sm text-slate-500">
                <span>Пока пусто.</span>
                @can('create', \App\Models\CancelReason::class)
                    <x-ui.button :href="route('admin.cancel_reasons.create')" variant="primary" size="sm">
                        Создать
                    </x-ui.button>
                @endcan
            </div>
        @else
            <x-ui.table tone="bold" bordered borderThick hover compact :headers="[
                ['label' => 'Сорт', 'align' => 'right', 'width' => '70px'],
                ['label' => 'Код', 'width' => '120px'],
                'Название',
                ['label' => 'Инициатор', 'width' => '130px'],
                ['label' => 'Окно, мин', 'align' => 'right', 'width' => '110px'],
                ['label' => 'Штраф клиент', 'width' => '180px'],
                ['label' => 'Штраф водитель', 'width' => '220px'],
                ['label' => 'Статус', 'width' => '100px'],
                ['label' => 'Действия', 'align' => 'right', 'width' => '280px'],
            ]">
                @foreach ($reasons as $r)
                    <tr>
                        <td class="text-right">{{ $r->sort }}</td>
                        <td>{{ $r->code }}</td>
                        <td>{{ $r->title }}</td>
                        <td>{{ $r->initiator_label }}</td>
                        <td class="text-right">{{ $r->window_minutes }}</td>
                        <td>
                            @php
                                $c = [];
                                if (!is_null($r->client_fee_fixed)) {
                                    $c[] = number_format($r->client_fee_fixed, 2, ',', ' ') . ' ₽';
                                }
                                if (!is_null($r->client_fee_percent)) {
                                    $c[] = $r->client_fee_percent . ' %';
                                }
                            @endphp
                            {{ $c ? implode(' + ', $c) : '—' }}
                        </td>
                        <td>
                            @php
                                $d = [];
                                if (!is_null($r->driver_fee_fixed)) {
                                    $d[] = number_format($r->driver_fee_fixed, 2, ',', ' ') . ' ₽';
                                }
                                if (!is_null($r->driver_fee_percent)) {
                                    $d[] = $r->driver_fee_percent . ' %';
                                }
                                if (!is_null($r->driver_fee_min)) {
                                    $d[] = 'мин ' . number_format($r->driver_fee_min, 2, ',', ' ') . ' ₽';
                                }
                            @endphp
                            {{ $d ? implode(' + ', $d) : '—' }}
                        </td>
                        <td>
                            <x-ui.badge :variant="$r->active ? 'success' : 'muted'">
                                {{ $r->active ? 'Активна' : 'Выключена' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @can('update', $r)
                                    <x-ui.button :href="route('admin.cancel_reasons.edit', $r)" variant="primary" size="sm">
                                        Править
                                    </x-ui.button>
                                @endcan

                                @can('toggle', $r)
                                    <form action="{{ route('admin.cancel_reasons.toggle', $r) }}" method="post">
                                        @csrf @method('PATCH')
                                        <x-ui.button type="submit" :variant="$r->active ? 'ghost' : 'success'" size="sm">
                                            {{ $r->active ? 'Выключить' : 'Включить' }}
                                        </x-ui.button>
                                    </form>
                                @endcan

                                @can('delete', $r)
                                    <form action="{{ route('admin.cancel_reasons.destroy', $r) }}" method="post"
                                        onsubmit="return confirm('Удалить причину?');">
                                        @csrf @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" size="sm">
                                            Удалить
                                        </x-ui.button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif
    </x-ui.card>
@endsection
