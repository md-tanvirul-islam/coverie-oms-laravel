@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Income</h4>

    <div class="card shadow-sm p-4" x-data="incomeDocuments(
        @js(
            $income->documents->map(
                fn($d) => [
                    'id' => $d->id,
                    'name' => $d->file_name,
                    'url' => $d->temporarySignedUrl(),
                ],
            ),
        )
    )">

        <form method="POST" action="{{ route('incomes.update', $income->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ================= BASIC INFO ================= --}}
            <div class="mb-3">
                <label class="form-label">Income Type</label>
                <x-dropdowns.select-income-type name="income_type_id" :selected="old('income_type_id', $income->income_type_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store name="store_id" :selected="old('store_id', $income->store_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Employee</label>
                <x-dropdowns.select-employee name="employee_id" :selected="old('employee_id', $income->employee_id)" />
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" value="{{ old('amount', $income->amount) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Income Date</label>
                <input type="date" name="income_date" class="form-control"
                    value="{{ old('income_date', $income->income_date) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control" rows="3">{{ old('note', $income->note) }}</textarea>
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
            <a href="{{ route('incomes.index') }}" class="btn btn-secondary">Back</a>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function incomeDocuments(existingDocs = []) {
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
