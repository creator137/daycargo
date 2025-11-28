<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\City;

class ClientTariffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowed = City::where('active', true)->pluck('name')->all();

        return [
            'name'               => ['required', 'string', 'max:255'],
            'tariff_group_id'    => ['nullable', Rule::exists('tariff_groups', 'id')],
            'vehicle_type_id'    => ['required', Rule::exists('vehicle_types', 'id')],
            'city' => ['nullable', Rule::in($allowed)],
            'description'        => ['nullable', 'string', 'max:5000'],

            'available_site'       => ['nullable', 'boolean'],
            'available_app'        => ['nullable', 'boolean'],
            'available_dispatcher' => ['nullable', 'boolean'],
            'available_driver'     => ['nullable', 'boolean'],
            'available_cabinet'    => ['nullable', 'boolean'],

            'require_prepayment' => ['nullable', 'boolean'],
            'addresses_min'      => ['nullable', 'integer', 'min:1', 'max:10'],

            'sort'   => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $bool = fn($key) => $this->boolean($key);

        $this->merge([
            'available_site'       => $bool('available_site'),
            'available_app'        => $bool('available_app'),
            'available_dispatcher' => $bool('available_dispatcher'),
            'available_driver'     => $bool('available_driver'),
            'available_cabinet'    => $bool('available_cabinet'),
            'require_prepayment'   => $bool('require_prepayment'),
            'active'               => $bool('active'),
            'addresses_min'        => $this->filled('addresses_min') ? (int)$this->input('addresses_min') : 1,
            'sort'                 => $this->filled('sort') ? (int)$this->input('sort') : 100,
        ]);
    }
}
