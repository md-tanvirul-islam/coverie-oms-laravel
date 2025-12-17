@extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Manage Teams</span>

                <div class="btn-group" role="group">
                    {{-- <a href="{{ route('teams.import') }}" class="btn btn-primary"><i class="bi bi-upload"></i> Import Excel</a>
                    <a href="{{ route('teams.export') }}" class="btn btn-success"><i class="bi bi-download"></i> Export
                        Excel</a>

                    <a href="{{ route('teams.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Team
                    </a> --}}
                </div>
            </div>

            <div class="card-body table-responsive">
                {{ $dataTable->table() }}
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
