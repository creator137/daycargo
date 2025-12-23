<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:32'],

            // физик
            'person_full_name' => ['nullable', 'string', 'max:255'],
            'person_birth_date' => ['nullable', 'date'],

            // юрик
            'legal_company_name' => ['nullable', 'string', 'max:255'],
            'legal_inn' => ['nullable', 'string', 'max:20'],
            'legal_kpp' => ['nullable', 'string', 'max:20'],
            'legal_company_address' => ['nullable', 'string', 'max:500'],
            'legal_contact_name' => ['nullable', 'string', 'max:255'],
            'legal_contact_position' => ['nullable', 'string', 'max:255'],
            'legal_comment' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
