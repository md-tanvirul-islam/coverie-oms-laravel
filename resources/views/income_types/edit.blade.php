@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Income Type</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('income_types.update', $income_type->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $income_type->name) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $income_type->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Is Active</label>
                <x-radio-inputs.app-model-status name="is_active" :checked="old('is_active', $income_type->is_active)" />
            </div>
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('income_types.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
