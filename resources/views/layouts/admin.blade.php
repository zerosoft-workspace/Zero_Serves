<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Admin Paneli')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vendor CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Ortak header ve admin yerleşim CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app-header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>

<body class="has-app-header">
    {{-- Üst Navbar (ortak) --}}
    @include('layouts.partials.app-header')

    {{-- Yerleşim: Sidebar + İçerik --}}
    <div class="admin-layout">
        <aside class="admin-sidebar" id="adminSidebar">
            {{-- Kök elemana class ekleyemiyorsan burada sarmalamak önemli --}}
            @includeWhen(View::exists('admin.partials.sidebar'), 'admin.partials.sidebar') </aside>

        <main class="admin-main">
            {{-- İsteğe göre container genişliğini burada kontrol et --}}
            <div class="admin-main-inner container-fluid py-3">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Bootstrap Bundle (Popper dahil) – dropdownların çalışması için şart --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>