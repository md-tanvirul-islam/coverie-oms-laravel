<?php

namespace App\Http\Requests\IncomeType;

use Illuminate\Foundation\Http\FormRequest;

class FilterIncomeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string|nullable',
            'description' => 'string|nullable',
            'is_active' => 'boolean|nullable',
            'created_by' => 'integer|nullable',
            'updated_by' => 'integer|nullable',
        ];
    }
}
