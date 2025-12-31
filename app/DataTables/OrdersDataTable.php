<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Database\Eloquent\Builder;

class OrdersDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('store', fn($row) => $row->store?->name ?? '-')
            ->addColumn('employee', fn($row) => $row->employee?->name_and_code ?? '-')
            ->addColumn('action', 'orders.action')
            ->setRowId('id');
    }

    public function query(Order $model): Builder
    {
        return $model->with(['employee:id,name,code', 'store:id,name'])->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('orders-table')
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
            Column::make('store'),
            Column::make('invoice_code'),
            Column::make('order_date'),
            Column::make('customer_name'),
            Column::make('customer_phone'),
            Column::make('customer_address'),
            Column::make('total_quantity'),
            Column::make('sub_total'),
            Column::make('discount'),
            Column::make('total_cost'),
            Column::make('employee')->title('Order Taken By'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Orders_' . date('YmdHis');
    }
}
