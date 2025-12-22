@extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Manage Stores</span>

                <div class="btn-group" role="group">
                    {{-- <a href="{{ route('stores.import') }}" class="btn btn-primary"><i class="bi bi-upload"></i> Import
                        Excel</a>
                    <a href="{{ route('stores.export') }}" class="btn btn-success"><i class="bi bi-download"></i> Export
                        Excel</a> --}}

                    <a href="{{ route('stores.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Store
                    </a>
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
