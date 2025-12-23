{{-- resources/views/admin/drivers/form.blade.php --}}

@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $driver->exists;
        $title = $isEdit ? 'Правка водителя' : 'Создание водителя';
        $action = $isEdit ? route('admin.drivers.update', $driver) : route('admin.drivers.store');
        $method = $isEdit ? 'PUT' : 'POST';

        // Типы документов (совпадают с полями из формы/endpoint)
        $docTypes = [
            'osagoScan1' => 'ОСАГО (скан)',
            'passportScan1' => 'Паспорт (1-я страница)',
            'passportScan2' => 'Паспорт (прописка/2-я)',
            'driverLicenseScan1' => 'Вод. удостоверение (лицевая)',
            'driverLicenseScan2' => 'Вод. удостоверение (оборот)',
            'ptsScan1' => 'ПТС (1-я)',
            'ptsScan2' => 'ПТС (2-я)',
        ];

        $filesByType = [];
        if (method_exists($driver, 'files')) {
            try {
                $filesByType = $driver->relationLoaded('files')
                    ? $driver->files->groupBy('type')
                    : $driver->files()->get()->groupBy('type');
            } catch (\Throwable $e) {
                $filesByType = [];
            }
        }
    @endphp

    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.drivers.index')" :hasFiles="true">
        <x-crud.fields>

            {{-- Фото + ФИО/статус --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Фотография</label>
                    @if ($driver->avatar_url)
                        <img src="{{ $driver->avatar_url }}" alt="{{ $driver->full_name }}"
                            class="w-24 h-24 rounded-full object-cover border mb-2">
                    @endif
                    <x-form.file name="avatar" accept="image/*" hint="JPG/PNG/WEBP до 5 МБ." />
                </div>

                <x-form.input name="full_name" label="ФИО" required :value="old('full_name', $driver->full_name)" />
                <x-form.select name="status" label="Статус" :options="['active' => 'Активен', 'blocked' => 'Заблокирован', 'pending' => 'Неактивирован']" :value="old('status', $driver->status ?? 'pending')" required />
            </div>

            {{-- Анкета из заявки (но в админке редактируем вручную, если нужно) --}}
            <x-form.row cols="3">
                <x-form.input name="last_name" label="Фамилия" :value="old('last_name', $driver->last_name)" />
                <x-form.input name="first_name" label="Имя" :value="old('first_name', $driver->first_name)" />
                <x-form.input name="second_name" label="Отчество" :value="old('second_name', $driver->second_name)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="citizenship" label="Гражданство" :value="old('citizenship', $driver->citizenship)" />
                <x-form.input name="employment_type" label="Тип занятости" :value="old('employment_type', $driver->employment_type)" />
                <x-form.input name="city_id" label="Город (cityId из формы)" type="number" min="0"
                    :value="old('city_id', $driver->city_id)" />
            </x-form.row>

            {{-- Связки / класс --}}
            <x-form.row cols="3">
                <x-form.select name="vehicle_type_id" label="Класс авто" :options="$vehicleTypes" :value="old('vehicle_type_id', $driver->vehicle_type_id)" required />
                <x-form.select name="driver_group_id" label="Группа исполнителей (опц.)" :options="['' => '—'] + $driverGroups->toArray()"
                    :value="old('driver_group_id', $driver->driver_group_id)" />
                <x-form.toggle name="supports_terminal" label="Поддержка терминала" :checked="old('supports_terminal', (bool) $driver->supports_terminal)" />
            </x-form.row>

            {{-- Контакты --}}
            <x-form.row cols="3">
                <x-form.input name="phone" label="Мобильный телефон" required :value="old('phone', $driver->phone)" />
                <x-form.input name="email" label="Email" type="email" :value="old('email', $driver->email)" />
                <x-form.date name="birth_date" label="Дата рождения" :value="old('birth_date', $driver->birth_date)" />
            </x-form.row>

            {{-- Города + позывной --}}
            <x-form.row cols="2">
                <x-form.select name="main_city" label="Главный город" :options="['' => '—'] + $citiesOptions" :value="old('main_city', $driver->main_city)" required />
                <x-form.input name="callsign" label="Позывной" :value="old('callsign', $driver->callsign)" />
            </x-form.row>

            {{-- Множественный выбор городов (чекбоксы) --}}
            <x-form.multiselect-checkboxes name="cities" label="Города работы (множественно)" :options="$citiesOptions"
                :values="old('cities', $driver->cities ?? [])" :columns="3" />

            {{-- Платёжные реквизиты / партнёр --}}
            <x-form.row cols="3">
                <x-form.input name="payout_card" label="Номер банковской карты" :value="old('payout_card', $driver->payout_card)" />
                <x-form.input name="payout_first_name_en" label="Имя (латиницей)" :value="old('payout_first_name_en', $driver->payout_first_name_en)" />
                <x-form.input name="payout_last_name_en" label="Фамилия (латиницей)" :value="old('payout_last_name_en', $driver->payout_last_name_en)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="yandex_wallet" label="Яндекс.Кошелёк" :value="old('yandex_wallet', $driver->yandex_wallet)" />
                <x-form.input name="partner_name" label="Партнёр (компания/флит)" :value="old('partner_name', $driver->partner_name)" />
                <x-form.input name="sort" type="number" min="0" step="1" label="Сортировка"
                    :value="old('sort', $driver->sort ?? 100)" />
            </x-form.row>

            {{-- Доступ в приложение (опционально) --}}
            <x-form.row cols="2">
                <x-form.input name="password" type="password" label="Пароль для приложения" />
                <x-form.input name="password_confirmation" type="password" label="Повтор пароля" />
            </x-form.row>

            <x-form.input name="sms_fixed_code" label="Фиксированный SMS-код" :value="old('sms_fixed_code', $driver->sms_fixed_code)" />

            {{-- Документы (driver_files) --}}
            <div class="mt-2">
                <div class="text-sm font-medium text-slate-700 mb-2">Документы (сканы)</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($docTypes as $type => $label)
                        @php
                            $existing = $filesByType[$type] ?? collect();
                            $first = $existing->first();
                            $url = null;
                            if ($first && !empty($first->path)) {
                                try {
                                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($first->path);
                                } catch (\Throwable $e) {
                                    $url = null;
                                }
                            }
                        @endphp

                        <div class="rounded-lg border border-slate-200 p-3 bg-white">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-slate-800">{{ $label }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        Тип: <span class="font-mono">{{ $type }}</span>
                                        @if ($existing->count() > 0)
                                            · загружено: {{ $existing->count() }}
                                        @else
                                            · нет файлов
                                        @endif
                                    </div>
                                </div>

                                @if ($url)
                                    <a href="{{ $url }}" target="_blank"
                                        class="text-sm text-indigo-600 hover:underline whitespace-nowrap">
                                        Открыть
                                    </a>
                                @endif
                            </div>

                            <div class="mt-2">
                                <x-form.file name="docs[{{ $type }}]" accept="image/*,application/pdf"
                                    hint="Загрузить/заменить (png/jpg/webp/pdf)." />
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <x-form.textarea name="comment" rows="3" label="Комментарий">
                {{ old('comment', $driver->comment) }}
            </x-form.textarea>

        </x-crud.fields>

        <x-form.actions :cancel="route('admin.drivers.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
