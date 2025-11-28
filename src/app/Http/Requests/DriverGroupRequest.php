<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\City;

class DriverGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowed = City::where('active', true)->pluck('name')->all();

        return [
            'name'            => ['required', 'string', 'max:255'],
            'city' => ['nullable', Rule::in($allowed)],
            'profession'      => ['nullable', 'string', 'max:100'],
            'vehicle_type_id' => ['required', Rule::exists('vehicle_types', 'id')],
            'priority'        => ['nullable', 'integer', 'min:0', 'max:9999'],
            'sort'            => ['nullable', 'integer', 'min:0'],
            'description'     => ['nullable', 'string', 'max:5000'],
            'active'          => ['nullable', 'boolean'],

            // видимость по классам
            'visibility_mode'           => ['required', Rule::in(['own_and_lower', 'manual'])],
            'visible_vehicle_type_ids'  => ['nullable', 'array'],
            'visible_vehicle_type_ids.*' => [Rule::exists('vehicle_types', 'id')],

            // ограничение тарифов
            'client_tariff_ids'   => ['nullable', 'array'],
            'client_tariff_ids.*' => [Rule::exists('client_tariffs', 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $mode = $this->input('visibility_mode') ?: 'own_and_lower';

        $this->merge([
            'active'   => $this->boolean('active'),
            'priority' => $this->filled('priority') ? (int) $this->input('priority') : 10,
            'sort'     => $this->filled('sort') ? (int) $this->input('sort') : 100,
            'visibility_mode' => $mode,
            // если выбран режим "свой и ниже" — чистим ручной список
            'visible_vehicle_type_ids' => $mode === 'manual'
                ? array_values(array_filter((array) $this->input('visible_vehicle_type_ids')))
                : [],
        ]);
    }
}
