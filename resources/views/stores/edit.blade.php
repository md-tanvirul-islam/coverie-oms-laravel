@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Store</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('stores.update', $store->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $store->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <x-dropdowns.select-store-type name="type" :selected="old('type', $store->type)" />
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-check-label">Status</label>
                <x-radio-inputs.app-model-status name="status" :checked="old('status', $store->status)" />
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @php $users_ids = $store->users->count() ? $store->users->pluck('id')->toArray() : [] @endphp
            <div class="mb-3">
                <label class="form-check-label">Authorized Users</label>
                <x-dropdowns.select-user name="user_ids[]" :selected="old('user_ids', $users_ids)" multiple />
                @error('user_ids')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Full Data --}}
            <div class="mb-3">
                <label class="form-label">Data Visibility </label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="full_data" id="data-visibility-yes" value="1"
                        @checked(old('full_data'))>
                    <label class="form-check-label" for="data-visibility-yes">
                        Full Data
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="full_data" id="data-visibility-no" value="0"
                        @checked(old('full_data') == '0')>
                    <label class="form-check-label" for="data-visibility-no">
                        Own Data
                    </label>
                </div>

                @error('full_data')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="logo" class="form-label">
                    Logo <small class="text-muted">(leave empty to keep existing)</small>
                </label>
                <input type="file" id="logo" class="form-control" name="logo" onchange="previewLogo(event)">
                @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <img id="logoPreview" class="mt-2 img-thumbnail d-none" style="max-height:120px;">
            </div>
            <div class="mb-3">
                @if ($store->logo)
                    <div class="mb-3">
                        <label class="form-label d-block">Current Logo</label>
                        <img src="{{ $store->logo->temporarySignedUrl() }}" alt="Store Logo" class="img-thumbnail"
                            style="max-height: 120px;">
                    </div>
                @endif
            </div>

            <button class="btn btn-primary">Update</button>
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
