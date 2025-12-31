<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => 'required|integer|exists:stores,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:items,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
            'item_attributes' => 'array',
            'item_attributes.*.label' => 'required|string|max:255',
            'item_attributes.*.type' => 'required|string|in:text,number,select,date',
            'item_attributes.*.options' => 'nullable|string|max:1000',
            'item_attributes.*.is_required' => 'required|boolean',
        ];
    }
}
