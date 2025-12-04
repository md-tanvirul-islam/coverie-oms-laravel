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
        $query = $model->with('moderator')->newQuery();

        // Apply filters from request
        $invoiceId = request('invoice_id');
        $orderDate = request('order_date');
        $customerName = request('customer_name');
        $customerPhone = request('customer_phone');
        $quantity = request('quantity');
        $moderator = request('moderator');

        if ($invoiceId) {
            $query->where('invoice_id', 'like', "%$invoiceId%");
        }

        if ($orderDate) {
            $query->whereDate('order_date', $orderDate);
        }

        if ($customerName) {
            $query->where('customer_name', 'like', "%$customerName%");
        }

        if ($customerPhone) {
            $query->where('customer_phone', 'like', "%$customerPhone%");
        }

        if ($quantity) {
            $query->where('quantity', $quantity);
        }

        if ($moderator) {
            $query->where('moderator_id', $moderator);
        }

        return $query;
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
            Column::make('quantity'),
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
