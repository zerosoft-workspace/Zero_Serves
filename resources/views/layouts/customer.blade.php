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

    {{-- CSRF Handler --}}
    <script src="{{ asset('js/csrf-handler.js') }}" defer></script>
    {{-- Session Manager --}}
    <script src="{{ asset('js/session-manager.js') }}" defer></script>

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
                    <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-sm btn-ghost me-2" onclick="showCustomerSection('menu'); return false;">
                        <i class="bi bi-list-ul me-1"></i> Menü
                    </a>
                    <a href="{{ route('customer.table.token', $table->token) }}?view=cart" class="btn btn-sm btn-ghost me-2" onclick="showCustomerSection('cart'); return false;">
                        <i class="bi bi-cart3 me-1"></i> Sepet
                        @if(!empty(session('cart', [])))
                            <span class="badge text-bg-primary ms-1">{{ count(session('cart', [])) }}</span>
                        @endif
                    </a>
                    <a href="{{ route('customer.table.token', $table->token) }}?view=orders" class="btn btn-sm btn-ghost" onclick="showCustomerSection('orders'); return false;">
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

    <script>
        // Global function to handle customer section navigation
        function showCustomerSection(sectionName) {
            // Check if we're on the menu page (has navigation system)
            const navButtons = document.querySelectorAll('.nav-toggle-btn');
            const sections = document.querySelectorAll('.content-section');
            
            if (navButtons.length > 0 && sections.length > 0) {
                // We're on the menu page - use the existing navigation system
                navButtons.forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.getAttribute('data-target') === sectionName) {
                        btn.classList.add('active');
                    }
                });
                
                sections.forEach(section => {
                    if (section.id === sectionName) {
                        section.style.display = 'block';
                        if (sectionName === 'cart' || sectionName === 'orders') {
                            section.className = 'col-12 content-section';
                        } else {
                            section.className = 'col-12 col-lg-8 content-section';
                        }
                    } else {
                        section.style.display = 'none';
                    }
                });
            } else {
                // We're on dashboard or other page - redirect with view parameter
                const currentUrl = window.location.href.split('?')[0];
                window.location.href = currentUrl + '?view=' + sectionName;
            }
        }
    </script>

    @yield('scripts')
</body>

</html>