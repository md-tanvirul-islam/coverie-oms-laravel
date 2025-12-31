<?php

namespace App\DataTables;

use App\Http\Requests\Income\FilterIncomeRequest;
use App\Services\IncomeService;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class IncomesDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('income_type', fn ($income) => $income->incomeType?->name)
            ->addColumn('store', fn ($income) => $income->store?->name)
            ->addColumn('employee', fn ($income) => $income->employee?->name_and_code)
            ->addColumn('action', 'incomes.action')
            ->setRowId('id');
    }

    public function query(FilterIncomeRequest $request, IncomeService $service)
    {
        $data = $request->validated();

        return $service->list($data, true);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('incomes_table')
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
            Column::make('income_type'),
            Column::make('store'),
            Column::make('employee'),
            Column::make('amount'),
            Column::make('income_date'),
            Column::make('note'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Incomes_' . date('YmdHis');
    }
}
