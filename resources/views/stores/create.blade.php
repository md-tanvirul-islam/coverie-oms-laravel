@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Store</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('stores.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <x-dropdowns.select-store-type name="type" :selected="old('type')" />
            </div>
            <div class="mb-3">
                <label class="form-check-label">Status</label>
                <x-radio-inputs.app-model-status name="status" :checked="old('status')" />
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">Logo:</label>
                <input type="file" id="logo" class="form-control" name="logo" onchange="previewLogo(event)">
                <img id="logoPreview" class="mt-2 img-thumbnail d-none" style="max-height:120px;">
            </div>
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection


@push('scripts')
    <script>
        function previewLogo(event) {
            const img = document.getElementById('logoPreview');
            img.src = URL.createObjectURL(event.target.files[0]);
            img.classList.remove('d-none');
        }
    </script>
@endpush
