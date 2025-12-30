@extends('layouts.app')

@section('content')
    <div class="m-2">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Manage Income Types</span>

                <div class="btn-group" role="group">

                    <a href="{{ route('income_types.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Income Type
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
