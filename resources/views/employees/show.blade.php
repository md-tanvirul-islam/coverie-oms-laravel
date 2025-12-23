@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">

        {{-- Card Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Employee Details</h5>

            <div class="btn-group">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-md-6">
                    <x-detail-box label="Name">
                        {{ $employee->name }}
                    </x-detail-box>
                </div>

                {{-- Employee Code --}}
                <div class="col-md-6">
                    <x-detail-box label="Employee Code">
                        {{ $employee->code }}
                    </x-detail-box>
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <x-detail-box label="Phone">
                        {{ $employee->phone ?? '—' }}
                    </x-detail-box>
                </div>

                {{-- Address --}}
                <div class="col-md-6">
                    <x-detail-box label="Address">
                        {{ $employee->address ?? '—' }}
                    </x-detail-box>
                </div>

                {{-- Joining Date --}}
                <div class="col-md-6">
                    <x-detail-box label="Joining Date">
                        {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : '—' }}
                    </x-detail-box>
                </div>

                {{-- Commission --}}
                <div class="col-md-6">
                    <x-detail-box label="Commission Per Order">
                        {{ number_format($employee->commission_fee_per_order, 2) }}
                    </x-detail-box>
                </div>

                {{-- Login Access --}}
                <div class="col-md-6">
                    <x-detail-box label="Login Access">
                        @if ($employee->user)
                            <span class="badge bg-success">Enabled</span>
                        @else
                            <span class="badge bg-danger">Disabled</span>
                        @endif
                    </x-detail-box>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <x-detail-box label="Email">
                        {{ $employee->user->email ?? '—' }}
                    </x-detail-box>
                </div>

                {{-- Created By --}}
                <div class="col-md-6">
                    <x-detail-box label="Created By">
                        {{ $employee->creator->name ?? 'System' }}
                    </x-detail-box>
                </div>

                {{-- Created At --}}
                <div class="col-md-6">
                    <x-detail-box label="Created At">
                        {{ $employee->created_at->format('d M Y, h:i A') }}
                    </x-detail-box>
                </div>

                {{-- Updated By --}}
                <div class="col-md-6">
                    <x-detail-box label="Last Updated By">
                        {{ $employee->lastUpdater->name ?? 'System' }}
                    </x-detail-box>
                </div>

                {{-- Updated At --}}
                <div class="col-md-6">
                    <x-detail-box label="Last Updated">
                        {{ $employee->updated_at->format('d M Y, h:i A') }}
                    </x-detail-box>
                </div>

            </div>

            {{-- ================= ROLES ================= --}}
            <div class="mt-4">
                <h6 class="fw-bold mb-3">Roles</h6>

                @if ($employee->user && $employee->user->roles->count())
                    @foreach ($employee->user->roles as $role)
                        <span class="badge bg-secondary me-1">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No roles assigned.</p>
                @endif
            </div>

            {{-- ================= STORES ================= --}}
            <div class="mt-4">
                <h6 class="fw-bold mb-3">Assigned Stores</h6>

                @if ($employee->user->stores->count())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Store</th>
                                    <th>Data Visibility</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->user->stores as $store)
                                    <tr>
                                        <td>
                                            <strong>{{ $store->name }}</strong>
                                        </td>
                                        <td>
                                            @if ($store->pivot->full_data)
                                                <span class="badge bg-success">
                                                    Full Data
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    Own Data
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No stores assigned.</p>
                @endif
            </div>

        </div>
    </div>
@endsection
