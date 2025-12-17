@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Team</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('teams.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="status" class="form-check-input" value="1"
                    {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label">Status</label>
            </div>
            {{-- <div class="mb-3">
                <label class="form-label">Created By</label>
                <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Updated By</label>
                <input type="text" name="updated_by" class="form-control" value="{{ old('updated_by') }}">
            </div> --}}
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
