<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class FilterExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_type_id' => 'nullable',
            'store_id' => 'nullable',
            'employee_id' => 'nullable',
            'amount' => 'nullable',
            'expense_date' => 'nullable',
            'reference' => 'string|nullable',
            'note' => 'string|nullable',
            'created_by' => 'integer|nullable',
            'updated_by' => 'integer|nullable',
        ];
    }
}
