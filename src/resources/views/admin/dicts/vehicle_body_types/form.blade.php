@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $item->exists;
        $title = $isEdit ? 'Правка типа кузова' : 'Добавление типа кузова';
        $action = $isEdit ? route('admin.vehicle_body_types.update', $item) : route('admin.vehicle_body_types.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.dicts.vehicle_body_types')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="name" label="Название" required :value="old('name', $item->name)" />
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка" :value="old('sort', $item->sort ?? 100)" />
            </x-form.row>

            <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $item->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.dicts.vehicle_body_types')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
