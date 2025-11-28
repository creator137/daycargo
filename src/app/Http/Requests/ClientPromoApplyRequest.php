<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientPromoApplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('promocodes.apply');
    }

    public function rules(): array
    {
        return [
            'code'    => ['required', 'string', 'max:64'],
            'comment' => ['nullable', 'string', 'max:500'],
        ];
    }
}
