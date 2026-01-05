@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Expense</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Expense Type --}}
            <div class="mb-3">
                <label class="form-label">Expense Type</label>
                <x-dropdowns.select-expense-type class="select2" name="expense_type_id" :selected="old('expense_type_id')" required />
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

            {{-- Expense Date --}}
            <div class="mb-3">
                <label class="form-label">Expense Date</label>
                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date') }}" required>
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
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
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
