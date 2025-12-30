@extends('layouts.app')

@section('content')
<h4 class="mb-3">Add Expense</h4>

<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('expenses.store') }}">
        @csrf

        <div class="mb-3">
                    <label class="form-label">Expense Type Id</label>
                    <input type="text" name="expense_type_id" class="form-control" value="{{ old('expense_type_id') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Store Id</label>
                    <input type="text" name="store_id" class="form-control" value="{{ old('store_id') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Employee Id</label>
                    <input type="text" name="employee_id" class="form-control" value="{{ old('employee_id') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="amount" class="form-control" value="{{ old('amount') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Expense Date</label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Reference</label>
                    <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                </div>
<div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    </div>
<div class="mb-3">
                    <label class="form-label">Created By</label>
                    <input type="text" name="created_by" class="form-control" value="{{ old('created_by') }}">
                </div>
<div class="mb-3">
                    <label class="form-label">Updated By</label>
                    <input type="text" name="updated_by" class="form-control" value="{{ old('updated_by') }}">
                </div>


        <button class="btn btn-primary">Create</button>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
