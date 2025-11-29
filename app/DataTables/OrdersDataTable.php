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
            ->addColumn('moderator', fn($row) => $row->moderator?->name_and_code ?? '-')
            ->addColumn('action', 'orders.action')
            ->setRowId('id');
    }

    public function query(Order $model): Builder
    {
        return $model->with('moderator')->newQuery();
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
            Column::make('invoice_id'),
            Column::make('order_date'),
            Column::make('customer_name'),
            Column::make('customer_phone'),
            Column::make('customer_address'),
            Column::make('total_cost'),
            Column::make('phone_model'),
            Column::make('moderator')->title('Order Taken By'),

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
