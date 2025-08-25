<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- App Header CSS (ortak stil) -->
    <link rel="stylesheet" href="{{ asset('css/app-header.css') }}">

    <!-- Customer CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">

    @stack('styles')
</head>

<body class="has-app-header">
    <!-- App Header (diğer panellerle uyumlu) -->
    <nav class="app-navbar">
        <div class="app-nav-inner container-fluid">
            <a href="#" class="app-brand">
                <i class="bi bi-shop"></i>
                <span>{{ $table->name ?? 'Masa' }}</span>
            </a>

            <div class="app-actions">
                <!-- Desktop Navigation -->
                <div class="d-none d-lg-flex align-items-center me-3">
                    <a href="{{ route('customer.table.token', $table->token) }}" class="btn btn-sm btn-ghost me-2">
                        <i class="bi bi-house me-1"></i> Dashboard
                    </a>
                    <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-sm btn-ghost me-2">
                        <i class="bi bi-list-ul me-1"></i> Menü
                    </a>
                    <a href="{{ route('customer.table.token', $table->token) }}?view=cart" class="btn btn-sm btn-ghost me-2">
                        <i class="bi bi-cart3 me-1"></i> Sepet
                        @if(!empty(session('cart', [])))
                            <span class="badge text-bg-primary ms-1">{{ count(session('cart', [])) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('customer.table.token', $table->token) }}?view=orders" class="btn btn-sm btn-ghost">
                        <i class="bi bi-clock-history me-1"></i> Siparişler
                    </a>
                </div>

                <!-- Landing Page Button -->
                <a href="{{ route('landing') }}" class="btn btn-sm btn-ghost me-2">
                    <i class="bi bi-arrow-left me-1"></i>
                    <span class="d-none d-sm-inline">Ana Sayfa</span>
                </a>

                <!-- Mobile Cart Badge -->
                <div class="d-lg-none">
                    @if(!empty(session('cart', [])))
                        <span class="badge bg-primary rounded-pill">{{ count(session('cart', [])) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid mt-5 pt-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>