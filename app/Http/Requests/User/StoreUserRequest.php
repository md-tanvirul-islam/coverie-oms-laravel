<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:6',
            'role_ids'     => 'array',
            'role_ids.*'   => [
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $user = Auth::user();
                    $query->where('team_id', $user->team_id);
                }),
            ]
        ];
    }
}
