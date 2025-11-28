@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $city->exists;
        $title = $isEdit ? 'Правка города' : 'Создание города';
        $action = $isEdit ? route('admin.cities.update', $city) : route('admin.cities.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.dicts.cities')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="name" label="Название" required :value="old('name', $city->name)" />
                <x-form.input name="slug" label="Слаг (латиницей)" :value="old('slug', $city->slug)"
                    hint="Оставьте пустым — сгенерируется из названия." />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка"
                    :value="old('sort', $city->sort ?? 100)" />
                <div class="flex items-end">
                    <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) $city->active)" />
                </div>
            </x-form.row>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.dicts.cities')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
