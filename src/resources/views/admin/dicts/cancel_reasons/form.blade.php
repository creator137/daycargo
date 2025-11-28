@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $reason->exists;
        $title = $isEdit ? 'Правка причины отмены' : 'Создание причины отмены';
        $action = $isEdit ? route('admin.cancel_reasons.update', $reason) : route('admin.cancel_reasons.store');
        $method = $isEdit ? 'PUT' : 'POST';
        $initiators = \App\Models\CancelReason::initiatorOptions(); // [code=>label]
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backRoute="route('admin.dicts.cancel_reasons')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.input name="code" type="text" label="Код" required :value="old('code', $reason->code)" />
                <x-form.input name="title" type="text" label="Название" required :value="old('title', $reason->title)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.select name="initiator" label="Инициатор" :options="$initiators" required :value="old('initiator', $reason->initiator)" />
                <x-form.input name="window_minutes" type="number" min="0" step="1"
                    label="Окно применения, мин" :value="old('window_minutes', $reason->window_minutes)" />
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка"
                    :value="old('sort', $reason->sort)" />
            </x-form.row>

            <div class="text-sm font-medium text-slate-700 mt-2">Штрафы</div>
            <x-form.row cols="3">
                <x-form.input name="client_fee_fixed" type="number" step="0.01" min="0" label="Клиент — фикс, ₽"
                    :value="old('client_fee_fixed', $reason->client_fee_fixed)" />
                <x-form.input name="client_fee_percent" type="number" step="0.01" min="0" max="100"
                    label="Клиент — %" :value="old('client_fee_percent', $reason->client_fee_percent)" />
                <div></div>
                <x-form.input name="driver_fee_fixed" type="number" step="0.01" min="0"
                    label="Водитель — фикс, ₽" :value="old('driver_fee_fixed', $reason->driver_fee_fixed)" />
                <x-form.input name="driver_fee_percent" type="number" step="0.01" min="0" max="100"
                    label="Водитель — %" :value="old('driver_fee_percent', $reason->driver_fee_percent)" />
                <x-form.input name="driver_fee_min" type="number" step="0.01" min="0"
                    label="Водитель — минималка, ₽" :value="old('driver_fee_min', $reason->driver_fee_min)" />
            </x-form.row>

            <x-form.textarea name="comment" rows="3"
                label="Комментарий">{{ old('comment', $reason->comment) }}</x-form.textarea>

            <x-form.toggle name="active" label="Активна" :checked="old('active', (bool) $reason->active)" />
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.dicts.cancel_reasons')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
