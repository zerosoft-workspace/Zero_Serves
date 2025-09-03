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
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .font-playfair {
            font-family: 'Playfair Display', serif
        }

        .font-inter {
            font-family: 'Inter', sans-serif
        }

        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --bg-dark: #0a0a0a;
            --bg-card: #111214;
            --text: #fff;
            --text-muted: #a0a0a0;
            --border: rgba(255, 255, 255, .1)
        }

        body {
            background: var(--bg-dark);
            color: var(--text)
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, .95);
            z-index: 1000;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 107, 53, .2);
            backdrop-filter: blur(15px)
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff6b35;
            text-decoration: none
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none
        }

        .nav-menu a:hover {
            color: #ff6b35
        }

        .order-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            padding: 14px 26px;
            border-radius: 30px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, .4)
        }

        .back-btn {
            background: rgba(255, 255, 255, .08);
            padding: 10px 14px;
            border-radius: 12px
        }

        .cart-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            font-weight: 900
        }

        .divider {
            border-top: 1px solid var(--border)
        }

        /* Disabled durum */
        .is-disabled,
        button:disabled {
            opacity: .5;
            pointer-events: none;
            cursor: not-allowed !important;
        }

        /* Sticky footer */
        html,
        body {
            height: 100%;
        }

        body {
            min-height: 100svh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        footer,
        .site-footer {
            margin-top: auto;
        }

        @media (max-width:768px) {
            .nav-menu {
                display: none
            }
        }
    </style>
</head>

<body class="font-inter">

    {{-- Header --}}
    <header class="header">
        <nav class="nav">
            <a href="#" class="logo">SoftFood</a>
            <ul class="nav-menu">
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
        </nav>
    </header>

    {{-- Page --}}
    <main class="max-w-4xl mx-auto px-4" style="padding-top:110px;padding-bottom:48px">
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
        Siparişiniz başarıyla iletildi.</div>

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

        // Buton toggle
        function toggleActionButtons(disabled) {
            [clearBtn, orderBtn].forEach(btn => {
                btn.disabled = disabled;
                btn.classList.toggle('is-disabled', disabled);
            });
        }

        // Listeyi doldur
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

        orderBtn.addEventListener('click', async () => {
            if (orderBtn.disabled) return;
            await fetch(ROUTES.checkout, {
                method: 'POST', headers: csrfHeader(),
                body: JSON.stringify({ confirm: true })
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

        // İlk yükleme
        loadCart();
    </script>
</body>

</html>