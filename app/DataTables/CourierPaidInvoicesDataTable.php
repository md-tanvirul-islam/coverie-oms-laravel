<?php

namespace App\DataTables;

use App\Models\CourierPaidInvoice;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;

class CourierPaidInvoicesDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('moderator', fn($row) => $row->order?->moderator?->name_and_code ?? '-')
            ->addColumn('action', 'courier_paid_invoices.action')
            ->editColumn('created_date', fn($row) => $row->created_date?->format('Y-m-d H:i') ?? '-')
            ->setRowId('id');
    }

    public function query(CourierPaidInvoice $model): Builder
    {
        // Eager load related order and moderator
        return $model->with([
            'order.moderator'
        ])->newQuery();
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('courier_paid_invoices-table')
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
            Column::make('consignment_id'),
            Column::make('created_date')->title('Created At'),
            Column::make('invoice_type'),
            Column::make('courier_name'),
            Column::make('collected_amount'),
            Column::make('recipient_name'),
            Column::make('recipient_phone'),
            Column::make('collectable_amount'),
            Column::make('cod_fee'),
            Column::make('delivery_fee'),
            Column::make('final_fee'),
            Column::make('discount'),
            Column::make('additional_charge'),
            Column::make('compensation_cost'),
            Column::make('promo_discount'),
            Column::make('payout'),
            Column::make('merchant_order_id'),
            Column::computed('moderator')
                ->title('Order Taken By')
                ->exportable(true)
                ->printable(true),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'courier_paid_invoices_' . date('YmdHis');
    }
}
