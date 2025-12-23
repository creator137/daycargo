<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Политики на контроллере
    }

    public function rules(): array
    {
        return [
            'city'      => ['required', 'string', 'max:100'],
            'full_name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],

            'legal_address'  => ['nullable', 'string', 'max:500'],
            'postal_address' => ['nullable', 'string', 'max:500'],

            'director_name'     => ['nullable', 'string', 'max:255'],
            'director_position' => ['nullable', 'string', 'max:255'],
            'chief_accountant'  => ['nullable', 'string', 'max:255'],

            'inn'  => ['nullable', 'string', 'max:20'],
            'kpp'  => ['nullable', 'string', 'max:20'],
            'ogrn' => ['nullable', 'string', 'max:20'],

            'bank_name'   => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:32'],
            'bank_corr'   => ['nullable', 'string', 'max:32'],
            'bank_bik'    => ['nullable', 'string', 'max:16'],

            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'site'  => ['nullable', 'string', 'max:255'],

            'contact_position' => ['nullable', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
            'contact_email'  => ['nullable', 'email', 'max:255'],

            'contract_number'       => ['nullable', 'string', 'max:100'],
            'contract_from'         => ['nullable', 'date'],
            'contract_to'           => ['nullable', 'date'],
            'billing_period_months' => ['nullable', 'integer', 'min:1', 'max:24'],

            'credit_limit' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'active'       => ['nullable', 'boolean'],
            'balance'      => ['nullable', 'numeric', 'min:-99999999', 'max:99999999'],
            'comment'      => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'billing_period_months' => $this->filled('billing_period_months') ? (int)$this->input('billing_period_months') : null,
        ]);
    }
}
