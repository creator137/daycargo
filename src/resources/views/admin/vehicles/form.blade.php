@extends('layouts.admin')

@section('content')
    @php
        $isEdit = $vehicle->exists;
        $title = $isEdit ? 'Правка автомобиля' : 'Добавление автомобиля';
        $action = $isEdit ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store');
        $method = $isEdit ? 'PUT' : 'POST';
    @endphp

    {{-- ВАЖНО: включаем загрузку файлов --}}
    <x-crud.form :title="$title" :action="$action" :method="$method" :backUrl="route('admin.vehicles.index')" :hasFiles="true">
        <x-crud.fields>

            {{-- Фото + базовые поля --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Фото автомобиля</label>
                    @if ($vehicle->photo_url)
                        <img src="{{ $vehicle->photo_url }}" alt="Фото авто"
                            class="w-32 h-24 object-cover rounded border mb-2">
                    @endif
                    <x-form.file name="photo" accept="image/*" hint="JPG/PNG/WEBP до 5 МБ." />
                </div>

                <x-form.input name="brand" label="Марка" required :value="old('brand', $vehicle->brand)" />
                <x-form.input name="model" label="Модель" required :value="old('model', $vehicle->model)" />
            </div>

            <x-form.row cols="4">
                <x-form.input name="license_plate" label="Гос. номер" required :value="old('license_plate', $vehicle->license_plate)" />
                <x-form.input name="year" type="number" min="1900" max="{{ now()->year + 1 }}" label="Год выпуска"
                    :value="old('year', $vehicle->year)" />
                <x-form.input name="color" label="Цвет" :value="old('color', $vehicle->color)" />
                <x-form.input name="vin" label="VIN" :value="old('vin', $vehicle->vin)" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.select name="vehicle_type_id" label="Класс авто" required :options="$vehicleTypes" :value="old('vehicle_type_id', $vehicle->vehicle_type_id)" />
                <x-form.select name="driver_id" label="Исполнитель (водитель)" :options="['' => '—'] + $drivers->toArray()" :value="old('driver_id', $vehicle->driver_id)" />
                <x-form.select name="status" label="Статус" required :options="['active' => 'Активен', 'blocked' => 'Заблокирован', 'pending' => 'Неактивирован']" :value="old('status', $vehicle->status ?? 'pending')" />
            </x-form.row>

            <x-form.row cols="3">
                <x-form.select name="owner_type" label="Владелец" required :options="['company' => 'Компания', 'private' => 'Частник', 'rent' => 'Аренда']" :value="old('owner_type', $vehicle->owner_type ?? 'private')" />
                <x-form.select name="city" label="Город" required :options="$cities" :value="old('city', $vehicle->city)" />
                <x-form.toggle name="is_rent" label="Арендный" :checked="old('is_rent', (bool) $vehicle->is_rent)" />
            </x-form.row>

            {{-- Опции авто: можно чекбоксами, а можно JSONом — оставим оба варианта --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <x-form.toggle name="options[wagon]" label="Кузов универсал" :checked="old('options.wagon', data_get($vehicle->options, 'wagon'))" />
                <x-form.toggle name="options[child_seat]" label="Детское кресло" :checked="old('options.child_seat', data_get($vehicle->options, 'child_seat'))" />
            </div>

            <x-form.textarea name="options_json" rows="3" label="Опции (JSON, опционально)"
                hint='Если заполните, перезапишет поля выше. Например: {"refrigerator":true,"tent":false}'>{{ old('options_json') }}</x-form.textarea>

            <x-form.textarea name="comment" rows="3" label="Комментарий">
                {{ old('comment', $vehicle->comment) }}
            </x-form.textarea>

        </x-crud.fields>

        <x-form.actions :cancel="route('admin.vehicles.index')" :submitLabel="$isEdit ? 'Сохранить' : 'Создать'" />
    </x-crud.form>
@endsection
