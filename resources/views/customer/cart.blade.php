{{-- resources/views/customer/cart.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Sepetim | SoftFood</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Global --}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    {{-- Sayfaya özgü CSS --}}
    <link rel="stylesheet" href="{{ asset('css/qr_customer.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-inter page-cart">
    {{-- Header --}}
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">SoftFood</a>
            <ul class="nav-menu" id="navMenu">
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'dashboard']) }}">Menü</a>
                </li>
                <li><a href="{{ route('customer.cart.view', ['token' => $table->token]) }}">Sepetim</a></li>
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'orders']) }}">Siparişlerim</a>
                </li>
            </ul>
            <div class="flex items-center gap-3 text-sm text-gray-300">
                <i class="fas fa-qrcode"></i>
                {{ $table->name ?? 'Dijital Menü' }}
            </div>
            <button class="mobile-menu-toggle" id="mobileToggle" aria-label="Menüyü Aç/Kapat">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    {{-- Page --}}
    <main class="max-w-4xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="font-playfair text-3xl font-bold">Sepetim</h1>
                <p class="text-gray-400">Ürünlerinizi inceleyebilir, adetleri değiştirebilir ve sipariş verebilirsiniz.
                </p>
            </div>
            <a href="{{ url()->previous() }}" class="back-btn inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Geri
            </a>
        </div>

        {{-- Sepet listesi --}}
        <section id="cartList" class="space-y-3"></section>

        {{-- Özet --}}
        <section class="cart-card mt-6 p-5">
            <div class="flex items-center justify-between">
                <span class="text-lg font-semibold">Toplam</span>
                <span id="sumTotal" class="text-2xl font-extrabold text-orange-500">0,00 ₺</span>
            </div>

            <div class="divider my-4"></div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <button id="clearBtn" class="back-btn w-full inline-flex items-center justify-center gap-2" disabled>
                    <i class="fa-solid fa-trash-can"></i> Temizle
                </button>
                <button id="callBtn"
                    class="btn-secondary w-full inline-flex items-center justify-center gap-2 border-2 border-white/30">
                    <i class="fa-solid fa-bell-concierge"></i> Garson Çağır
                </button>
                <button id="orderBtn" class="order-btn w-full inline-flex items-center justify-center gap-2" disabled>
                    <i class="fa-solid fa-check"></i> Siparişi Tamamla
                </button>
            </div>
        </section>
    </main>

    @include('layouts.partials.public-footer')

    <div id="toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg hidden">
        Siparişiniz başarıyla iletildi.
    </div>

    {{-- İsim Modalı --}}
    <div id="nameModal" class="name-modal" aria-modal="true" role="dialog">
        <div class="name-card">
            <h3 class="font-playfair text-xl">Sipariş için adınızı giriniz</h3>
            <p class="text-gray-400 text-sm mb-3">Hazırlık ve servis için isminize ihtiyaç duyuyoruz.</p>
            <input id="nameInput" type="text" class="name-input" placeholder="Örn: Mehmet" maxlength="100">
            <div class="name-actions">
                <button id="nameCancel" class="btn-light flex-1">İptal</button>
                <button id="nameConfirm" class="add-btn flex-1 justify-center">Devam Et</button>
            </div>
        </div>
    </div>

    <script>
        // ===== Backend sabitleri =====
        const TOKEN = @json($table->token);
        const ROUTES = {
            items: @json(route('customer.cart.items', ['token' => $table->token])),
            add: @json(route('customer.cart.add', ['token' => $table->token])),
            remove: @json(route('customer.cart.remove', ['token' => $table->token, 'productId' => '__ID__'])),
            clear: @json(route('customer.cart.clear', ['token' => $table->token])),
            checkout: @json(route('customer.checkout', ['token' => $table->token])),
            call: @json(route('customer.call', ['token' => $table->token])),
        };

        function csrfHeader() {
            const t = document.querySelector('meta[name="csrf-token"]').content;
            return { 'X-CSRF-TOKEN': t, 'Accept': 'application/json', 'Content-Type': 'application/json' };
        }

        const cartList = document.getElementById('cartList');
        const sumTotal = document.getElementById('sumTotal');
        const clearBtn = document.getElementById('clearBtn');
        const orderBtn = document.getElementById('orderBtn');

        function toggleActionButtons(disabled) {
            [clearBtn, orderBtn].forEach(btn => {
                btn.disabled = disabled;
                btn.classList.toggle('is-disabled', disabled);
            });
        }

        async function loadCart() {
            try {
                const res = await fetch(ROUTES.items);
                const data = await res.json();
                renderCart(data.items || [], data.total || 0);
            } catch (e) {
                console.error(e);
                renderCart([], 0);
            }
        }

        function renderCart(items, total) {
            cartList.innerHTML = '';
            if (!items.length) {
                cartList.innerHTML = `<div class="cart-card p-6 text-center text-gray-400">
          Sepetiniz boş. Menüye dönerek ürün ekleyebilirsiniz.
        </div>`;
            } else {
                items.forEach(it => {
                    const row = document.createElement('div');
                    row.className = 'cart-card p-3';
                    row.innerHTML = `
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <h3 class="font-semibold">${it.name}</h3>
                <p class="text-orange-500 font-bold">${Number(it.price).toFixed(2)} ₺</p>
              </div>
              <div class="flex items-center gap-2">
                <button class="qty-btn minus">−</button>
                <span class="px-2 font-bold">${it.quantity}</span>
                <button class="qty-btn plus">+</button>
              </div>
            </div>
          `;
                    row.querySelector('.minus').addEventListener('click', () => changeQty(it.id, -1));
                    row.querySelector('.plus').addEventListener('click', () => changeQty(it.id, +1));
                    cartList.appendChild(row);
                });
            }
            toggleActionButtons(!(items && items.length));
            sumTotal.textContent = (Number(total).toFixed(2)) + ' ₺';
        }

        async function changeQty(productId, delta) {
            if (delta > 0) {
                await fetch(ROUTES.add, {
                    method: 'POST', headers: csrfHeader(),
                    body: JSON.stringify({ product_id: productId, qty: 1 })
                }).catch(() => { });
            } else {
                await fetch(ROUTES.remove.replace('__ID__', productId), {
                    method: 'POST', headers: csrfHeader()
                }).catch(() => { });
            }
            loadCart();
        }

        clearBtn.addEventListener('click', async () => {
            if (clearBtn.disabled) return;
            try {
                await fetch(ROUTES.clear, { method: 'POST', headers: csrfHeader() });
            } catch (_) { }
            await loadCart();
        });

        document.getElementById('callBtn').addEventListener('click', async () => {
            await fetch(ROUTES.call, { method: 'POST', headers: csrfHeader() }).catch(() => { });
            showToast('Garson çağrınız iletildi. Lütfen bekleyiniz.');
        });

        // İsim modal
        function openNameModal(resolve) {
            const modal = document.getElementById('nameModal');
            const input = document.getElementById('nameInput');
            modal.classList.add('active');
            input.value = (localStorage.getItem('customer_name') || '').trim();
            input.focus();
            const confirmBtn = document.getElementById('nameConfirm');
            const cancelBtn = document.getElementById('nameCancel');
            function cleanup() {
                confirmBtn.removeEventListener('click', onConfirm);
                cancelBtn.removeEventListener('click', onCancel);
                modal.removeEventListener('click', onBackdrop);
            }
            function onConfirm() {
                const name = (input.value || '').trim();
                if (name.length < 2) { input.focus(); input.select(); return; }
                localStorage.setItem('customer_name', name);
                modal.classList.remove('active');
                cleanup();
                resolve(name);
            }
            function onCancel() { modal.classList.remove('active'); cleanup(); resolve(null); }
            function onBackdrop(e) { if (e.target === modal) { onCancel(); } }
            confirmBtn.addEventListener('click', onConfirm);
            cancelBtn.addEventListener('click', onCancel);
            modal.addEventListener('click', onBackdrop);
        }
        function askName() { return new Promise((resolve) => { openNameModal(resolve); }); }

        orderBtn.addEventListener('click', async () => {
            if (orderBtn.disabled) return;
            const name = await askName();
            if (!name) return;
            await fetch(ROUTES.checkout, {
                method: 'POST', headers: csrfHeader(),
                body: JSON.stringify({ customer_name: name, confirm: true })
            }).catch(() => null);
            await loadCart();
            showToast('Siparişiniz başarıyla iletildi.');
        });

        function showToast(message) {
            const t = document.getElementById('toast');
            if (!t) return;
            t.textContent = message || 'İşlem başarılı';
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 2000);
        }

        // Mobil menü toggle
        (function () {
            const toggle = document.getElementById('mobileToggle');
            const menu = document.getElementById('navMenu');
            if (toggle && menu) {
                toggle.addEventListener('click', () => menu.classList.toggle('active'));
                menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => menu.classList.remove('active')));
            }
        })();

        // İlk yükleme
        loadCart();
    </script>
</body>

</html>