<?php

namespace App\DataTables;

use App\Http\Requests\Team\FilterTeamRequest;
use App\Services\TeamService;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class TeamsDataTable extends DataTable
{
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('status', fn($row) => $row->status ? "Active" : "Inactive")
            ->addColumn('action', 'teams.action')
            ->setRowId('id');
    }

    public function query(FilterTeamRequest $request, TeamService $service)
    {
        $data = $request->validated();

        return $service->list($data, true);
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('teams_table')
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
            Column::make('status'),
            // Column::make('created_by'),
            // Column::make('updated_by'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Teams_' . date('YmdHis');
    }
}
