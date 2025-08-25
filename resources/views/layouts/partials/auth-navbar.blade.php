{{-- Ortak Auth Navbar (guest uyumlu) --}}
<nav class="admin-navbar">
    <div class="navbar-content container-fluid">
        <a href="{{ url('/') }}" class="navbar-brand">
            <i class="bi bi-egg-fried"></i>
            <span>Panel</span>
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('/') }}" class="btn btn-sm btn-ghost">
                <i class="bi bi-house me-1"></i> Ana Sayfa
            </a>
        </div>
    </div>
</nav>

@push('auth_navbar_styles')
    <style>
        :root {
            --nav-bg: #111827;
            --nav-text: #e5e7eb;
            --nav-border: rgba(255, 255, 255, .08);
        }

        .admin-navbar {
            position: fixed;
            inset: 0 0 auto 0;
            z-index: 1040;
            background: var(--nav-bg);
            color: var(--nav-text);
            border-bottom: 1px solid var(--nav-border);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
        }

        .admin-navbar .navbar-content {
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 .75rem;
        }

        .admin-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--nav-text);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-ghost {
            border: 1px solid var(--nav-border);
            color: var(--nav-text);
            background: transparent;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, .06);
            color: #fff;
        }
    </style>
@endpush