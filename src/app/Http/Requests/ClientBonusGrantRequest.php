<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientBonusGrantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('client_economy.topup') ?? false;
    }

    public function rules(): array
    {
        return [
            'points'     => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'source'     => ['nullable', 'string', 'max:50'],
            'expires_at' => ['nullable', 'date'],
            'comment'    => ['nullable', 'string', 'max:1000'],
        ];
    }
}
