<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('promocodes.update') || $this->user()?->can('promocodes.create');
    }

    public function rules(): array
    {
        $id = $this->route('promo_code')?->id;
        return [
            'code'   => ['required', 'string', 'max:64', 'unique:promo_codes,code,' . ($id ?? 'null') . ',id'],
            'type'   => ['required', 'in:bonus_fixed,bonus_percent,free_delivery'],
            'value'  => ['nullable', 'numeric', 'min:0'],
            'active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at'   => ['nullable', 'date', 'after_or_equal:starts_at'],
            'usage_limit'      => ['nullable', 'integer', 'min:1'],
            'per_client_limit' => ['nullable', 'integer', 'min:1'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['active' => $this->boolean('active')]);
    }
}
