{{-- resources/views/waiter/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Garson Paneli | Giriş</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Ortak CSS (adminAuth.css) --}}
    <link rel="stylesheet" href="{{ asset('css/adminAuth.css') }}">
</head>

<body>
    <div class="auth-wrap container">
        <div class="row g-0 auth-card">
            {{-- Sol kısım / Hero --}}
            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 hero-pane">
                <div class="text-center">
                    <h1 class="display-6 fw-bold mb-3">Garson Paneli</h1>
                    <p class="muted">Masaları, siparişleri ve teslimat sürecini yönetin.</p>
                </div>
            </div>

            {{-- Sağ kısım / Form --}}
            <div class="col-lg-6 bg-white p-4 p-md-5">
                <div class="mb-4">
                    <span class="brand h4 mb-0">SoftFood</span>
                    <div class="text-muted">Garson Girişi</div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('waiter.login.post') }}" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Şifre --}}
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <label for="password" class="form-label">Şifre</label>
                        </div>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-pass" type="button" onclick="
                            const i = document.getElementById('password');
                            i.type = i.type === 'password' ? 'text' : 'password';
                        ">Göster</button>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Beni Hatırla --}}
                    <div class="form-check my-3">
                        <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Beni hatırla</label>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark btn-lg">Giriş Yap</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.login') }}" class="small">Admin girişi</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>