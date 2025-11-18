@extends('layouts.public')

@section('content')
<div class="card auth-card shadow-sm">
    <div class="card-body">
        <h4 class="mb-3 text-center">Register</h4>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label>Name</label>
                <input name="name" class="form-control" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input name="email" type="email" class="form-control" required>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input name="password_confirmation" type="password" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>
@endsection
