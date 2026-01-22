@extends('layouts.admin')

@section('content')
    @php
        /** @var \App\Models\Organization $org */
        $isEdit = $org->exists;
        $title = $isEdit ? 'Правка организации' : 'Создание организации';
        $action = $isEdit ? route('admin.organizations.update', $org) : route('admin.organizations.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.organizations.index')">
        <x-crud.fields>
            <x-form.row cols="2">
                <x-form.select name="city" label="Город" :options="['' => '—'] + $cities" :value="old('city', $org->city)" />
                <x-form.input name="short_name" label="Краткое наименование" :value="old('short_name', $org->short_name)" />
            </x-form.row>

            <x-form.input name="full_name" label="Полное наименование" required :value="old('full_name', $org->full_name)" />

            <x-form.row cols="2">
                <x-form.input name="inn" label="ИНН" :value="old('inn', $org->inn)" />
                <x-form.input name="kpp" label="КПП" :value="old('kpp', $org->kpp)" />
                <x-form.input name="edo_code" label="Код ЭДО" :value="old('edo_code', $org->edo_code)" />
            </x-form.row>

            <x-form.row cols="2">
                <x-form.input name="legal_address" label="Юридический адрес" :value="old('legal_address', $org->legal_address)" />
                <x-form.input name="postal_address" label="Почтовый адрес" :value="old('postal_address', $org->postal_address)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="director_name" label="Директор (ФИО)" :value="old('director_name', $org->director_name)" />
                <x-form.input name="director_position" label="Должность директора" :value="old('director_position', $org->director_position)" />
                <x-form.input name="chief_accountant" label="Главный бухгалтер" :value="old('chief_accountant', $org->chief_accountant)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="bank_name" label="Банк" :value="old('bank_name', $org->bank_name)" />
                <x-form.input name="bank_bik" label="БИК" :value="old('bank_bik', $org->bank_bik)" />
                <x-form.input name="bank_corr" label="Корр. счёт" :value="old('bank_corr', $org->bank_corr)" />
            </x-form.row>

            <x-form.input name="bank_account" label="Расчётный счёт" :value="old('bank_account', $org->bank_account)" />

            <x-form.row cols="3">
                <x-form.input name="phone" label="Телефон" :value="old('phone', $org->phone)" />
                <x-form.input name="email" label="Email" :value="old('email', $org->email)" />
                <x-form.input name="site" label="Сайт" :value="old('site', $org->site)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="contact_person" label="Контактное лицо (ФИО)" :value="old('contact_person', $org->contact_person)" />
                <x-form.input name="contact_phone" label="Контактный телефон" :value="old('contact_phone', $org->contact_phone)" />
                <x-form.input name="contact_email" label="Контактный email" :value="old('contact_email', $org->contact_email)" />
            </x-form.row>

            <x-form.row cols="4">
                <x-form.input name="contract_number" label="Договор №" :value="old('contract_number', $org->contract_number)" />
                <x-form.input name="contract_from" type="date" label="Дата начала" :value="old('contract_from', optional($org->contract_from)->format('Y-m-d'))" />
                <x-form.input name="contract_to" type="date" label="Дата окончания" :value="old('contract_to', optional($org->contract_to)->format('Y-m-d'))" />
                <x-form.input name="billing_period_months" type="number" min="1" step="1"
                    label="Период (мес.)" :value="old('billing_period_months', $org->billing_period_months)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="credit_limit" type="number" step="0.01" label="Кредитный лимит, ₽"
                    :value="old('credit_limit', $org->credit_limit)" />
                <x-form.input name="balance" type="number" step="0.01" label="Баланс, ₽" :value="old('balance', $org->balance)" />
                <x-form.toggle name="active" label="Активна" :checked="old('active', (bool) $org->active)" />
            </x-form.row>

            <x-form.textarea name="comment" rows="3" label="Комментарий">
                {{ old('comment', $org->comment) }}
            </x-form.textarea>
        </x-crud.fields>

        <x-form.actions :cancel="route('admin.organizations.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>

    @if ($isEdit)
        @can('balance', $org)
            <div class="mt-6">
                <x-ui.card>
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-medium">Операции по балансу</div>
                        <div class="text-sm text-slate-600">
                            Текущий баланс: <span class="font-medium">{{ number_format((float) $org->balance, 2, ',', ' ') }}
                                ₽</span>
                        </div>
                    </div>
                    <form method="post" action="{{ route('admin.organizations.topup', $org) }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        @csrf
                        <x-form.select name="type" label="Операция" :options="['credit' => 'Пополнение', 'debit' => 'Списание']" />
                        <x-form.input name="amount" type="number" step="0.01" label="Сумма, ₽" required />
                        <x-form.input name="comment" label="Комментарий" />
                        <div class="md:col-span-3">
                            <x-ui.button type="submit" variant="primary" size="sm">Выполнить</x-ui.button>
                        </div>
                    </form>
                </x-ui.card>
            </div>
        @endcan
    @endif
@endsection
