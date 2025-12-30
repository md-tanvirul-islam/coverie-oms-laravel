<?php

namespace App\Http\Requests\ExpressType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpressTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:express_types,name,' . $this->express_type->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ];
    }
}
