<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Политика отработает в контроллере
    }

    public function rules(): array
    {
        $id = optional($this->route('vehicleType'))->id;

        return [
            'code'         => [
                'required',
                'string',
                'max:8',
                Rule::unique('vehicle_types', 'code')->ignore($id),
            ],
            'name'         => ['required', 'string', 'max:255'],
            'capacity_kg'  => ['required', 'integer', 'min:0'],
            'length_cm'    => ['required', 'integer', 'min:0'],
            'width_cm'     => ['required', 'integer', 'min:0'],
            'height_cm'    => ['required', 'integer', 'min:0'],

            // не обяз. — но если пусто, проставим 0 в prepareForValidation()
            'sort'         => ['nullable', 'integer', 'min:0'],
            'active'       => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            // если сортировка не заполнена — не пихаем null в БД (ставим 0)
            'sort'   => ($this->filled('sort') && is_numeric($this->input('sort')))
                ? (int) $this->input('sort')
                : 0,
        ]);
    }
}
