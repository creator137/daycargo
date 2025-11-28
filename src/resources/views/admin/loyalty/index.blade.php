@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">Бонусная система</h1>
        <div class="flex gap-2">
            @if (\Illuminate\Support\Facades\Route::has('admin.promocodes.create'))
                @can('promocodes.create')
                    <a href="{{ route('admin.promocodes.create') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm hover:bg-indigo-500">
                        Создать промокод
                    </a>
                @endcan
            @endif
            @if (\Illuminate\Support\Facades\Route::has('admin.referrals.index'))
                @can('referrals.view')
                    <a href="{{ route('admin.referrals.index') }}"
                        class="inline-flex items-center px-3 py-2 rounded-md border border-slate-200 text-sm hover:bg-slate-50">
                        Все рефералы
                    </a>
                @endcan
            @endif
            @if (\Illuminate\Support\Facades\Route::has('admin.promocodes.index'))
                @can('promocodes.view')
                    <a href="{{ route('admin.promocodes.index') }}""
                        class="inline-flex items-center px-3 py-2 rounded-md border border-slate-200 text-sm hover:bg-slate-50">
                        Все промокоды
                    </a>
                @endcan
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Последние промокоды --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-3">
                <div class="font-medium">Новые промокоды</div>
                @if (\Illuminate\Support\Facades\Route::has('admin.promocodes.index'))
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('admin.promocodes.index') }}">все</a>
                @endif
            </div>

            <x-ui.table :headers="['Код', 'Тип', 'Скидка/Бонус', 'Лимит', 'Период']">
                @forelse($recentCodes as $c)
                    <tr>
                        <td class="whitespace-nowrap font-mono">{{ $c->code }}</td>
                        <td class="whitespace-nowrap">
                            {{ $c->type === 'percent' ? 'Проценты' : ($c->type === 'fixed' ? 'Фикс. скидка' : 'Бонусы') }}
                        </td>
                        <td class="whitespace-nowrap">
                            @if ($c->type === 'percent')
                                {{ rtrim(rtrim(number_format((float) $c->value, 2, ',', ' '), '0'), ',') }}%
                            @else
                                {{ number_format((float) $c->value, 2, ',', ' ') }}
                                {{ $c->type === 'bonus' ? 'балл.' : '₽' }}
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            @if ($c->per_user_limit)
                                до {{ $c->per_user_limit }} / чел.
                            @else
                                —
                            @endif
                        </td>
                        <td class="whitespace-nowrap text-sm text-slate-600">
                            {{ $c->starts_at?->format('d.m.Y') ?? '—' }} — {{ $c->expires_at?->format('d.m.Y') ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"><x-ui.alert tone="muted">Пока нет промокодов.</x-ui.alert></td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        {{-- Последние активации промокодов --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-3">
                <div class="font-medium">Недавние активации</div>
                @if (\Illuminate\Support\Facades\Route::has('admin.promocodes.redemptions'))
                    <a class="text-sm text-indigo-600 hover:underline"
                        href="{{ route('admin.promocodes.redemptions') }}">все</a>
                @endif
            </div>

            <x-ui.table :headers="['Дата', 'Клиент', 'Код', 'Сумма', 'Статус']">
                @forelse($recentRed as $r)
                    <tr>
                        <td class="whitespace-nowrap">{{ $r->created_at->format('d.m.Y H:i') }}</td>
                        <td class="whitespace-nowrap">{{ $r->client?->full_name ?? ($r->client?->phone ?? '—') }}</td>
                        <td class="whitespace-nowrap font-mono">{{ $r->promoCode?->code }}</td>
                        <td class="whitespace-nowrap">
                            {{ number_format((float) $r->applied_amount, 2, ',', ' ') }}
                            @if ($r->promoCode?->type === 'bonus')
                                балл.
                            @else
                                ₽
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $r->status === 'applied' ? 'Зачтён' : ($r->status === 'rejected' ? 'Отклонён' : 'Ожидает') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"><x-ui.alert tone="muted">Активаций ещё нет.</x-ui.alert></td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>

        {{-- Последние рефералы --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-3">
                <div class="font-medium">Последние рефералы</div>
                @if (\Illuminate\Support\Facades\Route::has('admin.referrals.index'))
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('admin.referrals.index') }}">все</a>
                @endif
            </div>

            <x-ui.table :headers="['Дата', 'Пригласил', 'Новый клиент', 'Статус', 'Бонусы']">
                @forelse($recentRefs as $ref)
                    <tr>
                        <td class="whitespace-nowrap">{{ $ref->created_at->format('d.m.Y H:i') }}</td>
                        <td class="whitespace-nowrap">{{ $ref->referrer?->full_name ?? ($ref->referrer?->phone ?? '—') }}
                        </td>
                        <td class="whitespace-nowrap">{{ $ref->referee?->full_name ?? ($ref->referee?->phone ?? '—') }}</td>
                        <td class="whitespace-nowrap">
                            {{ $ref->status === 'approved' ? 'Подтверждён' : ($ref->status === 'pending' ? 'Ожидает' : 'Отклонён') }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ number_format((float) ($ref->reward_points ?? 0), 2, ',', ' ') }} балл.
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"><x-ui.alert tone="muted">Рефералов ещё нет.</x-ui.alert></td>
                    </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
@endsection
