@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">

        {{-- Page Header --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Import Courier Paid Invoices (Excel Upload)</h5>
                <div class="btn-group" role="group">
                    <a href="{{ route('courier_paid_invoices.index') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-list"></i> Invoice List
                    </a>
                    <a href="{{ route('courier_paid_invoices.export') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- Instructions --}}
                <div class="alert alert-info">
                    <strong>Instructions:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Only <b>.xlsx</b> or <b>.csv</b> files are allowed.</li>
                        <li>Your Excel file must contain the following columns:</li>
                        <ul>
                            <li>CONSINGMENT_ID</li>
                            <li>CREATED_DATE</li>
                            <li>INVOICE_TYPE ({{ implode(', ', array_values(config('constants.paid_invoice_types'))) }})</li>
                            <li>COLLECTED_AMOUNT</li>
                            <li>RECIPIENT_NAME</li>
                            <li>RECIPIENT_PHONE</li>
                            <li>COLLECTABLE_AMOUNT</li>
                            <li>COD_FEE</li>
                            <li>DELIVERY_FEE</li>
                            <li>FINAL_FEE</li>
                            <li>DISCOUNT</li>
                            <li>ADDITIONAL_CHARGE</li>
                            <li>COMPENSATION_COST</li>
                            <li>PROMO_DISCOUNT</li>
                            <li>PAYOUT</li>
                            <li>MERCHANT_ORDER_ID</li>
                            <li>COURIER_NAME ({{ implode(', ', array_values(config('constants.couriers'))) }})</li>
                        </ul>
                        <li>Each row will be validated during import.</li>
                        <li>If any row has an error, you will see detailed feedback below.</li>
                    </ul>
                </div>

                {{-- Sample Excel --}}
                <div class="mb-3">
                    <a href="{{ asset('sample/excel/courier-paid-invoices-sample.xlsx') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Download Sample Excel
                    </a>
                </div>

                {{-- Import Form --}}
                <form action="{{ route('courier_paid_invoices.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Select Excel File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" required accept=".xlsx,.csv">
                        @error('file')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary" name="courier_name" value="{{App\Enums\CourierName::PATHAO}}">
                        <i class="bi bi-upload"></i> Import Now
                    </button>
                </form>

                {{-- Success Message --}}
                @if(session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
                @endif

                {{-- Error Table (from runtime validation) --}}
                @if(session('import_errors'))
                <div class="alert alert-danger mt-4">
                    <strong>Some rows could not be imported:</strong>
                </div>

                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Row</th>
                            <th>Error(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('import_errors') as $row_errors)
                        <tr>
                            <td>{{ $row_errors['row'] }}</td>
                            <td>
                                @if(is_array($row_errors['errors']))
                                    <ul class="mb-0">
                                        @foreach($row_errors['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $row_errors['errors'] }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
