@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Team</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('teams.update', $team->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $team->name }}">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="status" class="form-check-input" value="1"
                    {{ $team->status ? 'checked' : '' }}>
                <label class="form-check-label">Status</label>
            </div>
            {{-- <div class="mb-3">
                <label class="form-label">Created By</label>
                <input type="text" name="created_by" class="form-control" value="{{ $team->created_by }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Updated By</label>
                <input type="text" name="updated_by" class="form-control" value="{{ $team->updated_by }}">
            </div> --}}


            <button class="btn btn-primary">Update</button>
            <a href="{{ route('teams.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
