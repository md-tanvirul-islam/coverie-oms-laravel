<?php

namespace App\Exports;

use App\Enums\StoreType;
use App\Http\Requests\Store\FilterStoreRequest;
use App\Services\StoreService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StoresExport implements FromQuery, WithHeadings, WithMapping
{
    private $storeService;
    public function __construct(public $filter_data = [])
    {
        $this->storeService = new StoreService;
    }

    public function query()
    {
        return $this->storeService->list($this->filter_data, true);
    }

    /**
     * Map each row before export
     */
    public function map($store): array
    {
        return [
            $store->name,
            StoreType::options()[$store->type] ?? '—',
            $store->status ? 'Active' : 'Inactive',
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Type',
            'Status',
        ];
    }
}
