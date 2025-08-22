{{-- resources/views/layouts/waiter.blade.php --}}
<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Garson Paneli')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap 5 (CDN) - projede Vite varsa bunu kaldırıp @vite kullanabilirsin --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .status-badge {
            text-transform: capitalize;
        }

        .toast-fixed {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1056;
            min-width: 260px;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            transition: transform .15s ease;
        }
    </style>

    @stack('head')
</head>

<body class="bg-light">
    {{-- Basit üst şerit --}}
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Garson Paneli</span>
            <div class="d-flex align-items-center gap-2">
                {{-- (İstersen buraya çıkış formu/ikonları ekleyebilirsin) --}}
            </div>
        </div>
    </nav>

    {{-- İçerik --}}
    <main class="container py-3">
        @yield('content')
    </main>

    {{-- Toast / Geri Bildirim --}}
    <div class="toast align-items-center text-bg-success border-0 toast-fixed" id="appToast" role="alert"
        aria-live="assertive" aria-atomic="true" data-bs-delay="2300">
        <div class="d-flex">
            <div class="toast-body" id="appToastBody">İşlem başarılı.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Kapat"></button>
        </div>
    </div>

    {{-- Bootstrap JS (CDN) --}}
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

            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // Waiter: Sipariş durumu değiştir
        async function changeStatus(orderId, to) {
            try {
                const url = "{{ route('waiter.orders.status', ['order' => '__ID__']) }}".replace('__ID__', orderId);
                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ to_status: to })
                });

                const data = await resp.json();

                if (!resp.ok || data.success === false) {
                    showToast(data.message || 'Geçiş başarısız.', true);
                    return;
                }

                showToast('Durum güncellendi.');
                // Basit ve sağlam: yenile
                setTimeout(() => window.location.reload(), 650);
            } catch (e) {
                showToast('Beklenmeyen bir hata oluştu.', true);
            }
        }
    </script>

    @stack('scripts')
</body>

</html>