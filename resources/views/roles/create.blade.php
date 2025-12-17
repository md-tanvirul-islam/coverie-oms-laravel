@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Role</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"
                    required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Create</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
