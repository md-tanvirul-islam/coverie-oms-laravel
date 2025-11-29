@extends('layouts.app')
 
@section('content')
    <div class="m-2">
        <div class="card">
            <div class="card-header">Manage Users</div>
            <div class="card-body  table-responsive">
                {{ $dataTable->table() }}
            </div>
        </div>
    </div>
@endsection
 
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush