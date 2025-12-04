{{-- @extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Manage Orders</span>
                <div class="btn-group" role="group">
                    <a href="{{ route('orders.import') }}" class="btn btn-secondary">
                        <i class="bi bi-upload"></i> Import Excel
                    </a>

                    <a href="{{ route('orders.export') }}" class="btn btn-success">
                        <i class="bi bi-download"></i> Export Excel
                    </a>

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
@endpush --}}




@extends('layouts.app')

@section('content')
<div class="m-2">
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Manage Orders</span>
            <div class="btn-group">
                <a href="{{ route('orders.import') }}" class="btn btn-secondary">
                    <i class="bi bi-upload"></i> Import Excel
                </a>

                <a href="{{ route('orders.export') }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Export Excel
                </a>

                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Order
                </a>
            </div>
        </div>

        {{-- 🔍 FILTER BOX --}}
        <div class="card-body border rounded p-3 mb-3 bg-light">
            <form id="orderFilters">

                <div class="row">

                    <div class="col-md-2">
                        <label>Invoice ID</label>
                        <input type="text" name="invoice_id" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-2">
                        <label>Order Date</label>
                        <input type="date" name="order_date" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-2">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-2">
                        <label>Phone</label>
                        <input type="text" name="customer_phone" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-2">
                        <label>Moderator</label>
                        <select name="moderator" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach(\App\Models\Moderator::all() as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="button" id="applyFilters" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-search"></i> Apply Filters
                </button>

            </form>
        </div>

        <div class="card-body table-responsive">
            {{ $dataTable->table() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <script>
        document.getElementById('applyFilters').addEventListener('click', function () {
            window.LaravelDataTables['orders-table'].ajax.reload();
        });

        // Inject filter values into the DataTable request
        document.addEventListener('DOMContentLoaded', function () {
            window.LaravelDataTables['orders-table'].on('preXhr.dt', function (e, settings, data) {
                const formData = new FormData(document.getElementById('orderFilters'));

                formData.forEach((value, key) => {
                    data[key] = value;
                });
            });
        });
    </script>

    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script> --}}
    <script src="/vendor/datatables/buttons.server-side.js"></script>
@endpush
