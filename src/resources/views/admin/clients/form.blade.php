@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $client->exists;
        $title = $isEdit ? 'Правка клиента' : 'Создание клиента';
        $action = $isEdit ? route('admin.clients.update', $client) : route('admin.clients.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.clients.index')" :hasFiles="true">
        <x-crud.fields>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Фото</label>
                    @if ($client->photo_url)
                        <img src="{{ $client->photo_url }}" class="w-24 h-24 rounded-full object-cover border mb-2">
                    @endif
                    <x-form.file name="photo" accept="image/*" hint="JPG/PNG/WEBP до 5 МБ" />
                </div>

                <x-form.input name="full_name" label="ФИО" :value="old('full_name', $client->full_name)" />
                <x-form.input name="phone" label="Телефон" required :value="old('phone', $client->phone)" />
            </div>

            <x-form.row cols="3">
                <x-form.input name="email" type="email" label="Email" :value="old('email', $client->email)" />
                <x-form.date name="birth_date" label="Дата рождения" :value="old('birth_date', $client->birth_date)" />
                <x-form.select name="lang" label="Язык" :options="['ru' => 'Русский', 'en' => 'English']" :value="old('lang', $client->lang ?? 'ru')" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.select name="city" label="Город" :options="['' => '—'] + $citiesOptions" :value="old('city', $client->city)" />
                <x-form.select name="client_type" label="Тип клиента" :options="['person' => 'Физ. лицо', 'company' => 'Юр. лицо']" :value="old('client_type', $client->client_type ?? 'person')" />
                <x-form.toggle name="is_agent" label="Агент" :checked="old('is_agent', (bool) $client->is_agent)" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="passport_series" label="Паспорт (серия)" :value="old('passport_series', $client->passport_series)" />
                <x-form.input name="passport_number" label="Паспорт (номер)" :value="old('passport_number', $client->passport_number)" />
            </x-form.row>

            <x-form.textarea name="comment" rows="3" label="Комментарий">
                {{ old('comment', $client->comment) }}
            </x-form.textarea>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form.toggle name="send_trip_report" label="Отправлять отчёт о поездке" :checked="old('send_trip_report', (bool) $client->send_trip_report)" />
                <x-form.toggle name="news_notifications" label="Уведомления о новостях" :checked="old('news_notifications', (bool) $client->news_notifications)" />
                <x-form.toggle name="allow_push" label="PUSH-рассылки" :checked="old('allow_push', (bool) $client->allow_push)" />
            </div>

            <x-form.row cols="3">
                <x-form.input name="credit_limit" type="number" step="0.01" min="0" label="Кредитный лимит"
                    :value="old('credit_limit', $client->credit_limit ?? 0)" />
                <x-form.input name="balance" type="number" step="0.01" label="Баланс (инфо)" :value="old('balance', $client->balance ?? 0)" />
                <x-form.toggle name="blacklisted" label="Чёрный список" :checked="old('blacklisted', (bool) $client->blacklisted)" />
            </x-form.row>

            <x-ui.alert tone="muted">
                Вкладки «Заказы, Бонусы, Отзывы, Баланс, Корп. обслуживание, Промокоды, Блокировки, Агент, Адреса, Долг,
                Избранное»
                появятся по мере реализации соответствующих модулей.
            </x-ui.alert>

        </x-crud.fields>

        @if ($isEdit)
            @can('client_economy.view')
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Денежный баланс --}}
                    <x-ui.card>
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-medium">Денежный баланс</div>
                            <div class="text-sm text-slate-600">
                                Текущий: <span class="font-medium">{{ number_format((float) $client->balance, 2, ',', ' ') }}
                                    ₽</span>
                            </div>
                        </div>

                        <form method="post" action="{{ route('admin.clients.wallet', $client) }}"
                            class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @csrf
                            <input type="hidden" name="wallet" value="money">
                            <x-form.select name="operation" label="Операция" :options="['topup' => 'Пополнение', 'debit' => 'Списание']" />
                            <x-form.input name="amount" type="number" step="0.01" label="Сумма, ₽" required />
                            <x-form.input name="comment" label="Комментарий" />
                            <div class="md:col-span-3">
                                @canany(['client_economy.topup', 'client_economy.debit'])
                                    <x-ui.button type="submit" variant="primary" size="sm">Выполнить</x-ui.button>
                                @else
                                    <x-ui.alert tone="muted">Нет прав на операции с балансом.</x-ui.alert>
                                @endcanany
                            </div>
                        </form>

                        <div class="mt-4">
                            <div class="text-sm font-medium mb-2">Последние операции</div>
                            @php
                                $moneyTx = $client->walletTransactions()->where('wallet', 'money')->limit(10)->get();
                            @endphp
                            <x-ui.table :headers="['Дата', 'Операция', 'Сумма', 'Комментарий', 'Менеджер']">
                                @forelse($moneyTx as $tx)
                                    <tr>
                                        <td class="whitespace-nowrap">{{ $tx->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="whitespace-nowrap">
                                            {{ $tx->operation === 'topup' ? 'Пополнение' : 'Списание' }}</td>
                                        <td class="whitespace-nowrap">{{ number_format((float) $tx->amount, 2, ',', ' ') }}
                                        </td>
                                        <td>{{ $tx->comment }}</td>
                                        <td class="whitespace-nowrap">{{ $tx->performer?->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><x-ui.alert tone="muted">Пока нет операций.</x-ui.alert></td>
                                    </tr>
                                @endforelse
                            </x-ui.table>
                        </div>
                    </x-ui.card>

                    {{-- Бонусный баланс --}}
                    <x-ui.card>
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-medium">Бонусный баланс</div>
                            <div class="text-sm text-slate-600">
                                Текущий: <span
                                    class="font-medium">{{ number_format((float) $client->bonus_balance, 2, ',', ' ') }}
                                    баллов</span>
                            </div>
                        </div>

                        <form method="post" action="{{ route('admin.clients.wallet', $client) }}"
                            class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @csrf
                            <input type="hidden" name="wallet" value="bonus">
                            <x-form.select name="operation" label="Операция" :options="['topup' => 'Начислить', 'debit' => 'Списать']" />
                            <x-form.input name="amount" type="number" step="0.01" label="Баллы" required />
                            <x-form.input name="comment" label="Комментарий" />
                            <div class="md:col-span-3">
                                @canany(['client_economy.topup', 'client_economy.debit'])
                                    <x-ui.button type="submit" variant="primary" size="sm">Выполнить</x-ui.button>
                                @else
                                    <x-ui.alert tone="muted">Нет прав на операции с бонусами.</x-ui.alert>
                                @endcanany
                            </div>
                        </form>

                        <div class="mt-4">
                            <div class="text-sm font-medium mb-2">История бонусов</div>
                            @php
                                $bonusEntries = $client->bonusEntries()->limit(10)->get();
                            @endphp
                            <x-ui.table :headers="['Дата', 'Тип', 'Баллы', 'Источник', 'Комментарий']">
                                @forelse($bonusEntries as $b)
                                    <tr>
                                        <td class="whitespace-nowrap">{{ $b->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="whitespace-nowrap">
                                            {{ $b->type === 'earn' ? 'Начисление' : ($b->type === 'spend' ? 'Списание' : 'Сгорание') }}
                                        </td>
                                        <td class="whitespace-nowrap">{{ number_format((float) $b->points, 2, ',', ' ') }}
                                        </td>
                                        <td class="whitespace-nowrap">{{ $b->source ?? '—' }}</td>
                                        <td>{{ $b->comment }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"><x-ui.alert tone="muted">История пустая.</x-ui.alert></td>
                                    </tr>
                                @endforelse
                            </x-ui.table>
                        </div>
                    </x-ui.card>
                </div>
            @endcan
        @endif
        <x-form.actions :cancel="route('admin.clients.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
