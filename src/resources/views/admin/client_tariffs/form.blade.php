@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $tariff->exists;
        $title = $isEdit ? 'Правка тарифа для клиентов' : 'Создание тарифа для клиентов';
        $action = $isEdit ? route('admin.client_tariffs.update', $tariff) : route('admin.client_tariffs.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backRoute="route('admin.client_tariffs.index')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="name" label="Название" required :value="old('name', $tariff->name)" />
                <x-form.select name="city" label="Город (опц.)" :options="['' => '—'] + $citiesOptions" :value="old('city', $tariff->city)" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.select name="tariff_group_id" label="Группа (опц.)" :options="$groups" :value="old('tariff_group_id', $tariff->tariff_group_id)" />
                <x-form.select name="vehicle_type_id" label="Класс авто" required :options="$vehicleTypes" :value="old('vehicle_type_id', $tariff->vehicle_type_id)" />
            </x-form.row>

            <x-form.textarea name="description" rows="3" label="Описание">
                {{ old('description', $tariff->description) }}
            </x-form.textarea>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <div class="text-sm font-medium text-slate-700">Доступность по каналам</div>
                    <x-form.toggle name="available_site" label="Сайт" :checked="old('available_site', (bool) $tariff->available_site)" />
                    <x-form.toggle name="available_app" label="Приложение" :checked="old('available_app', (bool) $tariff->available_app)" />
                    <x-form.toggle name="available_dispatcher" label="Диспетчер" :checked="old('available_dispatcher', (bool) $tariff->available_dispatcher)" />
                    <x-form.toggle name="available_driver" label="Видно водителю" :checked="old('available_driver', (bool) $tariff->available_driver)" />
                    <x-form.toggle name="available_cabinet" label="Личный кабинет" :checked="old('available_cabinet', (bool) $tariff->available_cabinet)" />
                </div>

                <div class="space-y-4">
                    <x-form.input name="addresses_min" type="number" min="1" max="10"
                        label="Мин. адресов в заказе" :value="old('addresses_min', $tariff->addresses_min)" />
                    <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка"
                        :value="old('sort', $tariff->sort)" />
                    <x-form.toggle name="require_prepayment" label="Требовать предоплату" :checked="old('require_prepayment', (bool) $tariff->require_prepayment)" />
                    <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $tariff->active)" />
                </div>
            </div>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.client_tariffs.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
