<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')->id;

        return [
            'name' => [
                'nullable',
                'string',

                Rule::unique('roles', 'name')
                    ->where(fn($query) => $query->where(
                        'team_id',
                        session('team_id')
                    ))
                    ->ignore($roleId),
            ],

            'permissions' => ['required', 'array'],

            'permissions.*' => [
                'exists:permissions,name'
            ]
        ];
    }
}
