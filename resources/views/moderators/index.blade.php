@extends('layouts.app')

@section('content')
<div class="m-2">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span>Manage Moderators</span>
            <a href="{{ route('moderators.create') }}" class="btn btn-primary btn-sm">Add Moderator</a>
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
