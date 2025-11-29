@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Page Header --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white d-flex justify-content-between">
                    <h5 class="mb-0">Import Orders (Excel Upload)</h5>
                    <div class="btn-group" role="group">

                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-list"></i> Order List
                        </a>
                        <a href="{{ route('orders.export') }}" class="btn btn-success btn-sm">
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
                                <li>INVOICE_ID</li>
                                <li>ORDER_DATE</li>
                                <li>CUSTOMER_NAME</li>
                                <li>CUSTOMER_PHONE</li>
                                <li>CUSTOMER_ADDRESS</li>
                                <li>TOTAL_COST</li>
                                <li>PHONE_MODEL</li>
                                <li>ORDER_TAKEN_BY (Moderator Code)</li>
                            </ul>
                            <li>Each row will be validated during import.</li>
                            <li>If any row has an error, you will see detailed feedback.</li>
                        </ul>
                    </div>

                    {{-- Sample Excel --}}
                    <div class="mb-3">
                        <a href="{{ asset('sample/excel/orders-sample.xlsx') }}" class="btn btn-success">
                            Download Sample Excel
                        </a>
                    </div>

                    {{-- Import Form --}}
                    <form action="{{ route('orders.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Select Excel File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" required accept=".xlsx,.csv">
                            @error('file')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Import Now
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

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Row</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('import_errors') as $row_errors)
                                <tr>
                                    <td>{{ $row_errors['row'] }}</td>
                                    <td>{{ $row_errors['errors'] }}</td>
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