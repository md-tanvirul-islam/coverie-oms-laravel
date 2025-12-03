<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Coverie OMS' }}</title>

    <style>
        body { background: #f8f9fa; }
        .auth-card { max-width: 420px; margin:auto; margin-top:70px; }
    </style>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">Coverie OMS</a>

        @auth
        <div class="ms-auto">
            <span class="me-3">{{ auth()->user()->name }}</span>

            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>
        @else
        <div class="ms-auto">
            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Login</a>
            {{-- <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Register</a> --}}
        </div>
        @endauth
    </div>
</nav>

<div class="container py-4">
    @yield('content')
</div>

</body>
</html>
