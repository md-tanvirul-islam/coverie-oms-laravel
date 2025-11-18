@extends('layouts.public')

@section('content')
<div class="card auth-card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3 text-center">Forgot Password</h4>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input name="email" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Send Reset Link</button>
        </form>
    </div>
</div>
@endsection
