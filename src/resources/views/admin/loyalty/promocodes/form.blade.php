@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $pc->exists;
        $title = $isEdit ? "Правка промокода {$pc->code}" : 'Создание промокода';
        $action = $isEdit ? route('admin.loyalty.promocodes.update', $pc) : route('admin.loyalty.promocodes.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.loyalty.promocodes.index')">
        <x-crud.fields>
            <x-form.row cols="3">
                <x-form.input name="code" label="Код" required :value="old('code', $pc->code)" />
                <x-form.select name="type" label="Тип" required :options="[
                    'bonus_fixed' => 'Фикс.бонус',
                    'bonus_percent' => '% бонус',
                    'free_delivery' => 'Бесплатная подача',
                ]" :value="old('type', $pc->type ?? 'bonus_fixed')" />
                <x-form.input name="value" type="number" step="0.01" label="Значение" :value="old('value', $pc->value)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="starts_at" type="datetime-local" label="Начало" :value="old('starts_at', optional($pc->starts_at)->format('Y-m-d\TH:i'))" />
                <x-form.input name="ends_at" type="datetime-local" label="Окончание" :value="old('ends_at', optional($pc->ends_at)->format('Y-m-d\TH:i'))" />
                <x-form.toggle name="active" label="Активен" :checked="old('active', (bool) ($pc->active ?? true))" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="usage_limit" type="number" min="1" step="1"
                    label="Общий лимит использований" :value="old('usage_limit', $pc->usage_limit)" />
                <x-form.input name="per_client_limit" type="number" min="1" step="1" label="Лимит на клиента"
                    :value="old('per_client_limit', $pc->per_client_limit)" />
            </x-form.row>

            <x-form.textarea name="comment" label="Комментарий">{{ old('comment', $pc->comment) }}</x-form.textarea>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.loyalty.promocodes.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
