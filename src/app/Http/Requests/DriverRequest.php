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

            'phone'             => ['required', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:255', Rule::unique('drivers', 'email')->ignore($id)],
            'birth_date'        => ['nullable', 'date'],

            'main_city' => ['required', Rule::in($allowed)],
            'cities'    => ['nullable', 'array'],
            'cities.*'  => [Rule::in($allowed)],

            'partner_name'      => ['nullable', 'string', 'max:255'],

            'payout_card'           => ['nullable', 'string', 'max:32'],
            'payout_first_name_en'  => ['nullable', 'string', 'max:100'],
            'payout_last_name_en'   => ['nullable', 'string', 'max:100'],
            'yandex_wallet'         => ['nullable', 'string', 'max:64'],

            'sms_fixed_code'    => ['nullable', 'string', 'max:16'],
            'sort'              => ['nullable', 'integer', 'min:0'],
            'comment'           => ['nullable', 'string'],

            // Доступ в приложение
            'password'              => ['nullable', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:6'],

            // Фото
            'avatar'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // до 5 МБ
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'supports_terminal' => $this->boolean('supports_terminal'),
            // если сорт не задан — 100
            'sort' => $this->filled('sort') ? (int)$this->input('sort') : 100,
        ]);
    }
}
