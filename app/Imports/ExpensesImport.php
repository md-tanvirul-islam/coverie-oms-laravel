<?php

namespace App\Imports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class ExpensesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        return new Expense([
            'expense_type_id' => $row['expense_type_id'],
            'store_id' => $row['store_id'],
            'employee_id' => $row['employee_id'],
            'amount' => $row['amount'],
            'expense_date' => $row['expense_date'],
            'reference' => $row['reference'],
            'note' => $row['note'],
            'created_by' => $row['created_by'],
            'updated_by' => $row['updated_by']
        ]);
    }

    public function rules(): array
    {
        return [
            'expense_type' => 'required|exists:expense_types,name',
            'store' => 'nullable|exists:stores,name',
            'employee_id' => 'nullable|exists:employees,code',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string|max:2000'
        ];
    }
}
