@extends('layouts.admin')

@section('content')
    @php
        /** @var \App\Models\Order $order */
        $isEdit = $order->exists;
        $title = $isEdit ? 'Правка заказа' : 'Создание заказа';
        $action = $isEdit ? route('admin.orders.update', $order) : route('admin.orders.store');
        $method = $isEdit ? 'PUT' : 'POST';

        $tabKeys = ['main', 'route', 'calc', 'pay', 'opts', 'meta'];
        $activeTab = request('tab');
        if (!in_array($activeTab, $tabKeys, true)) {
            $activeTab = 'main';
        }

        $tabs = [
            ['key' => 'main', 'label' => 'Основное'],
            ['key' => 'route', 'label' => 'Маршрут'],
            ['key' => 'calc', 'label' => 'Расчёт'],
            ['key' => 'pay', 'label' => 'Оплата'],
            ['key' => 'opts', 'label' => 'Опции'],
            ['key' => 'meta', 'label' => 'Дополнительно'],
        ];

        $viaPoints = old('via_points');
        if ($viaPoints === null) {
            $viaPoints = $order->via_points ?? [];
        }

        $viaPoints = collect((array) $viaPoints)
            ->map(function ($point) {
                if (is_array($point)) {
                    return trim((string) ($point['address'] ?? ($point['text'] ?? '')));
                }
                return trim((string) $point);
            })
            ->filter(fn($point) => $point !== '')
            ->values()
            ->all();

        if (empty($viaPoints)) {
            $viaPoints = [''];
        }

        $selectedTariffId = old('tariff_id', $order->tariff_id);
        $onlyClientTariffsUi = (bool) old('only_client_tariffs_ui', false);
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.orders.index')">
        <x-crud.fields>
            <x-tabs.root :active="$activeTab">
                <x-tabs.nav :tabs="$tabs" :active="$activeTab" />

                <x-tabs.panel name="main" :active="$activeTab">
                    <x-form.row cols="5">
                        <x-form.select name="status" label="Статус" required data-step-required="1" data-step-label="Статус"
                            :options="[
                                'new' => 'Новый',
                                'search' => 'Поиск',
                                'assigned' => 'Назначен',
                                'en_route' => 'К подаче',
                                'loading' => 'Погрузка',
                                'in_progress' => 'Выполняется',
                                'waiting' => 'Ожидание',
                                'paused' => 'Пауза',
                                'completed' => 'Завершен',
                                'canceled' => 'Отменен',
                                'failed' => 'Срыв',
                            ]" :value="old('status', $order->status ?? 'new')" />

                        <x-form.select name="type" label="Срочность" required data-step-required="1"
                            data-step-label="Срочность" :options="[
                                'now' => 'Срочно',
                                'preorder' => 'По времени',
                                'offer' => 'Офер',
                            ]" :value="old('type', $order->type ?? 'now')" />

                        <x-form.select name="service_category" label="Категория услуги/груза" :options="[
                            '' => '—',
                            'courier' => 'Курьер',
                            'cargo' => 'Грузовой',
                            'move' => 'Переезд',
                            'intercity' => 'Межгород',
                            'other' => 'Другое',
                        ]"
                            :value="old('service_category', $order->service_category)" />

                        <x-form.select name="city_id" label="Город" :options="['' => '—'] + $cityOptions" :value="old('city_id', $order->city_id)" />
                        <x-form.input name="city" label="Название города (текст)" required data-step-required="1"
                            data-step-label="Название города" :value="old('city', $order->city)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <x-form.select name="client_id" label="Клиент" required data-step-required="1"
                            data-step-label="Клиент" :options="['' => '—'] + $clientOptions" :value="old('client_id', $order->client_id)" />
                        <x-form.select name="organization_id" label="Организация (опц.)" :options="['' => '—'] + $organizationOptions"
                            :value="old('organization_id', $order->organization_id)" />
                        <x-form.select name="payer_type" label="Плательщик" required data-step-required="1"
                            data-step-label="Плательщик" :options="[
                                'client' => 'Клиент',
                                'organization' => 'Организация',
                            ]" :value="old('payer_type', $order->payer_type ?? 'client')" />
                        <x-form.input name="priority" type="number" min="0" max="10" label="Приоритет"
                            :value="old('priority', $order->priority ?? 0)" />
                    </x-form.row>

                    <x-form.row cols="3">
                        <x-form.input name="contact_name" label="Контактное лицо" :value="old('contact_name', $order->contact_name)" />
                        <x-form.input name="contact_phone" label="Телефон" :value="old('contact_phone', $order->contact_phone)" />
                        <x-form.toggle name="blacklist_check" label="Проверять ЧС" :checked="old('blacklist_check', (bool) $order->blacklist_check)" />
                    </x-form.row>

                    <x-form.row cols="4">
                        <div>
                            <label for="tariff_id" class="block text-sm font-medium text-slate-700 mb-1">Тариф</label>
                            <select id="tariff_id" name="tariff_id"
                                class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">—</option>
                                @foreach ($tariffOptions as $tariff)
                                    <option value="{{ $tariff['id'] }}" data-scope-type="{{ $tariff['scope_type'] }}"
                                        data-scope-id="{{ $tariff['scope_id'] ?? '' }}" @selected((string) $selectedTariffId === (string) $tariff['id'])>
                                        {{ $tariff['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tariff_id')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                            <p id="tariff-client-hint" class="mt-1 text-xs text-slate-500">
                                Сначала показываются тарифы клиента, затем глобальные.
                            </p>
                        </div>

                        <x-form.select name="vehicle_type_id" label="Тип авто" :options="['' => '—'] + $vehicleTypeOptions" :value="old('vehicle_type_id', $order->vehicle_type_id)" />
                        <x-form.select name="driver_group_id" label="Группа водителей" :options="['' => '—'] + $driverGroupOptions"
                            :value="old('driver_group_id', $order->driver_group_id)" />
                        <x-form.toggle id="only_client_tariffs_ui" name="only_client_tariffs_ui"
                            label="Только тарифы клиента" :checked="$onlyClientTariffsUi" />
                    </x-form.row>

                    <x-form.row cols="3">
                        <x-form.select name="driver_id" label="Водитель" :options="['' => '—'] + $driverOptions" :value="old('driver_id', $order->driver_id)" />
                        <x-form.select name="vehicle_id" label="Авто" :options="['' => '—'] + $vehicleOptions" :value="old('vehicle_id', $order->vehicle_id)" />
                        <x-form.input name="broadcast_radius_km" type="number" step="0.1" min="0"
                            label="Радиус рассылки, км" :value="old('broadcast_radius_km', $order->broadcast_radius_km)" />
                    </x-form.row>

                    <x-form.row cols="1">
                        <x-form.select name="assign_strategy" label="Назначение" :options="[
                            'manual' => 'Ручное',
                            'auto_broadcast' => 'Авто-рассылка',
                            'direct_offer' => 'Прямой офер',
                        ]" :value="old('assign_strategy', $order->assign_strategy ?? 'manual')" />
                    </x-form.row>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <div></div>
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-next data-current-tab="main"
                            data-next-tab="route">Далее</x-ui.button>
                    </div>
                </x-tabs.panel>

                <x-tabs.panel name="route" :active="$activeTab">
                    <x-form.row cols="2">
                        <x-form.input name="from_address" label="Откуда" required data-step-required="1"
                            data-step-label="Адрес подачи" :value="old('from_address', $order->from_address)" />
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

                    <x-ui.card class="mt-3">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium">Промежуточные точки</div>
                            <x-ui.button id="via-add" type="button" variant="gray" size="sm">+ Добавить
                                точку</x-ui.button>
                        </div>

                        <div id="via-list" class="space-y-2">
                            @foreach ($viaPoints as $point)
                                <div class="via-row grid grid-cols-1 md:grid-cols-[1fr_auto] gap-2">
                                    <input type="text" name="via_points[]" value="{{ $point }}"
                                        class="block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Промежуточный адрес">
                                    <div class="flex items-center gap-1">
                                        <button type="button"
                                            class="via-up px-2 py-1 text-xs rounded border border-slate-300 hover:bg-slate-50">↑</button>
                                        <button type="button"
                                            class="via-down px-2 py-1 text-xs rounded border border-slate-300 hover:bg-slate-50">↓</button>
                                        <button type="button"
                                            class="via-remove px-2 py-1 text-xs rounded border border-rose-300 text-rose-700 hover:bg-rose-50">Удалить</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <template id="via-template">
                            <div class="via-row grid grid-cols-1 md:grid-cols-[1fr_auto] gap-2">
                                <input type="text" name="via_points[]"
                                    class="block w-full rounded-md border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Промежуточный адрес">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        class="via-up px-2 py-1 text-xs rounded border border-slate-300 hover:bg-slate-50">↑</button>
                                    <button type="button"
                                        class="via-down px-2 py-1 text-xs rounded border border-slate-300 hover:bg-slate-50">↓</button>
                                    <button type="button"
                                        class="via-remove px-2 py-1 text-xs rounded border border-rose-300 text-rose-700 hover:bg-rose-50">Удалить</button>
                                </div>
                            </div>
                        </template>

                        @error('via_points')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        @error('via_points.*')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror

                    </x-ui.card>

                    <x-ui.card class="mt-4">
                        <div class="text-sm font-medium mb-2">Карта маршрута (по адресам)</div>
                        @php
                            $query = urlencode(
                                old('from_address', $order->from_address ?? '') .
                                    ' ' .
                                    (old('to_address', $order->to_address ?? '')
                                        ? ' ' . old('to_address', $order->to_address ?? '')
                                        : ''),
                            );
                        @endphp
                        <iframe src="https://yandex.ru/map-widget/v1/?text={{ $query }}"
                            style="width: 100%; height: 320px; border:0;" loading="lazy"></iframe>
                    </x-ui.card>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-prev
                            data-current-tab="route" data-prev-tab="main">Назад</x-ui.button>
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-next
                            data-current-tab="route" data-next-tab="calc">Далее</x-ui.button>
                    </div>
                </x-tabs.panel>

                <x-tabs.panel name="calc" :active="$activeTab">
                    <x-form.select name="calc_schema" label="Схема расчёта" :options="[
                        'by_tariff' => 'По тарифу',
                        'fixed_price' => 'Фиксированная цена',
                        'hourly' => 'Почасовая',
                        'per_km' => 'За км',
                        'mixed' => 'Смешанная',
                    ]" :value="old('calc_schema', $order->calc_schema ?? 'by_tariff')" />

                    <x-form.row cols="4" class="mt-3">
                        <x-form.input name="price_base" type="number" step="0.01" label="База"
                            :value="old('price_base', $order->price_base)" />
                        <x-form.input name="price_surge" type="number" step="0.01" label="Сург"
                            :value="old('price_surge', $order->price_surge)" />
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

                    <x-ui.alert tone="muted" class="mt-2">
                        Километраж в текущем этапе вводится оператором вручную в поле "Дистанция, км".
                    </x-ui.alert>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-prev
                            data-current-tab="calc" data-prev-tab="route">Назад</x-ui.button>
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-next
                            data-current-tab="calc" data-next-tab="pay">Далее</x-ui.button>
                    </div>
                </x-tabs.panel>

                <x-tabs.panel name="pay" :active="$activeTab">
                    <x-form.row cols="4">
                        <x-form.select name="payment_method" label="Способ оплаты" required data-step-required="1"
                            data-step-label="Способ оплаты" :options="[
                                'cash' => 'Наличные',
                                'card' => 'Карта',
                                'cashless' => 'Безнал',
                                'client_balance' => 'Баланс клиента',
                                'org_balance' => 'Баланс орг.',
                            ]" :value="old('payment_method', $order->payment_method ?? 'cash')" />
                        <x-form.input name="prepaid_amount" type="number" step="0.01" label="Предоплата"
                            :value="old('prepaid_amount', $order->prepaid_amount)" />
                        <x-form.input name="paid_amount" type="number" step="0.01" label="Оплачено"
                            :value="old('paid_amount', $order->paid_amount)" />
                        <x-form.input name="debt_amount" type="number" step="0.01" label="Долг"
                            :value="old('debt_amount', $order->debt_amount)" />
                    </x-form.row>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-prev
                            data-current-tab="pay" data-prev-tab="calc">Назад</x-ui.button>
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-next
                            data-current-tab="pay" data-next-tab="opts">Далее</x-ui.button>
                    </div>
                </x-tabs.panel>

                <x-tabs.panel name="opts" :active="$activeTab">
                    <x-form.row cols="5">
                        <x-form.toggle name="need_terminal" label="Терминал" :checked="old('need_terminal', (bool) $order->need_terminal)" />
                        <x-form.toggle name="need_docs" label="Документы" :checked="old('need_docs', (bool) $order->need_docs)" />
                        <x-form.toggle name="fragile" label="Хрупкое" :checked="old('fragile', (bool) $order->fragile)" />
                        <x-form.toggle name="lift_required" label="Лифт обязателен" :checked="old('lift_required', (bool) $order->lift_required)" />
                        <x-form.input name="helper_count" type="number" min="0" max="6" label="Грузчики"
                            :value="old('helper_count', $order->helper_count)" />
                    </x-form.row>

                    <x-form.row cols="4" class="mt-2">
                        <x-form.toggle name="options[child_seat]" label="Детское кресло" :checked="old('options.child_seat', data_get($order->options, 'child_seat'))" />
                        <x-form.toggle name="options[wagon]" label="Универсал" :checked="old('options.wagon', data_get($order->options, 'wagon'))" />
                        <x-form.toggle name="options[refrigerator]" label="Холодильник" :checked="old('options.refrigerator', data_get($order->options, 'refrigerator'))" />
                        <x-form.toggle name="options[furniture_assembly]" label="Сборка/разборка мебели"
                            :checked="old(
                                'options.furniture_assembly',
                                data_get($order->options, 'furniture_assembly'),
                            )" />
                    </x-form.row>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-prev
                            data-current-tab="opts" data-prev-tab="pay">Назад</x-ui.button>
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-next
                            data-current-tab="opts" data-next-tab="meta">Далее</x-ui.button>
                    </div>
                </x-tabs.panel>

                <x-tabs.panel name="meta" :active="$activeTab">
                    <x-form.textarea name="comment" rows="4" label="Комментарий">
                        {{ old('comment', $order->comment) }}
                    </x-form.textarea>

                    <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                        <x-ui.button type="button" variant="gray" size="sm" data-wizard-prev
                            data-current-tab="meta" data-prev-tab="opts">Назад</x-ui.button>
                        <div class="flex items-center gap-2">
                            <x-ui.button :href="route('admin.orders.index')" variant="ghost" size="sm">Отмена</x-ui.button>
                            <x-ui.button type="submit" variant="primary" size="sm">
                                {{ $isEdit ? 'Сохранить' : 'Создать' }}
                            </x-ui.button>
                        </div>
                    </div>
                </x-tabs.panel>
            </x-tabs.root>
        </x-crud.fields>
    </x-crud.form>

    <x-tabs.script />
