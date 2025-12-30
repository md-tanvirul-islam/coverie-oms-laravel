@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Add Expense</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Expense Type</label>
                <x-dropdowns.select-expense-type name="expense_type_id" :selected="old('expense_type_id')" />
            </div>
            
            <div class="mb-3">
                <label class="form-label">Store</label>
                <x-dropdowns.select-store name="store_id" :selected="old('store_id')" />

            </div>
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <x-dropdowns.select-employee name="employee_id" :selected="old('employee_id')" />
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

            <button class="btn btn-primary">Create</button>
            <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
