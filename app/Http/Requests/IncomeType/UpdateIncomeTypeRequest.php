<?php

namespace App\Http\Requests\IncomeType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:income_types,name,' . $this->income_type->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'required|boolean',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
        ];
    }
}
