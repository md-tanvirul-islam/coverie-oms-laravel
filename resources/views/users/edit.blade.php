@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit User</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('users.update', $user->id) }}">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name', $user->name) }}"
                    class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="form-control @error('email') is-invalid @enderror" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password (optional update) --}}
            <div class="mb-3">
                <label class="form-label">Password (Leave empty if unchanged)</label>
                <input name="password" type="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @php $roles = $user->roles->count() ? $user->roles->pluck('id')->toArray() : [] @endphp
            {{-- Roles --}}
            <div class="mb-3">
                <label class="form-label">Roles </label>
                <x-dropdowns.select-role class="select2" name="role_ids[]" :selected="$roles" multiple />
                @error('roles')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @php $stores = $user->stores->count() ? $user->stores->pluck('id')->toArray() : [] @endphp
            {{-- Stores --}}
            <div class="mb-3">
                <label class="form-label">Stores </label>
                <x-dropdowns.select-store class="select2" name="store_ids[]" :selected="$stores" multiple />
                @error('store_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Full Data --}}
            <div class="mb-3">
                <label class="form-label">Data Visibility </label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="full_data" id="data-visibility-yes" value="1" @checked(old('full_data', $user->full_data))> 
                    <label class="form-check-label" for="data-visibility-yes">
                        Full Data
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="full_data" id="data-visibility-no" value="0" @checked(old('full_data', $user->full_data))>
                    <label class="form-check-label" for="data-visibility-no">
                        Own Data
                    </label>
                </div>

                @error('full_data')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
