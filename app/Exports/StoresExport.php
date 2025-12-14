<?php

namespace App\Exports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StoresExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Store::all([
            'name',
            'type',
            'status'
        ]);
    }

    public function headings(): array
    {
        return [
            'name',
            'type',
            'status'
        ];
    }
}
