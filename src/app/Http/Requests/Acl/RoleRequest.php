<?php

namespace App\Http\Requests\Acl;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = optional($this->route('role'))->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($id),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::exists('permissions', 'id')],
        ];
    }
}
