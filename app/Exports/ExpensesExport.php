<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Expense::query()
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'expense_type',
            'store',
            'employee_name',
            'employee_code',
            'amount',
            'expense_date',
            'note'
        ];
    }

    public function map($expense): array
    {
        return [
            $expense?->expenseType?->name,
            $expense?->store?->name,
            $expense?->employee?->name,
            $expense?->employee?->code,
            $expense->amount,
            $expense->expense_date,
            $expense->note
        ];
    }
}
