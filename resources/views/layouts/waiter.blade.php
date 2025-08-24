{{-- resources/views/layouts/waiter.blade.php --}}
<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Garson Paneli')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap 5 + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --nav-bg: #111827;
            /* koyu lacivert/siyah */
            --nav-text: #e5e7eb;
            /* açık gri */
            --nav-accent: #0ea5e9;
            /* mavi vurgu */
            --nav-border: rgba(255, 255, 255, .08);
        }

        /* ==== Admin navbar görünümüne uyumlu üst bar ==== */
        .admin-navbar {
            position: sticky;
            top: 0;
            z-index: 1040;
            width: 100%;
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

        .admin-navbar .navbar-left {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .admin-navbar .navbar-toggle {
            border: none;
            background: transparent;
            color: var(--nav-text);
            font-size: 1.35rem;
            line-height: 1;
        }

        .admin-navbar .navbar-brand {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--nav-text);
            text-decoration: none;
            font-weight: 600;
        }

        .admin-navbar .navbar-brand i {
            color: var(--nav-accent);
        }

        .admin-navbar .navbar-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .admin-navbar .btn-ghost {
            border: 1px solid var(--nav-border);
            color: var(--nav-text);
            background: transparent;
        }

        .admin-navbar .btn-ghost:hover {
            background: rgba(255, 255, 255, .06);
            color: #fff;
        }

        .user-dropdown .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #22c55e);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
        }

        .user-dropdown .dropdown-menu {
            min-width: 220px;
            border-radius: .5rem;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, .08);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
        }

        /* ==== Sayfa gövdesi ==== */
        body {
            background: #0f172a;
        }

        /* admin ile yakın koyu arka plan */
        .page {
            padding: 16px;
        }

        @media (min-width: 768px) {
            .page {
                padding: 24px;
            }
        }

        /* Kart/rozet ufak uyumlar */
        .card {
            border: 0;
            border-radius: .8rem;
        }

        .card-hover {
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, .25);
        }

        .toast-fixed {
            position: fixed;
            right: 12px;
            bottom: 12px;
            z-index: 1080;
        }
    </style>
</head>

<body>
    {{-- === ÜST NAVBAR (Admin görünümü ile eş) === --}}
    <nav class="admin-navbar">
        <div class="navbar-content container-fluid">
            <div class="navbar-left">
                {{-- (İleride yan menü eklenirse) mobil toggle --}}
                <button class="navbar-toggle d-md-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#waiterOffcanvas" aria-controls="waiterOffcanvas">
                    <i class="bi bi-list"></i>
                </button>

                {{-- Marka / Logo --}}
                <a href="{{ route('waiter.dashboard') }}" class="navbar-brand">
                    <i class="bi bi-egg-fried"></i>
                    <span>Garson Paneli</span>
                </a>
            </div>

            <div class="navbar-actions">
                {{-- Sayfa özel hızlı aksiyon alanı --}}
                @yield('header_actions')

                {{-- Bildirim, hızlı buton örnekleri (opsiyonel) --}}
                {{-- <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
                    <i class="bi bi-speedometer2 me-1"></i> Panel
                </a> --}}

                {{-- Kullanıcı menüsü --}}
                {{-- ...navbar-actions içinde, kullanıcı menüsü alanı --}}
                @auth
                    <div class="dropdown user-dropdown">
                        <a class="d-flex align-items-center text-decoration-none" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar me-2">{{ strtoupper(auth()->user()->name[0] ?? 'G') }}</span>
                            <span class="d-none d-sm-inline text-white-50">{{ auth()->user()->name ?? 'Garson' }}</span>
                            <i class="bi bi-chevron-down ms-2 text-white-50"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('waiter.dashboard') }}">
                                <i class="bi bi-grid me-2"></i> Masalar
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('waiter.logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('waiter.login') }}" class="btn btn-sm btn-ghost">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Giriş
                    </a>
                @endauth

            </div>
        </div>
    </nav>

    {{-- (İsteğe bağlı) mobil offcanvas menü --}}
    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="waiterOffcanvas"
        aria-labelledby="waiterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="waiterOffcanvasLabel">Garson Menüsü</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Kapat"></button>
        </div>
        <div class="offcanvas-body">
            <div class="list-group list-group-flush">
                <a href="{{ route('waiter.dashboard') }}"
                    class="list-group-item list-group-item-action text-white bg-transparent">
                    <i class="bi bi-grid me-2"></i> Masalar
                </a>
                {{-- Gerekirse ek bağlantılar --}}
            </div>
        </div>
    </div>

    <main class="page container-fluid">
        @yield('content')
    </main>

    {{-- Toast --}}
    <div class="toast align-items-center text-bg-success border-0 toast-fixed" id="appToast" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="2300">
        <div class="d-flex">
            <div class="toast-body" id="appToastBody">İşlem başarılı.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Kapat"></button>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Ortak JS --}}
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showToast(message = 'İşlem başarılı.', isError = false) {
            const toastEl = document.getElementById('appToast');
            const bodyEl = document.getElementById('appToastBody');
            if (!toastEl || !bodyEl) return;

            bodyEl.textContent = message;
            toastEl.classList.toggle('text-bg-success', !isError);
            toastEl.classList.toggle('text-bg-danger', isError);

            const t = bootstrap.Toast.getOrCreateInstance(toastEl);
            t.show();
        }

        // (Örnek) AJAX durum güncelleme fonksiyonu kaldıysa buraya koyabilirsiniz
        async function changeStatus(orderId, newStatus) {
            try {
                const res = await fetch(`/waiter/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                showToast('Durum güncellendi.');
                setTimeout(() => window.location.reload(), 650);
            } catch (e) { showToast('Beklenmeyen bir hata oluştu.', true); }
        }
    </script>

    @stack('scripts')
</body>

</html>