<?php

namespace App\Exports;

use App\Models\Stores;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StoressExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Stores::all([
            'user_id',
            'name',
            'slug',
            'type',
            'logo',
            'status',
            'created_by',
            'updated_by'
        ]);
    }

    public function headings(): array
    {
        return [
            'user_id',
            'name',
            'slug',
            'type',
            'logo',
            'status',
            'created_by',
            'updated_by'
        ];
    }
}
