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
        $user = Auth::user();

        return [
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'nullable|min:6',
            'role_ids'     => 'array',
            'role_ids.*'   => [
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                }),
            ],
            'store_ids'     => 'required|array',
            'store_ids.*'   => [
                'integer',
                Rule::exists('stores', 'id')->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                }),
            ],
            'full_data' => 'required|boolean'
        ];
    }
}