@endsection

@push('scripts')
    <script>
        (function() {
            const tabOrder = ['main', 'route', 'calc', 'pay', 'opts', 'meta'];

            function findTabLink(key) {
                return document.querySelector('[data-tabs-root] [role="tab"][data-tab="' + key + '"]');
            }

            function activeTabKey() {
                const selected = document.querySelector('[data-tabs-root] [role="tab"][aria-selected="true"]');
                return selected ? selected.getAttribute('data-tab') : null;
            }

            function activateTab(key) {
                const link = findTabLink(key);
                if (link) {
                    link.click();
                }
            }

            function persistTab(key) {
                if (!key) {
                    return;
                }

                const url = new URL(window.location.href);
                url.searchParams.set('tab', key);
                window.history.replaceState({}, '', url.pathname + url.search + window.location.hash);
            }

            function resolveControl(target) {
                if (!target) {
                    return null;
                }
                if (target.matches('input, select, textarea')) {
                    return target;
                }
                return target.querySelector('input, select, textarea');
            }

            function clearStepErrors(panel) {
                panel.querySelectorAll('.step-inline-error').forEach((el) => el.remove());
                panel.querySelectorAll('.step-invalid').forEach((el) => el.classList.remove('step-invalid',
                    'border-rose-500'));
            }

            function markStepError(target, control, message) {
                const host = target.matches('input, select, textarea') ? target.parentElement : target;
                const err = document.createElement('p');
                err.className = 'step-inline-error mt-1 text-sm text-rose-600';
                err.textContent = message;
                host.appendChild(err);
                control.classList.add('step-invalid', 'border-rose-500');
            }

            function validateStep(tabKey) {
                const panel = document.querySelector('[data-tab-panel="' + tabKey + '"]');
                if (!panel) {
                    return true;
                }

                clearStepErrors(panel);
                const requiredTargets = Array.from(panel.querySelectorAll('[data-step-required="1"]'));
                if (!requiredTargets.length) {
                    return true;
                }

                let firstInvalid = null;
                requiredTargets.forEach((target) => {
                    const control = resolveControl(target);
                    if (!control || control.disabled) {
                        return;
                    }

                    let invalid = false;
                    if (control.type === 'checkbox' || control.type === 'radio') {
                        invalid = !control.checked;
                    } else {
                        invalid = String(control.value || '').trim() === '';
                    }

                    if (invalid) {
                        markStepError(target, control, 'Поле обязательно');
                        if (!firstInvalid) {
                            firstInvalid = control;
                        }
                    }
                });

                if (firstInvalid) {
                    alert('Заполните обязательные поля текущего шага.');
                    firstInvalid.focus();
                    return false;
                }

                return true;
            }

            function initWizard() {
                const root = document.querySelector('[data-tabs-root]');
                if (!root) {
                    return;
                }

                const queryTab = new URLSearchParams(window.location.search).get('tab');
                let initialTab = tabOrder.includes(queryTab) ? queryTab : null;
                if (initialTab) {
                    activateTab(initialTab);
                }

                root.querySelectorAll('[role="tab"][data-tab]').forEach((link) => {
                    link.addEventListener('click', () => {
                        const key = link.getAttribute('data-tab');
                        setTimeout(() => persistTab(key), 0);
                    });
                });

                const activeKey = activeTabKey();
                if (activeKey) {
                    persistTab(activeKey);
                }

                document.querySelectorAll('[data-wizard-prev]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        activateTab(btn.getAttribute('data-prev-tab'));
                    });
                });

                document.querySelectorAll('[data-wizard-next]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const currentTab = btn.getAttribute('data-current-tab');
                        const nextTab = btn.getAttribute('data-next-tab');
                        if (!validateStep(currentTab)) {
                            return;
                        }
                        activateTab(nextTab);
                    });
                });
            }

            function initViaPointsRepeater() {
                const list = document.getElementById('via-list');
                const template = document.getElementById('via-template');
                const addBtn = document.getElementById('via-add');
                if (!list || !template || !addBtn) {
                    return;
                }

                const bindRow = (row) => {
                    const up = row.querySelector('.via-up');
                    const down = row.querySelector('.via-down');
                    const remove = row.querySelector('.via-remove');

                    if (up) {
                        up.addEventListener('click', () => {
                            const prev = row.previousElementSibling;
                            if (prev) {
                                list.insertBefore(row, prev);
                            }
                        });
                    }

                    if (down) {
                        down.addEventListener('click', () => {
                            const next = row.nextElementSibling;
                            if (next) {
                                list.insertBefore(next, row);
                            }
                        });
                    }

                    if (remove) {
                        remove.addEventListener('click', () => {
                            const rows = list.querySelectorAll('.via-row');
                            if (rows.length <= 1) {
                                const input = row.querySelector('input[name="via_points[]"]');
                                if (input) {
                                    input.value = '';
                                }
                                return;
                            }
                            row.remove();
                        });
                    }
                };

                list.querySelectorAll('.via-row').forEach(bindRow);

                addBtn.addEventListener('click', () => {
                    const node = template.content.firstElementChild.cloneNode(true);
                    list.appendChild(node);
                    bindRow(node);
                    const input = node.querySelector('input[name="via_points[]"]');
                    if (input) {
                        input.focus();
                    }
                });
            }

            function initTariffFilter() {
                const clientSelect = document.getElementById('client_id');
                const tariffSelect = document.getElementById('tariff_id');
                const onlyClientToggle = document.getElementById('only_client_tariffs_ui');
                const hint = document.getElementById('tariff-client-hint');
                if (!tariffSelect || !clientSelect || !onlyClientToggle) {
                    return;
                }

                const placeholder = tariffSelect.querySelector('option[value=""]') ?
                    tariffSelect.querySelector('option[value=""]').cloneNode(true) :
                    new Option('—', '');

                const sourceOptions = Array.from(tariffSelect.querySelectorAll('option'))
                    .filter((option) => option.value !== '')
                    .map((option, index) => ({
                        value: option.value,
                        label: option.textContent,
                        scopeType: option.dataset.scopeType || 'global',
                        scopeId: option.dataset.scopeId || '',
                        index,
                    }));

                function refreshTariffs() {
                    const selectedValue = tariffSelect.value;
                    const clientId = String(clientSelect.value || '').trim();
                    const onlyClient = Boolean(onlyClientToggle.checked);

                    let filtered = sourceOptions.filter((item) => {
                        if (!clientId) {
                            return !onlyClient;
                        }
                        if (onlyClient) {
                            return item.scopeType === 'customer' && item.scopeId === clientId;
                        }
                        if (item.scopeType === 'customer') {
                            return item.scopeId === clientId;
                        }
                        return item.scopeType === 'global';
                    });

                    if (clientId && !onlyClient) {
                        filtered = filtered.sort((a, b) => {
                            const weight = (item) => {
                                if (item.scopeType === 'customer' && item.scopeId === clientId) {
                                    return 0;
                                }
                                if (item.scopeType === 'global') {
                                    return 1;
                                }
                                return 2;
                            };
                            const diff = weight(a) - weight(b);
                            return diff !== 0 ? diff : a.index - b.index;
                        });
                    } else {
                        filtered = filtered.sort((a, b) => a.index - b.index);
                    }

                    tariffSelect.innerHTML = '';
                    tariffSelect.appendChild(placeholder.cloneNode(true));

                    filtered.forEach((item) => {
                        const option = new Option(item.label, item.value, false, item.value === selectedValue);
                        option.dataset.scopeType = item.scopeType;
                        option.dataset.scopeId = item.scopeId;
                        tariffSelect.appendChild(option);
                    });

                    const stillExists = Array.from(tariffSelect.options)
                        .some((option) => option.value === selectedValue);
                    tariffSelect.value = stillExists ? selectedValue : '';

                    if (hint) {
                        if (onlyClient && !clientId) {
                            hint.textContent = 'Выберите клиента, чтобы включить фильтр клиентских тарифов.';
                        } else if (onlyClient && clientId && filtered.length === 0) {
                            hint.textContent = 'Для выбранного клиента нет индивидуальных тарифов.';
                        } else {
                            hint.textContent = 'Сначала показываются тарифы клиента, затем глобальные.';
                        }
                    }
                }

                clientSelect.addEventListener('change', refreshTariffs);
                onlyClientToggle.addEventListener('change', refreshTariffs);
                refreshTariffs();
            }

            function bootOrderForm() {
                const root = document.querySelector('[data-tabs-root]');
                if (!root || root.dataset.wizardBooted === '1') {
                    return;
                }

                root.dataset.wizardBooted = '1';
                initWizard();
                initViaPointsRepeater();
                initTariffFilter();
            }

            document.addEventListener('DOMContentLoaded', bootOrderForm);
            document.addEventListener('turbo:load', bootOrderForm);
        })();
    </script>
@endpush
