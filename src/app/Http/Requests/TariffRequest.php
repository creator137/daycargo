<?php

namespace App\Http\Requests;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'scope_type'      => ['required', Rule::in(array_keys(Tariff::SCOPE_TYPES))],
            'scope_id'        => ['nullable', 'integer'],
            'city'            => ['nullable', 'string', 'max:255'],

            'tariff_type'     => ['required', Rule::in(array_keys(Tariff::TARIFF_TYPES))],

            'base_price'      => ['required', 'numeric', 'min:0'],

            // фикс тариф
            'base_hours'        => ['nullable', 'integer', 'min:1'],
            'extra_hour_price' => ['nullable', 'numeric', 'min:0'],
            'loader_hour_price' => ['nullable', 'numeric', 'min:0'],
            'top_loading_price' => ['nullable', 'numeric', 'min:0'],
            'side_loading_price' => ['nullable', 'numeric', 'min:0'],

            // поминутный (старый)
            'per_km'          => ['required', 'numeric', 'min:0'],
            'per_min'         => ['required', 'numeric', 'min:0'],
            'min_price'       => ['required', 'numeric', 'min:0'],
            'wait_free_min'   => ['required', 'integer', 'min:0'],
            'wait_per_min'    => ['required', 'numeric', 'min:0'],

            'active'          => ['boolean'],
        ];
    }
}
