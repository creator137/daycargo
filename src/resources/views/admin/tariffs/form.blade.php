{{-- resources/views/admin/tariffs/form.blade.php --}}
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
            <x-form.select name="vehicle_type_id" label="Тип кузова" :options="$vehicleTypes" {{-- ожидаем [id => name] --}} :value="old('vehicle_type_id', $tariff->vehicle_type_id)"
                required />

            <x-form.select name="scope_type" label="Область действия" :options="$scopeOptions" {{-- ['global'=>'Глобально', ...] --}}
                :value="old('scope_type', $tariff->scope_type)" required />

            <x-form.input name="scope_id" type="number" label="ID клиента/интеграции (опционально)" placeholder="Например: 42"
                :value="old('scope_id', $tariff->scope_id)" />

            <x-form.input name="city" label="Город" placeholder="Например: Москва" :value="old('city', $tariff->city)" />

            <x-form.row cols="3">
                <x-form.input name="base_price" type="number" step="0.01" min="0" label="Базовая цена, ₽"
                    :value="old('base_price', $tariff->base_price)" required />
                <x-form.input name="per_km" type="number" step="0.01" min="0" label="₽ за км" :value="old('per_km', $tariff->per_km)"
                    required />
                <x-form.input name="per_min" type="number" step="0.01" min="0" label="₽ за минуту"
                    :value="old('per_min', $tariff->per_min)" required />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="min_price" type="number" step="0.01" min="0" label="Мин. заказ, ₽"
                    :value="old('min_price', $tariff->min_price)" required />
                <x-form.input name="wait_free_min" type="number" step="1" min="0" label="Беспл. ожидание, мин"
                    :value="old('wait_free_min', $tariff->wait_free_min)" required />
                <x-form.input name="wait_per_min" type="number" step="0.01" min="0" label="₽/мин ожидания"
                    :value="old('wait_per_min', $tariff->wait_per_min)" required />
            </x-form.row>

            <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $tariff->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.tariffs.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
