@extends('layouts.app')

@section('content')
<div class="m-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Manage Employees</span>
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">Add Employee</a>
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
