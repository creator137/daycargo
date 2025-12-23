<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reg-email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'reg-password' => ['required', 'string', 'min:6', 'max:255'],
            'reg-password-confirm' => ['required', 'same:reg-password'],
            'reg-user-type' => ['required', Rule::in(['physical', 'legal'])],

            // ВАЖНО: на первом шаге регистрации больше ничего не валидируем,
            // потому что профиль дозаполняется на /cabinet/profile вторым шагом.
        ];
    }
}
