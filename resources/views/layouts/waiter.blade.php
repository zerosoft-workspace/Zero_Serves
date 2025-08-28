<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Garson Paneli')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Ortak header css --}}
    <link rel="stylesheet" href="{{ asset('css/app-header.css') }}">

    {{-- Garson paneli özel CSS --}}
    <link rel="stylesheet" href="{{ asset('css/waiter.css') }}">

    {{-- CSRF Handler --}}
    <script src="{{ asset('js/csrf-handler.js') }}" defer></script>
    {{-- Session Manager --}}
    <script src="{{ asset('js/session-manager.js') }}" defer></script>

    {{-- Sayfaya özel ek stiller için --}}
    @stack('styles')
</head>

<body class="has-app-header">

    {{-- ORTAK HEADER --}}
    @include('layouts.partials.app-header')

    <main class="container-fluid py-3">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Garson paneli özel JS --}}
    <script src="{{ asset('js/waiter.js') }}"></script>

    @stack('scripts')
</body>

</html>