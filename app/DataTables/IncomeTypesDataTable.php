<?php

namespace App\DataTables;

use App\Http\Requests\IncomeType\FilterIncomeTypeRequest;
use App\Services\IncomeTypeService;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class IncomeTypesDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('is_active', fn($row) => $row->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'income_types.action')
            ->setRowId('id');
    }

    public function query(FilterIncomeTypeRequest $request, IncomeTypeService $service)
    {
        $data = $request->validated();

        return $service->list($data, true);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('income_types_table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name'),
            Column::make('description'),
            Column::make('is_active'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'IncomeTypes_' . date('YmdHis');
    }
}
