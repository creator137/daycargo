<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrgTopupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // проверим политикой в контроллере
    }

    public function rules(): array
    {
        return [
            'type'    => ['required', 'in:topup,debit'],
            'amount'  => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
