@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Income</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('incomes.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Income Type --}}
            <div class="mb-3">
                <label class="form-label">Income Type</label>
                <x-dropdowns.select-income-type class="select2" name="income_type_id" :selected="old('income_type_id')" required />
            </div>

            {{-- Store --}}
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store class="select2" name="store_id" :selected="old('store_id')" required />
            </div>

            {{-- Employee --}}
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <x-dropdowns.select-employee class="select2" name="employee_id" :selected="old('employee_id')" />
            </div>

            {{-- Amount --}}
            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" min="0" name="amount" class="form-control" value="{{ old('amount') }}" required>
            </div>

            {{-- Income Date --}}
            <div class="mb-3">
                <label class="form-label">Income Date</label>
                <input type="date" name="income_date" class="form-control" value="{{ old('income_date') }}" required>
            </div>

            {{-- Supporting Documents --}}
            <div class="mb-3" x-data="fileRepeater()">

                <label class="form-label">
                    Supporting Documents
                </label>

                <template x-for="(row, index) in rows" :key="row.id">
                    <div class="d-flex align-items-center gap-2 mb-2">

                        <input type="file" :name="`documents[${index}]`" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

                        <button type="button" class="btn btn-outline-danger btn-sm" x-show="rows.length > 1"
                            @click="remove(index)">
                            <i class="bi bi-x"></i>
                        </button>

                    </div>
                </template>

                <button type="button" class="btn btn-outline-primary btn-sm mt-2" @click="add">
                    <i class="bi bi-plus-circle"></i> Add More
                </button>

                <small class="text-muted d-block mt-1">
                    Allowed: PDF, JPG, PNG, DOC, DOCX
                </small>
            </div>

            {{-- Note --}}
            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
            </div>

            {{-- Actions --}}
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('incomes.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection


@push('scripts')
    <script>
        function fileRepeater() {
            return {
                rows: [{
                    id: Date.now()
                }],

                add() {
                    this.rows.push({
                        id: Date.now() + Math.random()
                    })
                },

                remove(index) {
                    if (this.rows.length > 1) {
                        this.rows.splice(index, 1)
                    }
                }
            }
        }
    </script>
@endpush
