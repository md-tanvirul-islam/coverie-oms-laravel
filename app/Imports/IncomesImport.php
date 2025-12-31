<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Store;
use App\Rules\ExcelDate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;

class IncomesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        $income_type_id = IncomeType::where('name', $row['income_type'])->value('id');
        $store_id = Store::where('name', $row['store'])->value('id');
        $employee_id = Employee::where('code', $row['employee_code'])->value('id');

        $income_date = excelDateToDateTimeString($row['income_date']);
        $income_date = date('Y-m-d', strtotime($income_date));

        return new Income([
            'team_id' => getPermissionsTeamId(),
            'income_type_id' => $income_type_id,
            'store_id' => $store_id,
            'employee_id' => $employee_id,
            'amount' => $row['amount'],
            'income_date' => $income_date,
            'note' => $row['note']
        ]);
    }

    public function rules(): array
    {
        return [
            'income_type' => 'required|exists:income_types,name',
            'store' => 'nullable|exists:stores,name',
            'employee_code' => 'nullable|exists:employees,code',
            'amount' => 'required|numeric|min:0',
            'income_date' => ['required', new ExcelDate()],
            'note' => 'nullable|string|max:2000'
        ];
    }
}
