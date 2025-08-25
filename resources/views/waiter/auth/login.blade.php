<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garson Giriş</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Auth.css') }}">
</head>

<body>
    @include('layouts.partials.auth-navbar')
    @stack('auth_navbar_styles')

    <div class="auth-wrap">
        <div class="row g-0 auth-card mt-4" style="margin-top:72px!important;">
            {{-- Sol / hero (admin ile aynı stil) --}}
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 hero-pane">
                <div class="text-center">
                    <h1 class="display-6 fw-bold mb-3">Garson Paneli</h1>
                    <p class="muted">Masaları, siparişleri ve teslimat sürecini yönetin.</p>
                </div>
            </div>

            {{-- Sağ / form --}}
            <div class="col-lg-6 bg-white p-4 p-md-5">
                <h4 class="mb-3"><i class="bi bi-star-fill text-primary me-2"></i>Garson Girişi</h4>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">@foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('waiter.login.post') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">E‑posta</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Şifre</label>
                        <div class="input-group">
                            <input id="password" type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary toggle-pass" type="button"
                                onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'">Göster</button>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>