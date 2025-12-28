{{-- resources/views/admin/drivers/form.blade.php --}}

@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $driver->exists;
        $title = $isEdit ? 'Правка водителя' : 'Создание водителя';
        $action = $isEdit ? route('admin.drivers.update', $driver) : route('admin.drivers.store');
        $method = $isEdit ? 'PUT' : 'POST';

        // ✅ FIX: делаем два массива:
        // - для city_id: id => name
        // - для main_city и cities: name => name (если они строковые)
        $citiesOptionsById = [];
        $citiesOptionsByName = [];

        // если контроллер уже передал нужные переменные — используем
        if (isset($citiesOptionsById) && is_array($citiesOptionsById) && count($citiesOptionsById)) {
            // nothing
        } else {
            // fallback: пытаемся восстановить из $citiesOptions
            // если $citiesOptions был id=>name — ок
            // если $citiesOptions был name=>name — сделаем id=>name через запрос
            if (isset($citiesOptions) && is_array($citiesOptions)) {
                $keys = array_keys($citiesOptions);
                $looksLikeIdMap = count($keys) && is_numeric($keys[0]);
                if ($looksLikeIdMap) {
                    $citiesOptionsById = $citiesOptions;
                } else {
                    // name=>name -> грузим id=>name
                    try {
                        $citiesOptionsById = \App\Models\City::query()
                            ->where('active', true)
                            ->orderBy('sort')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    } catch (\Throwable $e) {
                        $citiesOptionsById = [];
                    }
                }
            } else {
                try {
                    $citiesOptionsById = \App\Models\City::query()
                        ->where('active', true)
                        ->orderBy('sort')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                } catch (\Throwable $e) {
                    $citiesOptionsById = [];
                }
            }
        }

        // name=>name (для строковых полей)
        // если $citiesOptions был id=>name — превратим в name=>name
        if (isset($citiesOptions) && is_array($citiesOptions) && count($citiesOptions)) {
            $keys = array_keys($citiesOptions);
            $looksLikeIdMap = count($keys) && is_numeric($keys[0]);
            if ($looksLikeIdMap) {
                foreach ($citiesOptions as $id => $name) {
                    $citiesOptionsByName[(string) $name] = (string) $name;
                }
            } else {
                $citiesOptionsByName = $citiesOptions;
            }
        } else {
            foreach ($citiesOptionsById as $id => $name) {
                $citiesOptionsByName[(string) $name] = (string) $name;
            }
        }

        // Типы документов
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

            <x-form.row cols="3">
                <x-form.input name="last_name" label="Фамилия" :value="old('last_name', $driver->last_name)" />
                <x-form.input name="first_name" label="Имя" :value="old('first_name', $driver->first_name)" />
                <x-form.input name="second_name" label="Отчество" :value="old('second_name', $driver->second_name)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="citizenship" label="Гражданство" :value="old('citizenship', $driver->citizenship)" />
                <x-form.input name="employment_type" label="Тип занятости" :value="old('employment_type', $driver->employment_type)" />

                {{-- ✅ FIX: city_id должен быть селект по ID --}}
                <x-form.select name="city_id" label="Город (cityId из формы)" :options="['' => '—'] + $citiesOptionsById" :value="old('city_id', (string) ($driver->city_id ?? ''))" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.select name="vehicle_type_id" label="Класс авто" :options="$vehicleTypes" :value="old('vehicle_type_id', $driver->vehicle_type_id)" required />
                <x-form.select name="driver_group_id" label="Группа исполнителей (опц.)" :options="['' => '—'] + $driverGroups->toArray()"
                    :value="old('driver_group_id', $driver->driver_group_id)" />
                <x-form.toggle name="supports_terminal" label="Поддержка терминала" :checked="old('supports_terminal', (bool) $driver->supports_terminal)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.input name="phone" label="Мобильный телефон" required :value="old('phone', $driver->phone)" />
                <x-form.input name="email" label="Email" type="email" :value="old('email', $driver->email)" />
                <x-form.date name="birth_date" label="Дата рождения" :value="old('birth_date', $driver->birth_date)" />
            </x-form.row>

            <x-form.row cols="2">
                {{-- main_city у тебя строка -> name=>name --}}
                <x-form.select name="main_city" label="Главный город" :options="['' => '—'] + $citiesOptionsByName" :value="old('main_city', $driver->main_city)" required />
                <x-form.input name="callsign" label="Позывной" :value="old('callsign', $driver->callsign)" />
            </x-form.row>

            {{-- cities обычно строками -> name=>name --}}
            <x-form.multiselect-checkboxes name="cities" label="Города работы (множественно)" :options="$citiesOptionsByName"
                :values="old('cities', $driver->cities ?? [])" :columns="3" />

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

            <x-form.row cols="2">
                <x-form.input name="password" type="password" label="Пароль для приложения" />
                <x-form.input name="password_confirmation" type="password" label="Повтор пароля" />
            </x-form.row>

            <x-form.input name="sms_fixed_code" label="Фиксированный SMS-код" :value="old('sms_fixed_code', $driver->sms_fixed_code)" />

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
