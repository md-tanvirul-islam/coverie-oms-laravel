@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add User</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('users.store') }}">
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

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Roles --}}
            <div class="mb-3">
                <label class="form-label">Roles </label>
                <x-dropdowns.select-role name="role_ids[]" multiple />
                @error('roles')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
