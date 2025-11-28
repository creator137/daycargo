<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CancelReasonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // политику проверяем в контроллере
    }

    public function rules(): array
    {
        $id = optional($this->route('cancelReason'))->id;

        return [
            'code'   => ['required', 'string', 'max:50', Rule::unique('cancel_reasons', 'code')->ignore($id)],
            'title'  => ['required', 'string', 'max:255'],

            'initiator' => ['required', Rule::in(array_keys(\App\Models\CancelReason::initiatorOptions()))],
            'window_minutes' => ['nullable', 'integer', 'min:0'],

            'client_fee_fixed'   => ['nullable', 'numeric', 'min:0'],
            'client_fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'driver_fee_fixed'   => ['nullable', 'numeric', 'min:0'],
            'driver_fee_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'driver_fee_min'     => ['nullable', 'numeric', 'min:0'],

            'comment' => ['nullable', 'string', 'max:2000'],
            'sort'    => ['nullable', 'integer', 'min:0'],
            'active'  => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'sort'   => ($this->filled('sort') && is_numeric($this->input('sort'))) ? (int)$this->input('sort') : 0,
        ]);
    }
}
