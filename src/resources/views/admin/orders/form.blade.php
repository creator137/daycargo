@extends('layouts.admin')

@section('content')
    @php
        /** @var \App\Models\Order $order */
        $isEdit = $order->exists;
        $title = $isEdit ? 'Правка заказа' : 'Создание заказа';
        $action = $isEdit ? route('admin.orders.update', $order) : route('admin.orders.store');
        $method = $isEdit ? 'PUT' : 'POST';

        $activeTab = 'main';

        $tabs = [
            ['key' => 'main', 'label' => 'Основное'],
            ['key' => 'route', 'label' => 'Маршрут'],
            ['key' => 'calc', 'label' => 'Расчёт'],
            ['key' => 'pay', 'label' => 'Оплата'],
            ['key' => 'opts', 'label' => 'Опции'],
            ['key' => 'meta', 'label' => 'Дополнительно'],
        ];
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.orders.index')">
        <x-crud.fields>
            <x-tabs.root :active="$activeTab">
                <x-tabs.nav :tabs="$tabs" :active="$activeTab" />

                {{-- ОСНОВНОЕ --}}
                <x-tabs.panel name="main" :active="$activeTab">
                    <x-form.row cols="4">
                        <x-form.select name="status" label="Статус" required :options="[
                            'new' => 'Новый',
                            'assigning' => 'Назначение',
                            'accepted' => 'Принят',
                            'arrived' => 'Подача',
                            'loading' => 'Погрузка',
                            'driving' => 'В пути',
                            'waiting' => 'Ожидание',
                            'completed' => 'Завершён',
                            'cancelled' => 'Отменён',
                            'failed' => 'Неуспех',
                            'refund' => 'Возврат',
                        ]" :value="old('status', $order->status ?? 'new')" />

                        <x-form.select name="type" label="Тип" required :options="[
                            'now' => 'Срочно',
                            'schedule' => 'По времени',
                            'courier' => 'Курьер',
                            'cargo' => 'Грузовой',
                            'move' => 'Переезд',
                            'intercity' => 'Межгород',
                        ]" :value="old('type', $order->type ?? 'now')" />

                        <x-form.select name="city_id" label="Город" :options="['' => '—'] + $cityOptions" :value="old('city_id', $order->city_id)" />
                        <x-form.input name="city" label="Название города (текст)" required :value="old('city', $order->city)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <x-form.select name="client_id" label="Клиент" :options="['' => '—'] + $clientOptions" :value="old('client_id', $order->client_id)" />
                        <x-form.select name="organization_id" label="Организация (опц.)" :options="['' => '—'] + $organizationOptions"
                            :value="old('organization_id', $order->organization_id)" />
                        <x-form.select name="payer_type" label="Плательщик" required :options="[
                            'client' => 'Клиент',
                            'organization' => 'Организация',
                            'cashless' => 'Безнал',
                            'other' => 'Другое',
                        ]"
                            :value="old('payer_type', $order->payer_type ?? 'client')" />
                        <x-form.input name="priority" type="number" min="0" max="10" label="Приоритет"
                            :value="old('priority', $order->priority ?? 0)" />
                    </x-form.row>

                    <x-form.row cols="3">
                        <x-form.input name="contact_name" label="Контактное лицо" :value="old('contact_name', $order->contact_name)" />
                        <x-form.input name="contact_phone" label="Телефон" :value="old('contact_phone', $order->contact_phone)" />
                        <x-form.toggle name="blacklist_check" label="Проверять ЧС" :checked="old('blacklist_check', (bool) $order->blacklist_check)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <x-form.select name="tariff_id" label="Тариф" :options="['' => '—'] + $tariffOptions" :value="old('tariff_id', $order->tariff_id)" />
                        <x-form.select name="vehicle_type_id" label="Тип авто" :options="['' => '—'] + $vehicleTypeOptions" :value="old('vehicle_type_id', $order->vehicle_type_id)" />
                        <x-form.select name="driver_group_id" label="Группа водителей" :options="['' => '—'] + $driverGroupOptions"
                            :value="old('driver_group_id', $order->driver_group_id)" />
                        <x-form.select name="assign_strategy" label="Назначение" :options="[
                            'manual' => 'Ручное',
                            'broadcast' => 'Рассылка',
                            'nearest' => 'Ближайший',
                            'group' => 'Группа',
                        ]" :value="old('assign_strategy', $order->assign_strategy ?? 'manual')" />
                    </x-form.row>

                    <x-form.row cols="3">
                        <x-form.select name="driver_id" label="Водитель" :options="['' => '—'] + $driverOptions" :value="old('driver_id', $order->driver_id)" />
                        <x-form.select name="vehicle_id" label="Авто" :options="['' => '—'] + $vehicleOptions" :value="old('vehicle_id', $order->vehicle_id)" />
                        <x-form.input name="broadcast_radius_km" type="number" step="0.1" min="0"
                            label="Радиус рассылки, км" :value="old('broadcast_radius_km', $order->broadcast_radius_km)" />
                    </x-form.row>
                </x-tabs.panel>

                {{-- МАРШРУТ --}}
                <x-tabs.panel name="route" :active="$activeTab">
                    <x-form.row cols="2">
                        <x-form.input name="from_address" label="Откуда" required :value="old('from_address', $order->from_address)" />
                        <x-form.input name="to_address" label="Куда" :value="old('to_address', $order->to_address)" />
                    </x-form.row>

                    <x-form.row cols="2">
                        <x-form.input name="arrival_window_from" type="datetime-local" label="Окно подачи (с)"
                            :value="old(
                                'arrival_window_from',
                                optional($order->arrival_window_from)->format('Y-m-d\TH:i'),
                            )" />
                        <x-form.input name="arrival_window_to" type="datetime-local" label="Окно подачи (до)"
                            :value="old(
                                'arrival_window_to',
                                optional($order->arrival_window_to)->format('Y-m-d\TH:i'),
                            )" />
                    </x-form.row>

                    <x-form.row cols="2">
                        <x-form.textarea name="from_comment" label="Комментарий к подаче"
                            rows="2">{{ old('from_comment', $order->from_comment) }}</x-form.textarea>
                        <x-form.textarea name="to_comment" label="Комментарий к месту доставки"
                            rows="2">{{ old('to_comment', $order->to_comment) }}</x-form.textarea>
                    </x-form.row>

                    <x-form.textarea name="via_points[]" label="Промежуточные точки (по одной в строке)"
                        rows="3">{{ old('via_points.0') }}</x-form.textarea>

                    <x-ui.card class="mt-4">
                        <div class="text-sm font-medium mb-2">Карта маршрута (по адресам)</div>
                        @php
                            $query = urlencode(
                                ($order->from_address ?? '') .
                                    ' ' .
                                    ($order->to_address ? ' ' . $order->to_address : ''),
                            );
                        @endphp
                        <iframe src="https://yandex.ru/map-widget/v1/?text={{ $query }}"
                            style="width: 100%; height: 320px; border:0;" loading="lazy"></iframe>
                        <x-ui.alert tone="muted" class="mt-2">
                            Для продакшен-геокодинга подключим API и будем строить точный маршрут.
                        </x-ui.alert>
                    </x-ui.card>
                </x-tabs.panel>

                {{-- РАСЧЁТ --}}
                <x-tabs.panel name="calc" :active="$activeTab">
                    <x-form.select name="calc_schema" label="Схема расчёта" :options="['by_tariff' => 'По тарифу', 'fixed' => 'Фикс', 'manual' => 'Вручную']" :value="old('calc_schema', $order->calc_schema ?? 'by_tariff')" />

                    <x-form.row cols="4" class="mt-3">
                        <x-form.input name="price_base" type="number" step="0.01" label="База" :value="old('price_base', $order->price_base)" />
                        <x-form.input name="price_surge" type="number" step="0.01" label="Сург" :value="old('price_surge', $order->price_surge)" />
                        <x-form.input name="price_options" type="number" step="0.01" label="Опции"
                            :value="old('price_options', $order->price_options)" />
                        <x-form.input name="price_waiting" type="number" step="0.01" label="Ожидание"
                            :value="old('price_waiting', $order->price_waiting)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <x-form.input name="price_loading" type="number" step="0.01" label="Погрузка"
                            :value="old('price_loading', $order->price_loading)" />
                        <x-form.input name="price_other" type="number" step="0.01" label="Другое"
                            :value="old('price_other', $order->price_other)" />
                        <x-form.input name="price_discount" type="number" step="0.01" label="Скидка"
                            :value="old('price_discount', $order->price_discount)" />
                        <x-form.input name="promo_discount" type="number" step="0.01" label="Промо"
                            :value="old('promo_discount', $order->promo_discount)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <x-form.input name="bonus_spent" type="number" step="0.01" label="Бонусы списано"
                            :value="old('bonus_spent', $order->bonus_spent)" />
                        <x-form.input name="price_total" type="number" step="0.01" label="Итого"
                            :value="old('price_total', $order->price_total)" />
                        <x-form.input name="distance_km_est" type="number" step="0.1" label="Дистанция, км"
                            :value="old('distance_km_est', $order->distance_km_est)" />
                        <x-form.input name="duration_min_est" type="number" step="1" label="Длительность, мин"
                            :value="old('duration_min_est', $order->duration_min_est)" />
                    </x-form.row>
                </x-tabs.panel>

                {{-- ОПЛАТА --}}
                <x-tabs.panel name="pay" :active="$activeTab">
                    <x-form.row cols="4">
                        <x-form.select name="payment_method" label="Способ оплаты" required :options="[
                            'cash' => 'Наличные',
                            'card' => 'Карта',
                            'cashless' => 'Безнал',
                            'client_balance' => 'Баланс клиента',
                            'org_balance' => 'Баланс орг.',
                        ]"
                            :value="old('payment_method', $order->payment_method ?? 'cash')" />
                        <x-form.input name="prepaid_amount" type="number" step="0.01" label="Предоплата"
                            :value="old('prepaid_amount', $order->prepaid_amount)" />
                        <x-form.input name="paid_amount" type="number" step="0.01" label="Оплачено"
                            :value="old('paid_amount', $order->paid_amount)" />
                        <x-form.input name="debt_amount" type="number" step="0.01" label="Долг"
                            :value="old('debt_amount', $order->debt_amount)" />
                    </x-form.row>
                </x-tabs.panel>

                {{-- ОПЦИИ --}}
                <x-tabs.panel name="opts" :active="$activeTab">
                    <x-form.row cols="5">
                        <x-form.toggle name="need_terminal" label="Терминал" :checked="old('need_terminal', (bool) $order->need_terminal)" />
                        <x-form.toggle name="need_docs" label="Документы" :checked="old('need_docs', (bool) $order->need_docs)" />
                        <x-form.toggle name="fragile" label="Хрупкое" :checked="old('fragile', (bool) $order->fragile)" />
                        <x-form.toggle name="lift_required" label="Лифт обязателен" :checked="old('lift_required', (bool) $order->lift_required)" />
                        <x-form.input name="helper_count" type="number" min="0" max="6" label="Грузчики"
                            :value="old('helper_count', $order->helper_count)" />
                    </x-form.row>

                    <x-form.row cols="3" class="mt-2">
                        <x-form.toggle name="options[child_seat]" label="Детское кресло" :checked="old('options.child_seat', data_get($order->options, 'child_seat'))" />
                        <x-form.toggle name="options[wagon]" label="Универсал" :checked="old('options.wagon', data_get($order->options, 'wagon'))" />
                        <x-form.toggle name="options[refrigerator]" label="Холодильник" :checked="old('options.refrigerator', data_get($order->options, 'refrigerator'))" />
                    </x-form.row>
                </x-tabs.panel>

                {{-- ДОПОЛНИТЕЛЬНО --}}
                <x-tabs.panel name="meta" :active="$activeTab">
                    <x-form.textarea name="comment" rows="4" label="Комментарий">
                        {{ old('comment', $order->comment) }}
                    </x-form.textarea>
                </x-tabs.panel>
            </x-tabs.root>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.orders.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
    <x-tabs.script />
@endsection
