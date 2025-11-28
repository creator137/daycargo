@extends('layouts.admin')

@section('content')
    @php
        $title = 'Роли пользователя';
        $action = route('admin.acl.users.update', $user);
    @endphp

    <x-crud.form :title="$title" :action="$action" method="PUT" :backUrl="route('admin.acl.users.index')">
        <x-crud.fields>
            <div class="mb-2 text-sm text-slate-600">
                <div><span class="font-medium">Пользователь:</span> {{ $user->name }}</div>
                <div><span class="font-medium">Email:</span> {{ $user->email }}</div>
            </div>

            <x-form.checkboxes name="roles" label="Роли" :options="$roles" :values="old('roles', $selected)" :columns="3" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.acl.users.index')" submitLabel="Сохранить" />
    </x-crud.form>
@endsection
