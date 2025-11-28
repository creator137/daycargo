@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $perm->exists;
        $title = $isEdit ? 'Правка права' : 'Создание права';
        $action = $isEdit ? route('admin.acl.permissions.update', $perm) : route('admin.acl.permissions.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.acl.permissions.index')">
        <x-crud.fields>
            <x-form.input name="name" label="Системное имя права" required :value="old('name', $perm->name)"
                hint="Например: drivers.update или reports.view" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.acl.permissions.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
