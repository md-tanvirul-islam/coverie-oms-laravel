@extends('layouts.app')

@section('content')
<div class="m-2 table-responsive">
    <h3 class="mb-4">Employee Commission Report</h3>

    <div class="card shadow-sm p-4">
        {!! $dataTable->table(['class' => 'table table-bordered table-striped'], true) !!}
    </div>
</div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
