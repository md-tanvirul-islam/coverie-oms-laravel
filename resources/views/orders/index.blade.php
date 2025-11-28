@extends('layouts.app')

@section('content')
<div class="container">

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            Add Order
        </a>

        <a href="{{ route('orders.export') }}" class="btn btn-success">
            Export Excel
        </a>

        <form action="{{ route('orders.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="d-flex gap-2">
                <input type="file" name="file" class="form-control" required>
                <button class="btn btn-warning">Import</button>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">Orders</div>
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
