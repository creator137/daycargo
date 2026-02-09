<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\City;

class DriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Политики применяются в контроллере
    }

    public function rules(): array
    {
        $id = optional($this->route('driver'))->id;
        $allowed = City::where('active', true)->pluck('name')->all();

        return [
            'full_name'         => ['required', 'string', 'max:255'],
            'callsign'          => ['nullable', 'string', 'max:50'],
            'status'            => ['required', Rule::in(['active', 'blocked', 'pending'])],

            'vehicle_type_id'   => ['required', 'exists:vehicle_types,id'],
            'driver_group_id'   => ['nullable', 'exists:driver_groups,id'],
            'supports_terminal' => ['sometimes', 'boolean'],
            'is_loader' => ['boolean'],
            'phone'             => ['required', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255', Rule::unique('drivers', 'email')->ignore($id)],
            'birth_date'        => ['nullable', 'date'],

            // доп поля профиля (есть в drivers)
            'first_name'        => ['nullable', 'string', 'max:100'],
            'last_name'         => ['nullable', 'string', 'max:100'],
            'second_name'       => ['nullable', 'string', 'max:100'],
            'citizenship'       => ['nullable', 'string', 'max:100'],
            'employment_type'   => ['nullable', 'string', 'max:50'],

            // city_id — по ID
            'city_id'           => ['nullable', 'integer', 'exists:cities,id'],

            // main_city / cities — по строковому названию города
            'main_city' => ['required', Rule::in($allowed)],
            'cities'    => ['nullable', 'array'],
            'cities.*'  => [Rule::in($allowed)],

            'partner_name'      => ['nullable', 'string', 'max:255'],

            'payout_card'           => ['nullable', 'string', 'max:32'],
            'payout_first_name_en'  => ['nullable', 'string', 'max:100'],
            'payout_last_name_en'   => ['nullable', 'string', 'max:100'],
            'yandex_wallet'         => ['nullable', 'string', 'max:255'],

            'sms_fixed_code'    => ['nullable', 'string', 'max:16'],
            'sort'              => ['nullable', 'integer', 'min:0'],
            'comment'           => ['nullable', 'string'],

            // --- НОВОЕ: реквизиты документов ---
            'passport_series'        => ['nullable', 'string', 'max:20'],
            'passport_number'        => ['nullable', 'string', 'max:20'],
            'passport_issued_by'     => ['nullable', 'string', 'max:255'],
            'passport_issued_at'     => ['nullable', 'date'],
            'passport_reg_address'   => ['nullable', 'string', 'max:500'],
            'passport_fact_address'  => ['nullable', 'string', 'max:500'],

            'inn'                    => ['nullable', 'string', 'max:20'],
            'ogrnip'                 => ['nullable', 'string', 'max:20'],

            'snils' => ['nullable', 'string', 'max:20'],

            'driver_license_series'          => ['nullable', 'string', 'max:20'],
            'driver_license_number'          => ['nullable', 'string', 'max:20'],
            'driver_license_category'        => ['nullable', 'string', 'max:10'],
            'driver_license_experience_from' => ['nullable', 'date'],
            'driver_license_expires_at'      => ['nullable', 'date'],

            // Доступ в приложение
            'password'              => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:6'],

            // Фото
            'avatar'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // до 5 МБ

            // --- НОВОЕ: сканы (driver_files) ---
            'docs'              => ['nullable', 'array'],
            'docs.*'            => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'], // до 10 МБ
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'supports_terminal' => $this->boolean('supports_terminal'),
            // если сорт не задан — 100
            'is_loader'         => $this->boolean('is_loader'),
            'sort' => $this->filled('sort') ? (int)$this->input('sort') : 100,
        ]);
    }
}
