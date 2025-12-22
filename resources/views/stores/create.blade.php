@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Store</h4>

    <div class="card shadow-sm p-4" x-data="storeVisibility({{ json_encode(old('user_ids', [])) }})">

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
                <x-dropdowns.select-store-type name="type" :selected="old('type')" />
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status</label>
                <x-radio-inputs.app-model-status name="status" :checked="old('status', '1')" />
            </div>

            {{-- Authorized Users --}}
            <div class="mb-3">
                <label class="form-label">Authorized Users</label>

                <x-dropdowns.select-user name="user_ids[]" multiple
                    x-on:change="syncUsers($event.target.selectedOptions)" />
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
                                        <input class="form-check-input" type="radio" :name="`full_data[${user.id}]`"
                                            value="1" x-model="full_data[user.id]">
                                        <label class="form-check-label">Full Data</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" :name="`full_data[${user.id}]`"
                                            value="0" x-model="full_data[user.id]">
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
        function storeVisibility(oldUsers = []) {
            return {
                users: [],
                visibility: {},

                syncUsers(options) {
                    console.log(options);
                    this.users = Array.from(options).map(opt => ({
                        id: opt.value,
                        name: opt.text
                    }))

                    this.users.forEach(user => {
                        if (!this.visibility[user.id]) {
                            this.visibility[user.id] = '0'
                        }
                    })
                }
            }
        }
    </script>
@endpush
