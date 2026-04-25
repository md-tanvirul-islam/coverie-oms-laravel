@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Expense</h4>

    <div class="card shadow-sm p-4" x-data="expenseDocuments(
        @js(
    $expense->documents->map(
        fn($d) => [
            'id' => $d->id,
            'name' => $d->file_name,
            'url' => $d->temporarySignedUrl(),
        ],
    ),
)
    )">

        <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ================= BASIC INFO ================= --}}
            <div class="mb-3">
                <label class="form-label">Expense Type</label>
                <x-dropdowns.select-expense-type class="select2" name="expense_type_id" :selected="old('expense_type_id', $expense->expense_type_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store class="select2" name="store_id" :selected="old('store_id', $expense->store_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Employee</label>
                <x-dropdowns.select-employee class="select2" name="employee_id" :selected="old('employee_id', $expense->employee_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Expense Date</label>
                <input type="date" name="expense_date" class="form-control"
                    value="{{ old('expense_date', $expense->expense_date) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note', $expense->note) }}</textarea>
            </div>

            {{-- ================= EXISTING DOCUMENTS ================= --}}
            <div class="mb-3" x-show="existing.length">
                <label class="form-label fw-bold">Existing Documents</label>

                <div class="border rounded p-3">
                    <template x-for="doc in existing" :key="doc.id">
                        <div class="d-flex justify-content-between align-items-center mb-2">

                            <a :href="doc.url" target="_blank" class="text-decoration-none">
                                <i class="bi bi-file-earmark-text"></i>
                                <span x-text="doc.name"></span>
                            </a>

                            <button type="button" class="btn btn-sm btn-outline-danger" @click="markForDelete(doc.id)">
                                Remove
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Hidden delete inputs --}}
            <template x-for="id in deleted">
                <input type="hidden" name="delete_documents[]" :value="id">
            </template>

            {{-- ================= NEW DOCUMENT UPLOAD ================= --}}
            <div class="mb-3">
                <label class="form-label fw-bold">Add Supporting Documents</label>

                <template x-for="(row, index) in uploads" :key="row.id">
                    <div class="d-flex gap-2 mb-2">
                        <input type="file" :name="`documents[${index}]`" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

                        <button type="button" class="btn btn-outline-danger btn-sm" x-show="uploads.length > 1"
                            @click="removeUpload(index)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </template>

                <button type="button" class="btn btn-outline-primary btn-sm" @click="addUpload">
                    <i class="bi bi-plus-circle"></i> Add More
                </button>
            </div>

            {{-- ================= ACTIONS ================= --}}
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function expenseDocuments(existingDocs = []) {
            return {
                existing: existingDocs,
                deleted: [],
                uploads: [{
                    id: Date.now()
                }],

                addUpload() {
                    this.uploads.push({
                        id: Date.now() + Math.random()
                    })
                },

                removeUpload(index) {
                    if (this.uploads.length > 1) {
                        this.uploads.splice(index, 1)
                    }
                },

                markForDelete(id) {
                    this.deleted.push(id)
                    this.existing = this.existing.filter(d => d.id !== id)

                    // Ensure at least one input exists
                    if (this.existing.length === 0 && this.uploads.length === 0) {
                        this.uploads.push({
                            id: Date.now()
                        })
                    }
                }
            }
        }
    </script>
@endpush
