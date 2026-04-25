@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Store</h4>

    <div class="card shadow-sm p-4" x-data="storeVisibility(
        @js(old('user_ids', [])),
        @js(old('full_data_ar', []))
    )" x-init="initSelect2()">
        <form method="POST" action="{{ route('stores.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" value="{{ old('name') }}">
            </div>

            {{-- Type --}}
            <div class="mb-3">
                <label class="form-label">Type</label>
                <x-dropdowns.select-store-type class="select2" name="type" :selected="old('type')" />
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <x-radio-inputs.app-model-status name="status" :checked="old('status', '1')" />
            </div>

            {{-- Authorized Users --}}
            <div class="mb-3">
                <label class="form-label">Authorized Users</label>
                <x-dropdowns.select-user id="user-select" class="select2" name="user_ids[]" :selected="old('user_ids', [])" multiple />
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
                <label class="form-label">Logo</label>
                <input type="file" name="logo" class="form-control">
            </div>

            <button class="btn btn-primary">Create</button>
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

                    // restore old selections
                    const initialData = el.select2('data')
                    this.syncFromSelect2(initialData)

                    el.on('select2:select select2:unselect', () => {
                        this.syncFromSelect2(el.select2('data'))
                    })
                },

                syncFromSelect2(data) {
                    if (!data) return;

                    this.users = data.map(u => ({
                        id: u.id,
                        name: u.text
                    }))

                    // default visibility
                    this.users.forEach(user => {
                        if (this.full_data_ar[user.id] === undefined) {
                            this.full_data_ar[user.id] = '0'
                        }
                    })

                    // cleanup removed users
                    Object.keys(this.full_data_ar).forEach(id => {
                        if (!this.users.find(u => u.id == id)) {
                            delete this.full_data_ar[id]
                        }
                    })
                }
            }
        }
    </script>
@endpush
