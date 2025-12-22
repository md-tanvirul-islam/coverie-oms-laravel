@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">

        {{-- Card Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Store Details</h5>

            <div class="btn-group">
                <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
                <a href="{{ route('stores.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            {{-- Logo --}}
            @if ($store->logo)
                <div class="text-center mb-4">
                    <img src="{{ $store->logo->temporarySignedUrl() }}"
                        alt="Store Logo"
                        class="img-thumbnail"
                        style="max-height:150px;">
                </div>
            @else
                <p class="text-muted text-center">No logo uploaded</p>
            @endif

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-md-6">
                    <x-detail-box label="Name">
                        {{ $store->name }}
                    </x-detail-box>
                </div>

                {{-- Type --}}
                <div class="col-md-6">
                    <x-detail-box label="Type">
                        {{ \App\Enums\StoreType::options()[$store->type] ?? '—' }}
                    </x-detail-box>
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <x-detail-box label="Status">
                        <span class="badge {{ $store->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $store->status ? 'Active' : 'Inactive' }}
                        </span>
                    </x-detail-box>
                </div>

                {{-- Created By --}}
                <div class="col-md-6">
                    <x-detail-box label="Created By">
                        {{ $store->creator->name ?? 'System' }}
                    </x-detail-box>
                </div>

                {{-- Created At --}}
                <div class="col-md-6">
                    <x-detail-box label="Created At">
                        {{ $store->created_at->format('d M Y, h:i A') }}
                    </x-detail-box>
                </div>

                {{-- Updated By --}}
                <div class="col-md-6">
                    <x-detail-box label="Last Updated By">
                        {{ $store->lastUpdater->name ?? 'System' }}
                    </x-detail-box>
                </div>

                {{-- Updated At --}}
                <div class="col-md-6">
                    <x-detail-box label="Last Updated">
                        {{ $store->updated_at->format('d M Y, h:i A') }}
                    </x-detail-box>
                </div>

            </div>

            {{-- Authorized Users --}}
            <div class="mt-4">
                <h6 class="fw-bold mb-3">Authorized Users</h6>

                @if ($store->users->count())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Data Visibility</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($store->users as $user)
                                    <tr>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>

                                        <td>{{ $user->email }}</td>

                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-secondary me-1">
                                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                </span>
                                            @endforeach
                                        </td>

                                        <td>
                                            @if ($user->pivot->full_data)
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
                    <p class="text-muted">No authorized users assigned.</p>
                @endif
            </div>

        </div>
    </div>
@endsection
