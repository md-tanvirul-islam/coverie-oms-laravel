@extends('layouts.public')

@section('content')
<div class="card auth-card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3 text-center">Login</h4>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input name="email" type="email" class="form-control" required autofocus>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input name="password" type="password" class="form-control" required>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-between mb-3">
                <div>
                    <input type="checkbox" name="remember"> Remember Me
                </div>
                <a href="{{ route('password.request') }}">Forgot password?</a>
            </div>

            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
@endsection
