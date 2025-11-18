@extends('layouts.public')

@section('content')
<div class="card auth-card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3 text-center">Reset Password</h4>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label>Email</label>
                <input name="email" class="form-control" value="{{ old('email', $request->email) }}" required>
            </div>

            <div class="mb-3">
                <label>New Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input name="password_confirmation" type="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</div>
@endsection
