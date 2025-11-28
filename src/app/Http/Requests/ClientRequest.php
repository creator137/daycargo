<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\City;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = optional($this->route('client'))->id;
        $allowedCities = City::where('active', true)->pluck('name')->all();

        return [
            'city'        => ['nullable', Rule::in($allowedCities)],
            'client_type' => ['required', Rule::in(['person', 'company'])],
            'is_agent'    => ['nullable', 'boolean'],
            'lang'        => ['required', 'string', 'max:5'],

            'full_name'   => ['nullable', 'string', 'max:255'],
            'birth_date'  => ['nullable', 'date'],

            'phone'       => ['required', 'string', 'max:32', Rule::unique('clients', 'phone')->ignore($id)],
            'email'       => ['nullable', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($id)],

            'passport_series' => ['nullable', 'string', 'max:16'],
            'passport_number' => ['nullable', 'string', 'max:32'],

            'comment'          => ['nullable', 'string', 'max:5000'],
            'send_trip_report' => ['nullable', 'boolean'],
            'news_notifications' => ['nullable', 'boolean'],
            'allow_push'        => ['nullable', 'boolean'],

            'blacklisted'  => ['nullable', 'boolean'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'balance'      => ['nullable', 'numeric'],

            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $bool = fn($k) => $this->boolean($k);

        $this->merge([
            'is_agent'           => $bool('is_agent'),
            'send_trip_report'   => $bool('send_trip_report'),
            'news_notifications' => $bool('news_notifications'),
            'allow_push'         => $bool('allow_push'),
            'blacklisted'        => $bool('blacklisted'),
            'credit_limit'       => $this->filled('credit_limit') ? (float)$this->input('credit_limit') : 0,
            'lang'               => $this->input('lang') ?: 'ru',
            'client_type'        => $this->input('client_type') ?: 'person',
        ]);
    }
}
