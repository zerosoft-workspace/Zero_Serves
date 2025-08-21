<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Giriş</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet"> {{-- 👈 Harici CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body>
    <div class="auth-wrap container-fluid">
        <div class="row g-0 auth-card">

            <!-- Sol tanıtım paneli -->
            <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center hero-pane p-5">
                <div class="mb-4">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Admin Paneli</span>
                </div>
                <h1 class="brand display-6 mb-3">Restoran Yönetimi</h1>
                <p class="lead muted mb-4">
                    Masalar, ürünler, kategoriler ve sipariş akışını tek yerden yönetin.
                </p>
                <ul class="small mb-0">
                    <li>Rol tabanlı erişim (Admin / Waiter)</li>
                    <li>Hızlı ve mobil uyumlu yönetim ekranları</li>
                    <li>Gerçek zamanlı sipariş durumu</li>
                </ul>
            </div>

            <!-- Sağ form paneli -->
            <div class="col-lg-6 bg-body-tertiary">
                <div class="h-100 d-flex align-items-center justify-content-center p-4 p-md-5">
                    <div class="w-100" style="max-width: 460px;">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center gap-2">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"
                                    class="text-primary">
                                    <path
                                        d="M12 2l2.09 6.26H20l-5 3.64L16.18 18 12 14.9 7.82 18 9 11.9 4 8.26h5.91L12 2z" />
                                </svg>
                                <h2 class="h4 mb-0 brand text-body">Restoran Admin Giriş</h2>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger small">{{ $errors->first() }}</div>
                        @endif

                        <form method="POST" action="{{ route('admin.login.post') }}"
                            class="d-grid gap-3 needs-validation" novalidate>
                            @csrf

                            <!-- E-posta -->
                            <div>
                                <label class="form-label text-dark" for="email">E-posta</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="form-control form-control-lg" placeholder="admin@restoran.local"
                                    autocomplete="email" required>
                                <div class="invalid-feedback">Lütfen geçerli bir e-posta girin.</div>
                            </div>

                            <!-- Şifre -->
                            <div>
                                <label class="form-label text-dark" for="password">Şifre</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-lg" placeholder="••••••••"
                                        autocomplete="current-password" required>

                                    <!-- ikon buton -->
                                    <button type="button" class="btn btn-outline-secondary toggle-pass"
                                        onclick="togglePassword()" tabindex="-1">
                                        <i class="bi bi-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Şifre zorunludur.</div>
                            </div>

                            <!-- Beni hatırla -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="remember"
                                        name="remember">
                                    <label class="form-check-label text-dark" for="remember">Beni hatırla</label>
                                </div>
                            </div>

                            <!-- Giriş Butonu -->
                            <button class="btn btn-lg w-100" style="background-color:#0b1320; color:white;"
                                type="submit">
                                Giriş Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
        // Bootstrap doğrulama
        (() => {
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Şifre göster/gizle
        document.querySelectorAll('.toggle-pass').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = document.querySelector(btn.dataset.target);
                if (!target) return;
                const isPass = target.type === 'password';
                target.type = isPass ? 'text' : 'password';
                btn.textContent = isPass ? 'Gizle' : 'Göster';
                target.focus();
            });
        });
    </script>
</body>

</html>