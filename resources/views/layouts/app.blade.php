<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SSM OMS') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    @include('layouts.navbar')
    
    <!-- Main Content -->
    <main class="m-2">
        @include('partials.alerts')
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
