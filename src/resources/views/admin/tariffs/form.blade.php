@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $tariff->exists;
        $title = $isEdit ? 'Правка тарифа' : 'Создание тарифа';
        $action = $isEdit ? route('admin.tariffs.update', $tariff) : route('admin.tariffs.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backRoute="route('admin.tariffs.index')">
        <x-crud.fields>

            {{-- БАЗОВЫЕ НАСТРОЙКИ --}}
            <x-form.row cols="2">
                <x-form.select name="vehicle_type_id" label="Тип авто" :options="$vehicleTypes" :value="old('vehicle_type_id', $tariff->vehicle_type_id)" required />

                <x-form.select name="scope_type" label="Область действия" :options="$scopeOptions" :value="old('scope_type', $tariff->scope_type)" required />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="scope_id" type="number" label="ID клиента / интеграции (опционально)"
                    placeholder="Например: 42" :value="old('scope_id', $tariff->scope_id)" />

                <x-form.input name="city" label="Город" placeholder="Например: Москва" :value="old('city', $tariff->city)" />
            </x-form.row>

            {{-- ТИП ТАРИФА --}}
            <x-form.select name="tariff_type" label="Тип тарифа" :options="\App\Models\Tariff::TARIFF_TYPES" :value="old('tariff_type', $tariff->tariff_type ?? 'per_minute')" required />

            {{-- БАЗОВЫЕ ЦЕНЫ (ОБЩИЕ ДЛЯ ВСЕХ ТАРИФОВ) --}}
            <x-ui.card class="mt-4">
                <div class="text-sm font-medium mb-2">
                    Базовые цены (применяются ко всем тарифам)
                </div>

                <x-form.row cols="4">
                    <x-form.input name="base_price" type="number" step="0.01" min="0" label="Базовая цена, ₽"
                        :value="old('base_price', $tariff->base_price)" required />

                    <x-form.input name="loader_hour_price" type="number" step="0.01" min="0"
                        label="Грузчик, ₽ / час" :value="old('loader_hour_price', $tariff->loader_hour_price)" />

                    <x-form.input name="per_km" type="number" step="0.01" min="0" label="₽ за км"
                        :value="old('per_km', $tariff->per_km)" required />

                    <x-form.input name="per_min" type="number" step="0.01" min="0" label="₽ за минуту"
                        :value="old('per_min', $tariff->per_min)" required />
                </x-form.row>

                <x-form.row cols="3">
                    <x-form.input name="min_price" type="number" step="0.01" min="0" label="Минимальный заказ, ₽"
                        :value="old('min_price', $tariff->min_price)" required />

                    <x-form.input name="wait_free_min" type="number" step="1" min="0"
                        label="Бесплатное ожидание, мин" :value="old('wait_free_min', $tariff->wait_free_min)" required />

                    <x-form.input name="wait_per_min" type="number" step="0.01" min="0"
                        label="₽ за минуту ожидания" :value="old('wait_per_min', $tariff->wait_per_min)" required />
                </x-form.row>

                <x-ui.alert tone="muted" class="mt-2">
                    Ставка грузчика указывается один раз и используется при расчёте заказов
                    независимо от типа тарифа.
                </x-ui.alert>
            </x-ui.card>

            {{-- ФИКСИРОВАННЫЙ ТАРИФ --}}
            <x-ui.card class="mt-4">
                <div class="text-sm font-medium mb-2">
                    Фиксированный тариф (дополнительно)
                </div>

                <x-form.row cols="4">
                    <x-form.input name="base_hours" type="number" min="1" label="Базовые часы" :value="old('base_hours', $tariff->base_hours)" />

                    <x-form.input name="extra_hour_price" type="number" step="0.01" min="0"
                        label="Цена доп. часа, ₽" :value="old('extra_hour_price', $tariff->extra_hour_price)" />

                    <x-form.input name="top_loading_price" type="number" step="0.01" min="0"
                        label="Верхняя погрузка, ₽" :value="old('top_loading_price', $tariff->top_loading_price)" />

                    <x-form.input name="side_loading_price" type="number" step="0.01" min="0"
                        label="Боковая погрузка, ₽" :value="old('side_loading_price', $tariff->side_loading_price)" />
                </x-form.row>

                <x-ui.alert tone="muted" class="mt-2">
                    Используется для фиксированных тарифов
                    (например: «Газель 5 метров Тент»).
                </x-ui.alert>
            </x-ui.card>

            <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $tariff->active)" />

        </x-crud.fields>

        <x-form.actions :cancel="route('admin.tariffs.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
