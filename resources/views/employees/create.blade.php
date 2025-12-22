@extends('layouts.app')

@section('content')
    <div x-data="{ hasLogin: {{ old('has_login', 0) }} }" class="card shadow-sm p-4">
        <h4 class="mb-3">Add Employee</h4>

        <form method="POST" action="{{ route('employees.store') }}">
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

            {{-- Phone --}}
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Joining Date --}}
            <div class="mb-3">
                <label class="form-label">Joining Date</label>
                <input type="date" name="joining_date" value="{{ old('joining_date') }}"
                    class="form-control @error('joining_date') is-invalid @enderror">
                @error('joining_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input name="address" value="{{ old('address') }}"
                    class="form-control @error('address') is-invalid @enderror">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Code --}}
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror"
                    required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Commission --}}
            <div class="mb-3">
                <label class="form-label">Commission Fee Per Order</label>
                <input type="number" min="0" name="commission_fee_per_order"
                    value="{{ old('commission_fee_per_order') }}"
                    class="form-control @error('commission_fee_per_order') is-invalid @enderror" required>
                @error('commission_fee_per_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Login Access --}}
            <div class="mb-3">
                <label class="form-label">User Login Access</label>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="has_login" value="1"
                        x-model.number="hasLogin">
                    <label class="form-check-label">Yes</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="has_login" value="0"
                        x-model.number="hasLogin">
                    <label class="form-check-label">No</label>
                </div>

                @error('has_login')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Login Credentials --}}
            <div x-show="hasLogin === 1" x-transition x-cloak class="card shadow-sm p-3 mb-3">

                <h5 class="mb-3">Login Credentials</h5>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" :disabled="hasLogin !== 1"
                        class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" :disabled="hasLogin !== 1"
                        class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Roles --}}
                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <x-dropdowns.select-role name="role_ids[]" multiple />
                    @error('role_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Stores --}}
                <div class="mb-3">
                    <label class="form-label">Stores</label>
                    <x-dropdowns.select-store name="store_ids[]" multiple />
                    @error('store_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Data Visibility --}}
                <div class="mb-3">
                    <label class="form-label">Data Visibility</label>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="full_data" value="1" checked>
                        <label class="form-check-label">Full Data</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="full_data" value="0">
                        <label class="form-check-label">Own Data</label>
                    </div>
                </div>
            </div>

            <button class="btn btn-primary">Create</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
