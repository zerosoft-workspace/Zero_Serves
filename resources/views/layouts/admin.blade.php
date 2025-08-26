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

    {{-- Mobile Overlay --}}
    <div class="mobile-overlay" id="mobileOverlay"></div>

    {{-- Yerleşim: Sidebar + İçerik --}}
    <div class="admin-layout">
        <aside class="admin-sidebar" id="adminSidebar">
            {{-- Sidebar Close Button (Mobile) --}}
            <div class="sidebar-close d-md-none">
                <button class="btn btn-link text-light p-2" id="sidebarClose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="sidebar-content">
                {{-- Main Navigation --}}
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>

                {{-- Restaurant Management --}}
                <div class="sidebar-title">Restoran Yönetimi</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.tables.index') }}"
                            class="{{ request()->routeIs('admin.tables.index*') ? 'active' : '' }}">
                            <i class="bi bi-grid-3x3-gap"></i>
                            <span>Masalar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}"
                            class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i>
                            <span>Siparişler</span>
                            @php
                                $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                                $preparingOrders = \App\Models\Order::where('status', 'preparing')->count();
                                $totalActive = $pendingOrders + $preparingOrders;
                            @endphp
                            @if($totalActive > 0)
                                <span class="badge bg-danger ms-1">{{ $totalActive }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            <span>Müşteriler</span>
                        </a>
                    </li>
                </ul>

                {{-- Menu Management --}}
                <div class="sidebar-title">Menü Yönetimi</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                            class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i>
                            <span>Kategoriler</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}"
                            class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                            <i class="bi bi-box"></i>
                            <span>Ürünler</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.stock.index') }}"
                            class="{{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
                            <i class="bi bi-boxes"></i>
                            <span>Stok Yönetimi</span>
                            @php
                                $lowStockCount = \App\Models\Product::whereRaw('stock_quantity <= min_stock_level')->where('is_active', true)->count();
                            @endphp
                            @if($lowStockCount > 0)
                                <span class="badge bg-warning text-dark ms-1">{{ $lowStockCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.ingredients*') ? 'active' : '' }}">
                            <i class="bi bi-egg"></i>
                            <span>Malzemeler</span>
                        </a>
                    </li>
                </ul>

                {{-- Reports & Analytics --}}
                <div class="sidebar-title">Raporlar & Analitik</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.reports.sales*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i>
                            <span>Satış Raporları</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.stock.reports') }}"
                            class="{{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-data"></i>
                            <span>Stok Raporları</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                            <i class="bi bi-pie-chart"></i>
                            <span>Analitikler</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-divider"></div>

                {{-- System Management --}}
                <div class="sidebar-title">Sistem Yönetimi</div>
                <ul class="sidebar-menu">
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <i class="bi bi-people"></i> Kullanıcılar
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                            <i class="bi bi-gear"></i>
                            <span>Ayarlar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="{{ request()->routeIs('admin.backup*') ? 'active' : '' }}">
                            <i class="bi bi-shield-check"></i>
                            <span>Yedekleme</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="admin-main" style="display: block !important; visibility: visible !important;">
            {{-- İsteğe göre container genişliğini burada kontrol et --}}
            <div class="admin-main-inner" style="display: block !important; visibility: visible !important;">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Bootstrap Bundle (Popper dahil) – dropdownların çalışması için şart --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Mobile Menu JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const sidebarClose = document.getElementById('sidebarClose');

            // Mobile menu toggle
            if (mobileMenuToggle) {
                mobileMenuToggle.addEventListener('click', function() {
                    adminSidebar.classList.toggle('show');
                    mobileOverlay.classList.toggle('show');
                    document.body.style.overflow = adminSidebar.classList.contains('show') ? 'hidden' : '';
                });
            }

            // Sidebar close button
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function() {
                    adminSidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // Overlay click to close
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', function() {
                    adminSidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                });
            }

            // Close sidebar when clicking on menu items (mobile)
            const sidebarLinks = adminSidebar.querySelectorAll('.sidebar-menu a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        adminSidebar.classList.remove('show');
                        mobileOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    adminSidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });

            // Escape key to close sidebar
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && adminSidebar.classList.contains('show')) {
                    adminSidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>