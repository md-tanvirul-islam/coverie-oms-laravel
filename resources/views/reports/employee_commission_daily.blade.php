@extends('layouts.app')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-10">

        {{-- Page Header --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Employee Commission Report(Daily)</h4>

                <a href="{{ route('courier_paid_invoices.index') }}" class="btn btn-light btn-sm">
                    <i class="bi bi-list"></i> Back to Invoice List
                </a>
            </div>

            <div class="card-body">

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('reports.employee_commission.daily') }}" class="mb-4">
                    <div class="row align-items-end">

                        <div class="col-md-3">
                            <label for="from" class="form-label">From Date</label>
                            <input type="date"
                                   name="from"
                                   id="from"
                                   value="{{ request('from') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="to" class="form-label">To Date</label>
                            <input type="date"
                                   name="to"
                                   id="to"
                                   value="{{ request('to') }}"
                                   class="form-control">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>

                    </div>
                </form>

                {{-- Report Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Employee Name</th>
                                <th>Code</th>
                                <th>Total Quantity</th>
                                <th>Commission (৳)</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>{{ $report->order_date }}</td>
                                <td>{{ $report->name }}</td>
                                <td>{{ $report->code }}</td>
                                <td>{{ $report->total_quantity }}</td>
                                <td>{{ number_format($report->total_commission, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No records found for the selected dates.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $reports->appends(request()->query())->links() }}
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
