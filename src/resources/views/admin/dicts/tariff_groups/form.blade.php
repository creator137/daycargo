@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $group->exists;
        $title = $isEdit ? 'Правка группы тарифов' : 'Создание группы тарифов';
        $action = $isEdit ? route('admin.tariff_groups.update', $group) : route('admin.tariff_groups.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backRoute="route('admin.dicts.tariff_groups')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="name" type="text" label="Название" required :value="old('name', $group->name)" />
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка" :value="old('sort', $group->sort)" />
            </x-form.row>

            <x-form.textarea name="description" rows="4"
                label="Описание">{{ old('description', $group->description) }}</x-form.textarea>

            <x-form.toggle name="active" label="Активна" :checked="old('active', (bool) $group->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.dicts.tariff_groups')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
