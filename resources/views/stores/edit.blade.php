@extends('layouts.app')

@section('content')
<h4 class="mb-3">Edit Stores</h4>

<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('stores.update', $store->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
                    <label class="form-label">User Id</label>
                    <input type="text" name="user_id" class="form-control" value="{{ $store->user_id }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $store->name }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ $store->slug }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Type</label>
                    <input type="text" name="type" class="form-control" value="{{ $store->type }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Logo</label>
                    <input type="text" name="logo" class="form-control" value="{{ $store->logo }}">
                </div>
<div class="form-check mb-3">
                        <input type="checkbox" name="status" class="form-check-input" value="1" {{ $store->status ? 'checked' : '' }}>
                        <label class="form-check-label">Status</label>
                    </div>
<div class="mb-3">
                    <label class="form-label">Created By</label>
                    <input type="text" name="created_by" class="form-control" value="{{ $store->created_by }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Updated By</label>
                    <input type="text" name="updated_by" class="form-control" value="{{ $store->updated_by }}">
                </div>


        <button class="btn btn-primary">Update</button>
        <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
