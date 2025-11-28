@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $item->exists;
        $title = $isEdit ? 'Правка типа авто' : 'Создание типа авто';
        $action = $isEdit ? route('admin.vehicle_types.update', $item) : route('admin.vehicle_types.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backRoute="route('admin.dicts.vehicle_types')">
        <x-crud.fields>
            <x-form.row cols="4">
                <x-form.input name="code" type="text" label="Код" placeholder="S / M / L / XL / XXL" :value="old('code', $item->code)"
                    required />

                <x-form.input name="name" type="text" label="Название" required :value="old('name', $item->name)" />

                <x-form.input name="capacity_kg" type="number" step="1" min="0" label="Грузоподъёмность, кг"
                    required :value="old('capacity_kg', $item->capacity_kg)" />

                <x-form.input name="sort" type="number" step="1" min="0" label="Сортировка"
                    :value="old('sort', $item->sort)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="length_cm" type="number" step="1" min="0" label="Длина, см" required
                    :value="old('length_cm', $item->length_cm)" />
                <x-form.input name="width_cm" type="number" step="1" min="0" label="Ширина, см" required
                    :value="old('width_cm', $item->width_cm)" />
                <x-form.input name="height_cm" type="number" step="1" min="0" label="Высота, см" required
                    :value="old('height_cm', $item->height_cm)" />
            </x-form.row>

            <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $item->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.dicts.vehicle_types')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
