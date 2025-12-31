<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;

class FilterItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => 'integer|nullable',
            'name' => 'string|nullable',
            'code' => 'string|nullable',
            'description' => 'string|nullable',
            'is_active' => 'boolean|nullable'
        ];
    }
}
