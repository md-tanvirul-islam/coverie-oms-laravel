<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Store;
use App\Rules\ExcelDate;
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
        $expense_type_id = ExpenseType::where('name', $row['expense_type'])->value('id');
        $store_id = Store::where('name', $row['store'])->value('id');
        $employee_id = Employee::where('code', $row['employee_code'])->value('id');

        $expense_date = excelDateToDateTimeString($row['expense_date']);
        $expense_date = date('Y-m-d', strtotime($expense_date));

        return new Expense([
            'team_id' => getPermissionsTeamId(),
            'expense_type_id' => $expense_type_id,
            'store_id' => $store_id,
            'employee_id' => $employee_id,
            'amount' => $row['amount'],
            'expense_date' => $expense_date,
            'note' => $row['note']
        ]);
    }

    public function rules(): array
    {
        return [
            'expense_type' => 'required|exists:expense_types,name',
            'store' => 'nullable|exists:stores,name',
            'employee_code' => 'nullable|exists:employees,code',
            'amount' => 'required|numeric|min:0',
            'expense_date' => ['required', new ExcelDate()],
            'note' => 'nullable|string|max:2000'
        ];
    }
}
