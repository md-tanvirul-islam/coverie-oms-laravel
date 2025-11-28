@extends('layouts.app')

@section('content')
<h4 class="mb-3">Edit Order</h4>

<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('orders.update', $order->id) }}">
        @csrf
        @method('PUT')

        {{-- Invoice ID --}}
        <div class="mb-3">
            <label class="form-label">Invoice ID</label>
            <input name="invoice_id" 
                   value="{{ old('invoice_id', $order->invoice_id) }}" 
                   class="form-control @error('invoice_id') is-invalid @enderror" 
                   required>
            @error('invoice_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Order Date --}}
        <div class="mb-3">
            <label class="form-label">Order Date</label>
            <input type="date" name="order_date" 
                   value="{{ old('order_date', $order->order_date) }}" 
                   class="form-control @error('order_date') is-invalid @enderror" 
                   required>
            @error('order_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Customer Name --}}
        <div class="mb-3">
            <label class="form-label">Customer Name</label>
            <input name="customer_name" 
                   value="{{ old('customer_name', $order->customer_name) }}" 
                   class="form-control @error('customer_name') is-invalid @enderror" 
                   required>
            @error('customer_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Customer Phone --}}
        <div class="mb-3">
            <label class="form-label">Customer Phone</label>
            <input name="customer_phone" 
                   value="{{ old('customer_phone', $order->customer_phone) }}" 
                   class="form-control @error('customer_phone') is-invalid @enderror" 
                   required>
            @error('customer_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Customer Address --}}
        <div class="mb-3">
            <label class="form-label">Customer Address</label>
            <textarea name="customer_address" 
                      class="form-control @error('customer_address') is-invalid @enderror"
                      rows="2">{{ old('customer_address', $order->customer_address) }}</textarea>
            @error('customer_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Total Cost --}}
        <div class="mb-3">
            <label class="form-label">Total Cost</label>
            <input type="number" step="0.01" name="total_cost" 
                   value="{{ old('total_cost', $order->total_cost) }}" 
                   class="form-control @error('total_cost') is-invalid @enderror" 
                   required>
            @error('total_cost')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Phone Model --}}
        <div class="mb-3">
            <label class="form-label">Phone Model</label>
            <input name="phone_model" 
                   value="{{ old('phone_model', $order->phone_model) }}" 
                   class="form-control @error('phone_model') is-invalid @enderror" 
                   required>
            @error('phone_model')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Order Taken By (Moderator) --}}
        <div class="mb-3">
            <label class="form-label">Order Taken By</label>
            <select name="moderator_id" 
                    class="form-control @error('moderator_id') is-invalid @enderror" 
                    required>
                <option value="">Select Moderator</option>
                @foreach($moderators as $id => $name)
                    <option value="{{ $id }}" 
                        {{ old('moderator_id', $order->moderator_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('moderator_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
