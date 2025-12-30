<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Expense::all([
            'expense_type_id',
            'store_id',
            'employee_id',
            'amount',
            'expense_date',
            'reference',
            'note'
        ]);
    }

    public function headings(): array
    {
        return [
            'expense_type_id',
            'store_id',
            'employee_id',
            'amount',
            'expense_date',
            'reference',
            'note'
        ];
    }
}
