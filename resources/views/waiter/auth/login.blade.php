{{-- resources/views/waiter/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garson Paneli | Giriş</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --nav-bg: #111827;
            --nav-text: #e5e7eb;
            --nav-accent: #0ea5e9;
            --nav-border: rgba(255, 255, 255, .08);
        }

        /* === Üst navbar === */
        .admin-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
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

        .admin-navbar .navbar-brand i {
            color: var(--nav-accent);
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

        /* === Sayfa yerleşimi === */
        body {
            background: #0b1324;
            margin: 0;
        }

        .page-wrap {
            padding-top: 56px;
            /* navbar yüksekliği */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 960px;
            border-radius: 18px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 25px 55px rgba(0, 0, 0, .35);
            background: #fff;
        }

        .hero-pane {
            background: linear-gradient(180deg, #0e1629 0%, #0b1324 100%);
            color: #e5e7eb;
        }
    </style>
</head>

<body>

    {{-- === ÜST NAVBAR === --}}
    <nav class="admin-navbar">
        <div class="navbar-content container-fluid">
            <a href="{{ route('waiter.dashboard') }}" class="navbar-brand">
                <i class="bi bi-egg-fried"></i><span>Garson Paneli</span>
            </a>
            <a href="{{ url('/') }}" class="btn btn-sm btn-ghost">
                <i class="bi bi-house me-1"></i> Ana Sayfa
            </a>
        </div>
    </nav>

    {{-- === ORTA BLOK === --}}
    <div class="page-wrap">
        <div class="auth-card">
            <div class="row g-0">
                {{-- Sol hero --}}
                <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 hero-pane">
                    <div class="text-center">
                        <h1 class="display-6 fw-bold mb-3">Garson Paneli</h1>
                        <p>Masaları, siparişleri ve teslimat sürecini yönetin.</p>
                    </div>
                </div>

                {{-- Sağ form --}}
                <div class="col-lg-6 p-4 p-md-5 bg-white">
                    <div class="mb-4">
                        <span class="h4 mb-0">SoftFood</span>
                        <div class="text-muted">Garson Girişi</div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('waiter.login.post') }}" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                required autofocus>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-2">
                            <label for="password" class="form-label">Şifre</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password';">Göster</button>
                            </div>
                            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-check my-3">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                            <label class="form-check-label" for="remember">Beni hatırla</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">Giriş Yap</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>