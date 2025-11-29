@extends('layouts.app')

@section('content')
<h4 class="mb-3">Add Moderator</h4>

<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('moderators.store') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name"
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror"
                   required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Phone --}}
        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="phone"
                   value="{{ old('phone') }}"
                   class="form-control @error('phone') is-invalid @enderror">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Joining Date --}}
        <div class="mb-3">
            <label class="form-label">Joining Date</label>
            <input name="joining_date" type="date"
                   value="{{ old('joining_date') }}"
                   class="form-control @error('joining_date') is-invalid @enderror">
            @error('joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Address --}}
        <div class="mb-3">
            <label class="form-label">Address</label>
            <input name="address"
                   value="{{ old('address') }}"
                   class="form-control @error('address') is-invalid @enderror">
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Code --}}
        <div class="mb-3">
            <label class="form-label">Code</label>
            <input name="code"
                   value="{{ old('code') }}"
                   class="form-control @error('code') is-invalid @enderror"
                   required>
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

                {{-- Code --}}
        <div class="mb-3">
            <label class="form-label">Commission Fee Per Order</label>
            <input name="commission_fee_per_order" type="number" min="0"
                   value="{{ old('commission_fee_per_order') }}"
                   class="form-control @error('commission_fee_per_order') is-invalid @enderror"
                   required>
            @error('commission_fee_per_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button class="btn btn-primary">Create</button>
        <a href="{{ route('moderators.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
