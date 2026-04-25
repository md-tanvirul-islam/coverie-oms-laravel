@extends('layouts.app')

@section('content')
    <div class="card shadow-sm p-4" x-data="employeeStoreVisibility(
        @js(old('store_ids', [])),
        @js(old('store_full_data', [])),
        {{ old('has_login', 0) }}
    )" x-init="initStoreSelect2()">

        <h4 class="mb-3">Add Employee</h4>

        <form method="POST" action="{{ route('employees.store') }}">
            @csrf

            {{-- ================= BASIC INFO ================= --}}

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Joining Date</label>
                <input type="date" name="joining_date" value="{{ old('joining_date') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <input name="address" value="{{ old('address') }}" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Employee Code</label>
                <input name="code" value="{{ old('code') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Commission Fee Per Order</label>
                <input type="number" min="0" name="commission_fee_per_order"
                    value="{{ old('commission_fee_per_order') }}" class="form-control" required>
            </div>

            {{-- ================= LOGIN ACCESS ================= --}}

            <div class="mb-3">
                <label class="form-label">User Login Access</label>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="has_login" value="1"
                        x-model.number="hasLogin">
                    <label class="form-check-label">Yes</label>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="radio" name="has_login" value="0"
                        x-model.number="hasLogin">
                    <label class="form-check-label">No</label>
                </div>
            </div>

            {{-- ================= LOGIN DETAILS ================= --}}

            <div x-show="hasLogin === 1" x-transition x-cloak class="card shadow-sm p-3 mb-3">

                <h5 class="mb-3">Login Credentials</h5>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Roles</label>
                    <x-dropdowns.select-role class="select2" name="role_ids[]" multiple />
                </div>

                {{-- ================= STORES ================= --}}

                <div class="mb-3">
                    <label class="form-label">Assigned Stores</label>
                    <x-dropdowns.select-store class="select2" id="store-select" name="store_ids[]" :selected="old('store_ids', [])" multiple />
                </div>

                {{-- ================= PER STORE VISIBILITY ================= --}}

                <template x-if="stores.length">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Store-wise Data Visibility
                        </label>

                        <div class="border rounded p-3 mt-2">
                            <template x-for="store in stores" :key="store.id">
                                <div class="row align-items-center mb-2">
                                    <div class="col-md-5">
                                        <strong x-text="store.name"></strong>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                :name="`store_full_data[${store.id}]`" value="1"
                                                x-model="store_full_data[store.id]">
                                            <label class="form-check-label">Full Data</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                :name="`store_full_data[${store.id}]`" value="0"
                                                x-model="store_full_data[store.id]">
                                            <label class="form-check-label">Own Data</label>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

            </div>

            {{-- ================= ACTIONS ================= --}}

            <button class="btn btn-primary">Create</button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function employeeStoreVisibility(oldStores = [], oldVisibility = {}, hasLogin = 0) {
            return {
                hasLogin: hasLogin,
                stores: [],
                store_full_data: oldVisibility ?? {},

                initStoreSelect2() {
                    const el = $('#store-select')
                    el.select2()

                    // Initial load (old data)
                    this.syncFromSelect2(el.select2('data'))

                    el.on('select2:select select2:unselect', () => {
                        this.syncFromSelect2(el.select2('data'))
                    })
                },

                syncFromSelect2(data) {
                    this.stores = data.map(s => ({
                        id: s.id,
                        name: s.text
                    }))

                    // Default visibility
                    this.stores.forEach(store => {
                        if (this.store_full_data[store.id] === undefined) {
                            this.store_full_data[store.id] = '0'
                        }
                    })

                    // Cleanup removed stores
                    Object.keys(this.store_full_data).forEach(id => {
                        if (!this.stores.find(s => s.id == id)) {
                            delete this.store_full_data[id]
                        }
                    })
                }
            }
        }
    </script>
@endpush
