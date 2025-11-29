<?php

namespace App\Http\Requests\Moderator;

use Illuminate\Foundation\Http\FormRequest;

class StoreModeratorRequest extends FormRequest
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
        return [
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'joining_date' => 'nullable|date',
            'address'      => 'nullable|string|max:255',
            'code'         => 'required|string|max:50|unique:moderators,code',
            'commission_fee_per_order'  => 'nullable|string',
        ];
    }
}
