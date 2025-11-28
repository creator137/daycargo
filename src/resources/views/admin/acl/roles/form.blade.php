{{-- resources/views/admin/acl/roles/form.blade.php --}}
@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $role->exists;
        $title = $isEdit ? 'Правка роли' : 'Создание роли';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.acl.roles.index')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="display_name" label="Название (рус.)" :value="old('display_name', $role->display_name)" />
                <x-form.input name="name" label="Код (тех., латиницей)" required
                    hint="например: admin, owner, accountant, viewer" :value="old('name', $role->name)" />
            </x-form.row>

            <x-form.textarea name="description" rows="2" label="Описание">
                {{ old('description', $role->description) }}
            </x-form.textarea>

            <div class="mt-2">
                <div class="text-sm font-medium text-slate-700 mb-2">Права</div>
                <div class="space-y-4">
                    @foreach ($groups as $key => $perms)
                        <div class="border rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-medium">{{ $ruGroups[$key] ?? $key }}</div>
                                <div class="text-xs flex gap-3">
                                    <button type="button" class="text-indigo-600 hover:underline"
                                        onclick="document.querySelectorAll('[data-group={{ $key }}] input[type=checkbox]').forEach(cb=>cb.checked=true)">
                                        Выбрать все
                                    </button>
                                    <button type="button" class="text-slate-600 hover:underline"
                                        onclick="document.querySelectorAll('[data-group={{ $key }}] input[type=checkbox]').forEach(cb=>cb.checked=false)">
                                        Очистить
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2" data-group="{{ $key }}">
                                @foreach ($perms as $p)
                                    @php
                                        $label =
                                            $p->display_name ??
                                            match (true) {
                                                str_ends_with($p->name, '.view') => 'Просмотр',
                                                str_ends_with($p->name, '.create') => 'Создание',
                                                str_ends_with($p->name, '.update') => 'Изменение',
                                                str_ends_with($p->name, '.delete') => 'Удаление',
                                                str_ends_with($p->name, '.toggle') => 'Вкл/Выкл',
                                                default => $p->name,
                                            };
                                    @endphp
                                    <label class="inline-flex items-center gap-2 text-sm">
                                        <input type="checkbox" name="permissions[]" value="{{ $p->name }}"
                                            @checked(in_array($p->name, old('permissions', $selected)))
                                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span>{{ $ruGroups[$key] ?? $key }} — {{ $label }}</span>
                                        <span class="text-xs text-slate-400">({{ $p->name }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.acl.roles.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
