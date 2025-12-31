<?php

namespace App\DataTables;

use App\Http\Requests\Item\FilterItemRequest;
use App\Services\ItemService;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ItemsDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('store', fn($item) => $item?->store?->name)
            ->addColumn('is_active', fn($item) => $item->is_active ? 'Yes' : 'No')
            ->addColumn('action', 'items.action')
            ->setRowId('id');
    }

    public function query(FilterItemRequest $request, ItemService $service)
    {
        $data = $request->validated();

        return $service->list($data, true);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('items_table')
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
            Column::make('name'),
            Column::make('code'),
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
        return 'items_' . date('YmdHis');
    }
}
