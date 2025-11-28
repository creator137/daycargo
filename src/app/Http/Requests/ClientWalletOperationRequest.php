<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientWalletOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user?->can('client_economy.topup') || $user?->can('client_economy.debit');
    }

    public function rules(): array
    {
        return [
            'wallet'   => ['required', 'in:money,bonus'],
            'operation' => ['required', 'in:topup,debit'],
            'amount'   => ['required', 'numeric', 'min:0.01', 'max:99999999'],
            'comment'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}
