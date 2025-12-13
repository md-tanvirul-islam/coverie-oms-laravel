@extends('layouts.app')

@section('content')
<h4 class="mb-3">Add Stores</h4>

<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('stores.store') }}">
        @csrf

        <div class="mb-3">
                    <label class="form-label">User Id</label>
                    <input type="text" name="user_id" class="form-control" value="{{ old('user_id') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Logo</label>
                    <input type="text" name="logo" class="form-control" value="{{ old('logo') }}">
                </div>
<div class="form-check mb-3">
                        <input type="checkbox" name="status" class="form-check-input" value="1" {{ old('status') ? 'checked' : '' }}>
                        <label class="form-check-label">Status</label>
                    </div>
<div class="mb-3">
                    <label class="form-label">Created By</label>
                    <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Updated By</label>
                    <input type="text" name="updated_by" class="form-control" value="{{ old('updated_by') }}">
                </div>


        <button class="btn btn-primary">Create</button>
        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
