<aside class="admin-sidebar">
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
                <a href="#" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Siparişler</span>

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
                <a href="{{ route('admin.stock.reports') }}" class="{{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
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
        <div class="sidebar-divider"></div>

        {{-- Quick Stats --}}
        <div class="sidebar-stats d-none d-lg-block">
            <div class="stats-item">
                <div class="stats-icon bg-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number">{{ $todayCustomers ?? '0' }}</div>
                    <div class="stats-label">Bugünkü Müşteri</div>
                </div>
            </div>
            <div class="stats-item">
                <div class="stats-icon bg-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number">₺{{ number_format($todayRevenue ?? 0) }}</div>
                    <div class="stats-label">Günlük Ciro</div>
                </div>
            </div>
        </div>
    </div>
</aside>

<style>
    /* Sidebar Stats Styles */
    .sidebar-stats {
        padding: 1rem 1.5rem;
        margin-top: 1rem;
    }

    .stats-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }

    .stats-item:not(:last-child) {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
    }

    .sidebar-stats .stats-icon {
        width: 36px;
        height: 36px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .sidebar-stats .stats-number {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        line-height: 1;
    }

    .sidebar-stats .stats-label {
        font-size: 0.75rem;
        color: var(--secondary-color);
        line-height: 1;
    }

    /* Badge styles */
    .badge {
        font-size: 0.6rem;
        padding: 0.25rem 0.5rem;
    }

    /* Sidebar Scrollbar Customization */
    .admin-sidebar {
        overflow-y: scroll;
        /* Kaydırmayı aktif tutar */
        scrollbar-width: none;
        /* Firefox için scrollbar'ı gizler */
    }

    .admin-sidebar::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari ve diğer WebKit tarayıcılar için scrollbar'ı gizler */
    }
</style>