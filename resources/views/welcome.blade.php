@extends('layouts.public')

@section('content')
    <div class="container">
        <div class="card welcome-card shadow-sm">
            <div class="card-body text-center py-5">

                <h1 class="fw-bold mb-3">Welcome 👋</h1>
                <p class="text-muted mb-4">
                    This is a {{env("APP_NAME")}} application.
                </p>

                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary px-4">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary px-4 me-2">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary px-4">
                        Register
                    </a>
                @endauth

            </div>
        </div>
    </div>
@endsection