<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // политику проверим в контроллере
    }

    public function rules(): array
    {
        $id = optional($this->route('city'))->id;

        return [
            'name'   => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($id)],
            'slug'   => ['nullable', 'alpha_dash', 'max:255', Rule::unique('cities', 'slug')->ignore($id)],
            'sort'   => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slug = $this->input('slug');
        if (!$slug && $this->filled('name')) {
            $slug = Str::slug($this->input('name'));
        }

        $this->merge([
            'slug'   => $slug,
            'active' => $this->boolean('active'),
            'sort'   => $this->filled('sort') ? (int)$this->input('sort') : 100,
        ]);
    }
}
