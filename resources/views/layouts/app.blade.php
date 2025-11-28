<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>

    @auth
    <!-- Navbar for logged in users -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel App') }}</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                
                <!-- Left Side -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" 
                           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                           Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" 
                           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                           Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('moderators.index') }}" 
                           class="nav-link {{ request()->routeIs('moderators.*') ? 'active' : '' }}">
                           Moderators   
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('orders.index') }}" 
                           class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                           Orders   
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('profile') }}" 
                           class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                           Profile
                        </a>
                    </li> --}}
                </ul>

                <!-- Right Side -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile') }}">My Profile</a>
                            </li>
                            
                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
    @endauth


    <!-- Main Content -->
    <main class="container">
        @include('partials.alerts')
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
