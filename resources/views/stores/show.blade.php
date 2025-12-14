@extends('layouts.app')

@section('content')
    <div class="card shadow-sm">

        {{-- Card Header --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Store Details</h5>

            <div class="btn-group">
                <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </a>
                <a href="{{ route('stores.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">

            {{-- Logo --}}
            @if ($store->logo)
                <div class="text-center mb-4">
                    <img src="{{ $store->logo->temporarySignedUrl() }}" alt="Store Logo" class="img-thumbnail"
                        style="max-height: 150px;">
                </div>
            @else
                <p class="text-muted text-center">No logo uploaded</p>
            @endif

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $store->name }}</strong>
                    </div>
                </div>

                {{-- Type --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Type</small>
                        <strong>
                            {{ \App\Enums\StoreType::options()[$store->type] ?? '—' }}
                        </strong>
                    </div>
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge {{ $store->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $store->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                {{-- Created By --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Created By</small>
                        <strong>{{ $store->creator->name ?? 'System' }}</strong>
                    </div>
                </div>

                {{-- Created At --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Created At</small>
                        <strong>{{ $store->created_at->format('d M Y, h:i A') }}</strong>
                    </div>
                </div>

                {{-- Last Updater By --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Last Updated By</small>
                        <strong>{{ $store->lastUpdater->name ?? 'System' }}</strong>
                    </div>
                </div>

                {{-- Updated At --}}
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted d-block">Last Updated</small>
                        <strong>{{ $store->updated_at->format('d M Y, h:i A') }}</strong>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
