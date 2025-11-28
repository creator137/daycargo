<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TariffGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'sort'        => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'active'      => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'sort'   => ($this->filled('sort') && is_numeric($this->input('sort')))
                ? (int) $this->input('sort')
                : 100,
        ]);
    }
}
