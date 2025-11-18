<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|min:6'
        ];
    }
}
