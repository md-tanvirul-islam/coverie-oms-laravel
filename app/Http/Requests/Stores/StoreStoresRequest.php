<?php

namespace App\Http\Requests\Stores;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
                        'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255|unique:stores,name',
            'slug' => 'required|string|max:255|unique:stores,slug',
            'type' => 'nullable|string|max:100',
            'logo' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ];
    }
}
