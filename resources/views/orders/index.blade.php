@extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Manage Orders</span>
                <div class="btn-group" role="group">
                    {{-- <a href="{{ route('orders.import') }}" class="btn btn-secondary">
                        <i class="bi bi-upload"></i> Import Excel
                    </a>

                    <a href="{{ route('orders.export') }}" class="btn btn-success">
                        <i class="bi bi-download"></i> Export Excel
                    </a> --}}

                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Order
                    </a>
                </div>
            </div>

            <div class="card-body  table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
