@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Item</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('items.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store name="store_id" :selected="old('store_id')" required />
            </div>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Is Active</label>
                <x-radio-inputs.app-model-status name="is_active" :checked="old('is_active', '1')" required/>
            </div>
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
