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
            
            {{-- Kök elemana class ekleyemiyorsan burada sarmalamak önemli --}}
            @includeWhen(View::exists('admin.partials.sidebar'), 'admin.partials.sidebar') 
        </aside>

        <main class="admin-main">
            {{-- İsteğe göre container genişliğini burada kontrol et --}}
            <div class="admin-main-inner container-fluid py-3">
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