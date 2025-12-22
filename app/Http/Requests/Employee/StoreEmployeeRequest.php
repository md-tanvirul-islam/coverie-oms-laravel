<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();

        return [
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'joining_date' => 'nullable|date',
            'address'      => 'nullable|string|max:255',
            'code'         => 'required|string|max:50|unique:employees,code',
            'commission_fee_per_order'  => 'nullable|string',
            'has_login' => 'required|boolean',
            'email' => 'required_if:has_login,1|email|unique:users,email',
            'password' => 'required_if:has_login,1|min:6',
            'role_ids' => 'required_if:has_login,1|array',
            'store_ids' => 'required_if:has_login,1|array',
            'role_ids.*'   => [
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                }),
            ],
            'store_ids.*'   => [
                'integer',
                Rule::exists('stores', 'id')->where(function ($query) use ($user) {
                    $query->where('team_id', $user->team_id);
                }),
            ],
        ];
    }
}
