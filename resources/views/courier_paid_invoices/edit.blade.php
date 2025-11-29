@extends('layouts.app')

@section('content')
<h4 class="mb-3">Edit Courier Paid Invoice</h4>
<div class="card shadow-sm p-4">
    <form method="POST" action="{{ route('courier_paid_invoices.update',$courier_paid_invoice->id) }}">
        @csrf
        @method('PUT')

        {{-- Courier Name --}}
        <div class="mb-3">
            <label class="form-label">Courier Name</label>
            <select name="courier_name" id="courier_name" class="form-control">
                @foreach(config('constants.couriers') as $code => $name)
                    <option value="{{ $name }}" {{ old('courier_name',$courier_paid_invoice->courier_name) == $name ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('courier_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Consignment ID --}}
        <div class="mb-3">
            <label class="form-label">Consignment ID</label>
            <input name="consignment_id" 
                   value="{{ old('consignment_id',$courier_paid_invoice->consignment_id) }}" 
                   class="form-control @error('consignment_id') is-invalid @enderror" 
                   required>
            @error('consignment_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Created Date --}}
        <div class="mb-3">
            <label class="form-label">Created Date</label>
            <input type="datetime-local" name="created_date" 
                   value="{{ old('created_date',$courier_paid_invoice->created_date ?$courier_paid_invoice->created_date->format('Y-m-d\TH:i') : '') }}" 
                   class="form-control @error('created_date') is-invalid @enderror" 
                   required>
            @error('created_date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Invoice Type --}}
        <div class="mb-3">
            <label class="form-label">Invoice Type</label>
            <select name="invoice_type" 
                    class="form-control @error('invoice_type') is-invalid @enderror" 
                    required>
                <option value="">Select Type</option>
                @foreach(config('constants.paid_invoice_types') as $code => $type)
                    <option value="{{ $type }}" {{ old('invoice_type',$courier_paid_invoice->invoice_type) == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            @error('invoice_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Collected Amount --}}
        <div class="mb-3">
            <label class="form-label">Collected Amount</label>
            <input type="number" step="0.01" name="collected_amount" 
                   value="{{ old('collected_amount',$courier_paid_invoice->collected_amount) }}" 
                   class="form-control @error('collected_amount') is-invalid @enderror">
            @error('collected_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Recipient Name --}}
        <div class="mb-3">
            <label class="form-label">Recipient Name</label>
            <input name="recipient_name" 
                   value="{{ old('recipient_name',$courier_paid_invoice->recipient_name) }}" 
                   class="form-control @error('recipient_name') is-invalid @enderror">
            @error('recipient_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Recipient Phone --}}
        <div class="mb-3">
            <label class="form-label">Recipient Phone</label>
            <input name="recipient_phone" 
                   value="{{ old('recipient_phone',$courier_paid_invoice->recipient_phone) }}" 
                   class="form-control @error('recipient_phone') is-invalid @enderror">
            @error('recipient_phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Collectable Amount --}}
        <div class="mb-3">
            <label class="form-label">Collectable Amount</label>
            <input type="number" step="0.01" name="collectable_amount" 
                   value="{{ old('collectable_amount',$courier_paid_invoice->collectable_amount) }}" 
                   class="form-control @error('collectable_amount') is-invalid @enderror">
            @error('collectable_amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- COD Fee --}}
        <div class="mb-3">
            <label class="form-label">COD Fee</label>
            <input type="number" step="0.01" name="cod_fee" 
                   value="{{ old('cod_fee',$courier_paid_invoice->cod_fee) }}" 
                   class="form-control @error('cod_fee') is-invalid @enderror">
            @error('cod_fee')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Delivery Fee --}}
        <div class="mb-3">
            <label class="form-label">Delivery Fee</label>
            <input type="number" step="0.01" name="delivery_fee" 
                   value="{{ old('delivery_fee',$courier_paid_invoice->delivery_fee) }}" 
                   class="form-control @error('delivery_fee') is-invalid @enderror">
            @error('delivery_fee')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Final Fee --}}
        <div class="mb-3">
            <label class="form-label">Final Fee</label>
            <input type="number" step="0.01" name="final_fee" 
                   value="{{ old('final_fee',$courier_paid_invoice->final_fee) }}" 
                   class="form-control @error('final_fee') is-invalid @enderror">
            @error('final_fee')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Discount --}}
        <div class="mb-3">
            <label class="form-label">Discount</label>
            <input type="number" step="0.01" name="discount" 
                   value="{{ old('discount',$courier_paid_invoice->discount) }}" 
                   class="form-control @error('discount') is-invalid @enderror">
            @error('discount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Additional Charge --}}
        <div class="mb-3">
            <label class="form-label">Additional Charge</label>
            <input type="number" step="0.01" name="additional_charge" 
                   value="{{ old('additional_charge',$courier_paid_invoice->additional_charge) }}" 
                   class="form-control @error('additional_charge') is-invalid @enderror">
            @error('additional_charge')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Compensation Cost --}}
        <div class="mb-3">
            <label class="form-label">Compensation Cost</label>
            <input type="number" step="0.01" name="compensation_cost" 
                   value="{{ old('compensation_cost',$courier_paid_invoice->compensation_cost) }}" 
                   class="form-control @error('compensation_cost') is-invalid @enderror">
            @error('compensation_cost')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Promo Discount --}}
        <div class="mb-3">
            <label class="form-label">Promo Discount</label>
            <input type="number" step="0.01" name="promo_discount" 
                   value="{{ old('promo_discount',$courier_paid_invoice->promo_discount) }}" 
                   class="form-control @error('promo_discount') is-invalid @enderror">
            @error('promo_discount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Payout --}}
        <div class="mb-3">
            <label class="form-label">Payout</label>
            <input type="number" step="0.01" name="payout" 
                   value="{{ old('payout',$courier_paid_invoice->payout) }}" 
                   class="form-control @error('payout') is-invalid @enderror">
            @error('payout')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Merchant Order ID --}}
        <div class="mb-3">
            <label class="form-label">Merchant Order ID</label>
            <input name="merchant_order_id" 
                   value="{{ old('merchant_order_id',$courier_paid_invoice->merchant_order_id) }}" 
                   class="form-control @error('merchant_order_id') is-invalid @enderror">
            @error('merchant_order_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('courier_paid_invoices.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
