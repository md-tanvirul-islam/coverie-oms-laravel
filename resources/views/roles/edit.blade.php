@extends('layouts.app')

@section('content')
    <h4 class="mb-3">Edit Role</h4>

    <div class="card shadow-sm p-4">
        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" value="{{ old('name', $role->name) }}"
                    class="form-control @error('name') is-invalid @enderror" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Permissions --}}
            <h5 class="mb-3">Permissions</h5>
            @foreach ($permission_groups as $group => $permissions)
                @php
                    // dd($permissions);
                    $groupId = Str::slug($group);
                    $oldPermissions = old('permissions', $assigned_permissions);
                @endphp

                <div class="border rounded p-3 mb-3 permission-group" data-group="{{ $groupId }}">
                    {{-- Group Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input group-checkbox" id="group-{{ $groupId }}">
                            <label class="form-check-label fw-bold" for="group-{{ $groupId }}">
                                {{ $group }}
                            </label>
                        </div>
                    </div>

                    {{-- Group Permissions --}}
                    <div class="row mt-2">
                        @foreach ($permissions as $permission)
                            @php
                                $label = str($permission->name)->replace('.', ' ')->replace('_', ' ')->title();
                            @endphp
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" id="{{ $permission->name }}"
                                        {{ in_array($permission->name, $oldPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $permission->name }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.permission-group').forEach(group => {
                const groupCheckbox = group.querySelector('.group-checkbox');
                const children = group.querySelectorAll('.permission-checkbox');

                // Initial state (on page load)
                groupCheckbox.checked = [...children].every(c => c.checked);

                // Group checkbox toggles children
                groupCheckbox.addEventListener('change', function() {
                    children.forEach(child => {
                        child.checked = groupCheckbox.checked;
                    });
                });

                // Children toggle group checkbox
                children.forEach(child => {
                    child.addEventListener('change', function() {
                        groupCheckbox.checked = [...children].every(c => c.checked);
                    });
                });
            });

        });
    </script>
@endpush
