<?php

namespace App\Http\Requests\Stores;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:stores,name,'.$this->store->id,
            'type' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ];
    }
}
