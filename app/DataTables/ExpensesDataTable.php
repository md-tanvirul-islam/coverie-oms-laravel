<?php

namespace App\DataTables;

use App\Http\Requests\Expense\FilterExpenseRequest;
use App\Services\ExpenseService;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ExpensesDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('expense_type', fn ($expense) => $expense->expenseType?->name)
            ->addColumn('store', fn ($expense) => $expense->store?->name)
            ->addColumn('employee', fn ($expense) => $expense->employee?->name_and_code)
            ->addColumn('action', 'expenses.action')
            ->setRowId('id');
    }

    public function query(FilterExpenseRequest $request, ExpenseService $service)
    {
        $data = $request->validated();

        return $service->list($data, true);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('expenses_table')
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
            Column::make('expense_type'),
            Column::make('store'),
            Column::make('employee'),
            Column::make('amount'),
            Column::make('expense_date'),
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
        return 'Expenses_' . date('YmdHis');
    }
}
