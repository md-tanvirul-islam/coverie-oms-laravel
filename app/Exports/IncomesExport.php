<?php

namespace App\Exports;

use App\Models\Income;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Income::query()
            ->orderBy('income_date', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'income_type',
            'store',
            'employee_name',
            'employee_code',
            'amount',
            'income_date',
            'note'
        ];
    }

    public function map($income): array
    {
        return [
            $income?->incomeType?->name,
            $income?->store?->name,
            $income?->employee?->name,
            $income?->employee?->code,
            $income->amount,
            $income->income_date,
            $income->note
        ];
    }
}
