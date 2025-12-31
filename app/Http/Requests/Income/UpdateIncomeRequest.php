<?php

namespace App\Http\Requests\Income;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'income_type_id' => 'required|exists:income_types,id',
            'store_id' => 'nullable|exists:stores,id',
            'employee_id' => 'nullable|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'note' => 'nullable|string|max:2000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|max:2048|mimes:pdf,jpg,jpeg,png,doc,docx',
            'delete_documents' => 'nullable|array',
            'delete_documents.*' => 'integer|exists:artifacts,id',
        ];
    }
}
