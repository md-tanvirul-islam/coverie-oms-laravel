@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Store</h4>

    @php
        $storeUserIds = $store->users->pluck('id')->toArray();

        // Assuming pivot column: full_data (boolean / 0|1)
        $storeFullData = $store->users->mapWithKeys(fn($u) => [$u->id => (string) $u->pivot->full_data])->toArray();
    @endphp

    <div class="card shadow-sm p-4" x-data="storeVisibility(
        @js(old('user_ids', $storeUserIds)),
        @js(old('full_data_ar', $storeFullData))
    )" x-init="initSelect2()">
        <form method="POST" action="{{ route('stores.update', $store->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" value="{{ old('name', $store->name) }}">
            </div>

            {{-- Type --}}
            <div class="mb-3">
                <label class="form-label">Type</label>
                <x-dropdowns.select-store-type class="select2" name="type" :selected="old('type', $store->type)" />
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <x-radio-inputs.app-model-status name="status" :checked="old('status', $store->status)" />
            </div>

            {{-- Authorized Users --}}
            <div class="mb-3">
                <label class="form-label">Authorized Users</label>
                <x-dropdowns.select-user class="select2" id="user-select" name="user_ids[]" :selected="old('user_ids', $storeUserIds)" multiple />
            </div>

            {{-- Per User Data Visibility --}}
            <template x-if="users.length">
                <div class="mb-3">
                    <label class="form-label fw-bold">Data Visibility Per User</label>

                    <div class="border rounded p-3 mt-2">
                        <template x-for="user in users" :key="user.id">
                            <div class="row align-items-center mb-2">
                                <div class="col-md-4">
                                    <strong x-text="user.name"></strong>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" :name="`full_data_ar[${user.id}]`"
                                            value="1" x-model="full_data_ar[user.id]">
                                        <label class="form-check-label">Full Data</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" :name="`full_data_ar[${user.id}]`"
                                            value="0" x-model="full_data_ar[user.id]">
                                        <label class="form-check-label">Own Data</label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Logo --}}
            <div class="mb-3">
                <label class="form-label">
                    Logo <small class="text-muted">(leave empty to keep existing)</small>
                </label>
                <input type="file" name="logo" class="form-control" onchange="previewLogo(event)">
                <img id="logoPreview" class="mt-2 img-thumbnail d-none" style="max-height:120px;">
            </div>

            @if ($store->logo)
                <div class="mb-3">
                    <label class="form-label d-block">Current Logo</label>
                    <img src="{{ $store->logo->temporarySignedUrl() }}" class="img-thumbnail" style="max-height:120px;">
                </div>
            @endif

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('stores.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function storeVisibility(oldUsers = [], oldFullData = {}) {
            return {
                users: [],
                full_data_ar: oldFullData ?? {},

                initSelect2() {
                    const el = $('#user-select')
                    el.select2()

                    // Sync initial data
                    this.syncFromSelect2(el.select2('data'))

                    el.on('select2:select select2:unselect', () => {
                        this.syncFromSelect2(el.select2('data'))
                    })
                },

                syncFromSelect2(data) {
                    if (!data) return

                    this.users = data.map(u => ({
                        id: u.id,
                        name: u.text
                    }))

                    // Default visibility
                    this.users.forEach(user => {
                        if (this.full_data_ar[user.id] === undefined) {
                            this.full_data_ar[user.id] = '0'
                        }
                    })

                    // Remove deleted users
                    Object.keys(this.full_data_ar).forEach(id => {
                        if (!this.users.find(u => u.id == id)) {
                            delete this.full_data_ar[id]
                        }
                    })
                }
            }
        }

        function previewLogo(event) {
            const img = document.getElementById('logoPreview')
            img.src = URL.createObjectURL(event.target.files[0])
            img.classList.remove('d-none')
        }
    </script>
@endpush
