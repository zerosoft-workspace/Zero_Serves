{{-- resources/views/admin/partials/navbar.blade.php --}}
<nav class="admin-navbar">
    <div class="navbar-content">
        {{-- Left Side --}}
        <div class="navbar-left">
            {{-- Mobile Toggle --}}
            <button class="navbar-toggle" type="button">
                <i class="bi bi-list"></i>
            </button>

            {{-- Brand/Logo --}}
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
                <i class="bi bi-shop"></i>
                <span>Restoran Admin</span>
            </a>
        </div>

        {{-- Right Side --}}
        <div class="navbar-right">
            {{-- Quick Actions --}}
            <div class="quick-actions d-none d-md-flex">
                <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-light btn-sm me-2" title="Masalar">
                    <i class="bi bi-grid-3x3-gap"></i>
                </a>
                <a href="#" class="btn btn-outline-light btn-sm me-2" title="Yeni Sipariş">
                    <i class="bi bi-plus-circle"></i>
                </a>
            </div>

            {{-- User Dropdown --}}
            <div class="user-dropdown">
                <a href="#" class="user-info">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="user-details d-none d-sm-block">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role" style="font-size: 0.75rem; opacity: 0.8;">
                            {{ ucfirst(auth()->user()->role) }}
                        </div>
                    </div>
                    <i class="bi bi-chevron-down ms-1"></i>
                </a>

                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-person me-2"></i>
                        Profil
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-gear me-2"></i>
                        Ayarlar
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Çıkış Yap
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>