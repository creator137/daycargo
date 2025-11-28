@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $group->exists;
        $title = $isEdit ? 'Правка группы исполнителей' : 'Создание группы исполнителей';
        $action = $isEdit ? route('admin.driver_groups.update', $group) : route('admin.driver_groups.store');
        $method = $isEdit ? 'PUT' : 'POST';
        $mode = old('visibility_mode', $group->visibility_mode ?? 'own_and_lower');
        $manual = $mode === 'manual';
        $visibleIds = old('visible_vehicle_type_ids', $group->visible_vehicle_type_ids ?? []);
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.driver_groups.index')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="name" label="Название" required :value="old('name', $group->name)" />
                <x-form.select name="vehicle_type_id" label="Класс авто" required :options="$vehicleTypes" :value="old('vehicle_type_id', $group->vehicle_type_id)" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.select name="city" label="Город (опц.)" :options="['' => '—'] + (is_array($citiesOptions) ? $citiesOptions : $citiesOptions->toArray())" :value="old('city', $group->city)" />
                <x-form.input name="profession" label="Профессия (опц.)" :value="old('profession', $group->profession)" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="priority" type="number" min="0" step="1" label="Приоритет"
                    :value="old('priority', $group->priority)" />
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка"
                    :value="old('sort', $group->sort)" />
            </x-form.row>

            <x-form.textarea name="description" rows="3" label="Описание">
                {{ old('description', $group->description) }}
            </x-form.textarea>

            {{-- Видимость заказов по классам --}}
            <div x-data="{ mode: '{{ $mode }}' }" class="space-y-3">
                <div class="text-sm font-medium text-slate-700">Видимость заказов по классам</div>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="visibility_mode" value="own_and_lower" @checked($mode === 'own_and_lower')
                            @change="mode = 'own_and_lower'" class="text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span>Свой и ниже</span>
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="radio" name="visibility_mode" value="manual" @checked($mode === 'manual')
                            @change="mode = 'manual'" class="text-indigo-600 border-slate-300 focus:ring-indigo-500">
                        <span>Выбрать вручную</span>
                    </label>
                </div>

                <div x-show="mode === 'manual'">
                    <x-form.checkboxes name="visible_vehicle_type_ids" label="Классы, которые видит группа"
                        :options="$vehicleTypes" :values="$visibleIds" :columns="3"
                        hint="Если ничего не выбрать — группа ничего не увидит (кроме ограничений по тарифам)." />
                </div>
            </div>

            {{-- Ограничение видимости тарифов (опционально) --}}
            <div class="space-y-2 pt-2">
                <div class="text-sm font-medium text-slate-700">Ограничить видимость тарифов</div>
                <x-form.checkboxes name="client_tariff_ids" label="Тарифы для клиентов" :options="$clientTariffs"
                    :values="old('client_tariff_ids', $selectedTariffIds)" :columns="2" hint="Если не выбрано — группа видит тарифы по общим правилам." />
            </div>

            <x-form.toggle name="active" label="Активна" :checked="old('active', (bool) $group->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.driver_groups.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
