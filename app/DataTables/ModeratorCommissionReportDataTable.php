<?php

namespace App\DataTables;

use Illuminate\Database\Query\Builder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use DB;

class ModeratorCommissionReportDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('commission_fee_per_order', fn($row) => number_format($row->commission_fee_per_order, 2))
            ->editColumn('total_commission', fn($row) => number_format($row->total_commission, 2))
            ->setRowId('moderator_id');
    }

    public function query(): Builder
    {
        return DB::table('courier_paid_invoices as cpi')
            ->join('orders as o', 'o.id', '=', 'cpi.order_id')
            ->join('moderators as m', 'm.id', '=', 'o.moderator_id')
            ->select(
                'm.id as moderator_id',
                'm.name',
                'm.code',
                'o.order_date',
                DB::raw("SUM(o.quantity) as total_quantity"),
                DB::raw("SUM(o.quantity * m.commission_fee_per_order) as total_commission")
            )
            ->where('cpi.invoice_type', 'DELIVERY')
            ->groupBy(
                'o.order_date',
                'm.id'
            );
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('moderator-commission-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0) // order by name
            ->buttons([
                'excel', 'csv', 'pdf', 'print', 'reset', 'reload'
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('name')->title('Moderator Name'),
            Column::make('code')->title('Moderator Code'),
            Column::make('commission_fee_per_order')->title('Commission Per Order'),
            Column::make('total_quantity')->title('Total Quantity'),
            Column::make('total_commission')->title('Total Commission'),
        ];
    }

    protected function filename(): string
    {
        return 'moderator_commission_report_' . date('YmdHis');
    }
}
