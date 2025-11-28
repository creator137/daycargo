<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrgEmployeeAttachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // политика в контроллере
    }

    public function rules(): array
    {
        return [
            // можно по client_id или по телефону
            'client_id'      => ['nullable', 'exists:clients,id'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'is_admin'       => ['nullable', 'boolean'],
            'active'         => ['nullable', 'boolean'],
            'personal_limit' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_admin' => $this->boolean('is_admin'),
            'active'   => $this->boolean('active'),
        ]);
    }
}
