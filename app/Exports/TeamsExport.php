<?php

namespace App\Exports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Team::all([
            'name',
            'status',
            'created_by',
            'updated_by'
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'status',
            'created_by',
            'updated_by'
        ];
    }
}
