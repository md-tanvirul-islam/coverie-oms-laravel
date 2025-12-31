<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class FilterIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'income_type_id' => 'nullable',
            'store_id' => 'nullable',
            'employee_id' => 'nullable',
            'amount' => 'nullable',
            'income_date' => 'nullable',
            'note' => 'string|nullable',
            'created_by' => 'integer|nullable',
            'updated_by' => 'integer|nullable',
        ];
    }
}
