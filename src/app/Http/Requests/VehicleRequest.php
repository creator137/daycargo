<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Политики применяются в контроллере через authorizeResource — тут можно оставить true
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('vehicle')?->id;

        return [
            'city'            => ['required', 'string', 'max:100'],
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'driver_id'       => ['nullable', 'exists:drivers,id'],
            'owner_type'      => ['required', 'in:company,private,rent'],
            'is_rent'         => ['sometimes', 'boolean'],

            'brand'           => ['required', 'string', 'max:100'],
            'model'           => ['required', 'string', 'max:100'],
            'year'            => ['nullable', 'integer', 'between:1900,' . (now()->year + 1)],
            'color'           => ['nullable', 'string', 'max:50'],

            'license_plate'   => ['required', 'string', 'max:32', 'unique:vehicles,license_plate,' . $id],
            'vin'             => ['nullable', 'string', 'max:64'],

            // файл фото, а не текстовый путь
            'photo'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            // options можем принять либо как массив, либо как JSON в options_json
            'options'         => ['nullable'],
            'options_json'    => ['nullable', 'string'],

            'body_type_id'   => ['nullable', 'exists:vehicle_body_types,id'],
            'passenger_seats' => ['nullable', 'integer', 'min:1'],
            'actual_capacity_kg' => ['nullable', 'integer', 'min:0'],

            'loading_types'  => ['nullable', 'array'],
            'loading_types.*' => ['integer', 'exists:vehicle_loading_types,id'],


            'status'          => ['required', 'in:active,blocked,pending'],
            'comment'         => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // приводим is_rent к булю
        if ($this->has('is_rent')) {
            $this->merge(['is_rent' => (bool) $this->input('is_rent')]);
        }

        // если пришёл options_json — парсим его в options
        if ($this->filled('options_json') && !$this->filled('options')) {
            $json = $this->string('options_json')->toString();
            $decoded = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge(['options' => $decoded]);
            }
        }
    }
}
