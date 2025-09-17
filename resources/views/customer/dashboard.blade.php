<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoftFood - Dijital Menü</title>

    {{-- Tailwind & Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Global (kurumsal) --}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}"><!-- .btn-menu / .btn-secondary renkleri buradan -->

    {{-- Sayfaya özgü CSS --}}
    <link rel="stylesheet" href="{{ asset('css/qr_customer.css') }}">

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-inter">
    {{-- Header --}}
    <header id="header" class="header">
        <nav class="nav container">
            <a href="#" class="logo">SoftFood</a>

            <ul id="navMenu" class="nav-menu">
                <li><a href="#menu">Menü</a></li>
                <li><a href="{{ route('customer.cart.view', ['token' => $table->token]) }}">Sepetim</a></li>
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'orders']) }}">Siparişlerim</a>
                </li>
            </ul>

            <div class="nav-buttons">
                <button id="backBtn" class="back-btn hidden">
                    <i class="fas fa-arrow-left"></i> Geri
                </button>
                <div class="text-sm text-gray-400 ml-4">
                    <i class="fas fa-qrcode mr-2"></i>
                    {{ $table->name ?? 'Dijital Menü' }}
                </div>
            </div>

            <button class="mobile-menu-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    {{-- Hero --}}
    <section id="home" class="hero">
        <div class="hero-content">
            <h1>Lezzet Yolculuğunuz Başlıyor</h1>
            <p class="hero-subtitle">
                QR kod ile kolayca menümüzü keşfedin, favorilerinizi seçin ve sipariş verin.
                Modern teknoloji ile geleneksel lezzetlerin buluştuğu nokta.
            </p>
            <div class="hero-buttons">
                <a href="#menu" class="btn-menu" id="heroMenuBtn">
                    <i class="fas fa-utensils"></i> Menüyü Gör
                </a>
                <button id="callWaiterBtn" class="btn-secondary">
                    <i class="fa-solid fa-bell-concierge"></i> Garson Çağır
                </button>
            </div>
        </div>
    </section>

    <!-- Kategori Görünümü -->
    <main id="categoryView" class="container mx-auto px-4 py-6">
        <div id="menu" class="text-center mb-8">
            <h2 class="font-playfair text-3xl font-bold mb-2">Menümüzü Keşfedin</h2>
            <p class="text-gray-400">Kategori seçerek ürünleri görüntüleyebilir ve sipariş verebilirsiniz</p>
        </div>

        <div id="categoryGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($categories as $cat)
                @php
                    $fallbacks = [
                        asset('images/menu/anayemek.jpg'),
                        asset('images/menu/salata.jpg'),
                        asset('images/menu/kahvalti.jpg'),
                        asset('images/menu/icecek.jpg'),
                        asset('images/menu/kahve.jpg'),
                        asset('images/menu/tatli.jpg'),
                    ];
                    $img = $fallbacks[$loop->index % count($fallbacks)];
                    $slug = $cat->slug ?? \Illuminate\Support\Str::slug($cat->name);
                @endphp

                <a class="category-card block"
                    href="{{ route('customer.menu', ['token' => $table->token, 'category' => $slug]) }}">
                    <img src="{{ $img }}" alt="{{ $cat->name }}" class="category-img">
                    <div class="category-title">
                        <h3 class="font-playfair text-xl font-bold">{{ $cat->name }}</h3>
                        <p class="text-sm text-gray-300">{{ $cat->products->count() }} ürün</p>
                    </div>
                </a>
            @empty
                <p class="text-gray-400 col-span-4 text-center">Henüz kategori bulunmamaktadır.</p>
            @endforelse
        </div>
    </main>

    {{-- Ürün Listesi --}}
    <main id="productView" class="container mx-auto px-4 py-6 hidden">
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-1">
                <button id="categoryBackBtn" class="back-btn"><i class="fas fa-arrow-left"></i> Geri</button>
                <h2 id="categoryTitle" class="font-playfair text-2xl font-bold"></h2>
            </div>
            <p class="text-gray-400">Ürün seçerek sepetinize ekleyebilirsiniz</p>
        </div>
        <div id="productGrid" class="space-y-4"><!-- JS dolduracak --></div>
    </main>

    {{-- Sepet FAB --}}
    <div id="cartFab" class="cart-fab hidden">
        <i class="fas fa-shopping-bag"></i>
        <span id="cartBadge" class="cart-badge">0</span>
    </div>

    {{-- Sepet Modal --}}
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-playfair text-xl font-bold">Sepetim</h3>
                <button id="closeCart" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="cartItems" class="space-y-4 mb-6"><!-- JS dolduracak --></div>

            <div class="border-t border-gray-700 pt-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-lg">Toplam:</span>
                    <span id="cartTotal" class="font-bold text-xl text-orange-500">0.00 ₺</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button id="clearCartBtn" class="back-btn justify-center"><i class="fa-solid fa-trash-can"></i>
                        Temizle</button>
                    <button id="orderBtn" class="order-btn"><i class="fas fa-check mr-2"></i> Sipariş Ver</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Basit toast bildirimi -->
    <div id="toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg hidden">
        Siparişiniz başarıyla iletildi.
    </div>

    {{-- Backend’den gelen veriler --}}
    @php
        $normalized = collect($categories)->mapWithKeys(function ($c) {
            $slug = \Illuminate\Support\Str::slug($c->name);
            return [
                $slug => [
                    'name' => $c->name,
                    'products' => collect($c->products)->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'price' => (float) ($p->price ?? 0),
                            'description' => $p->description ?? '',
                            'image' => $p->image_url ?? asset('images/placeholder.jpg'),
                        ];
                    })->values()
                ]
            ];
        });
    @endphp
    @php
        $fallbacks = [
            asset('images/menu/anayemek.jpg'),
            asset('images/menu/salata.jpg'),
            asset('images/menu/kahvalti.jpg'),
            asset('images/menu/icecek.jpg'),
            asset('images/menu/kahve.jpg'),
            asset('images/menu/tatli.jpg'),
        ];
    @endphp

    @include('layouts.partials.public-footer')

    <script>
        // ====== Back-end sabitleri ======
        const TOKEN = @json($table->token);
        const ROUTES = {
            add: @json(route('customer.cart.add', ['token' => $table->token])),
            remove: @json(route('customer.cart.remove', ['token' => $table->token, 'productId' => '__ID__'])),
            clear: @json(route('customer.cart.clear', ['token' => $table->token])),
            checkout: @json(route('customer.checkout', ['token' => $table->token])),
            call: @json(route('customer.call', ['token' => $table->token])),
            pay: @json(route('customer.pay', ['token' => $table->token])),
            items: @json(route('customer.cart.items', ['token' => $table->token])),
        };
        const CATEGORIES = @json($normalized, JSON_UNESCAPED_UNICODE);
        const FALLBACKS = @json($fallbacks);
    </script>

    <script>
        // ====== CSRF header helper ======
        function csrfHeader() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' };
        }

        // ====== UI elementleri ======
        const headerEl = document.getElementById('header');
        const categoryView = document.getElementById('categoryView');
        const categoryGrid = document.getElementById('categoryGrid');
        const productView = document.getElementById('productView');
        const categoryTitle = document.getElementById('categoryTitle');
        const productGrid = document.getElementById('productGrid');
        const cartFab = document.getElementById('cartFab');
        const cartBadge = document.getElementById('cartBadge');
        const cartModal = document.getElementById('cartModal');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const backBtn = document.getElementById('backBtn');

        // ====== State ======
        let cart = [];
        let currentCategory = null;

        // ====== Header ve mobil menü ======
        window.addEventListener("scroll", () => {
            headerEl.classList.toggle("scrolled", window.scrollY > 50);
        });
        const mobileToggle = document.getElementById("mobileToggle");
        const navMenu = document.getElementById("navMenu");
        mobileToggle.addEventListener("click", () => navMenu.classList.toggle("active"));
        document.querySelectorAll('#navMenu a').forEach(a => a.addEventListener('click', () => navMenu.classList.remove('active')));
        document.getElementById('heroMenuBtn').addEventListener('click', (e) => {
            e.preventDefault(); document.getElementById('menu').scrollIntoView({ behavior: 'smooth' });
        });
        document.querySelectorAll('.nav-menu a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', e => {
                e.preventDefault();
                const t = document.querySelector(anchor.getAttribute('href'));
                t && t.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // ====== Ürün listele ======
        function showProducts(categoryKey) {
            const categoryData = CATEGORIES[categoryKey];
            if (!categoryData) return;

            categoryTitle.textContent = categoryData.name;
            categoryView.classList.add('hidden');
            productView.classList.remove('hidden');
            backBtn.classList.add('hidden');
            productGrid.innerHTML = '';

            categoryData.products.forEach((p, i) => {
                const div = document.createElement('div');
                div.className = 'product-card fade-in';
                div.innerHTML = `
          <div class="flex justify-between items-center p-4 gap-4">
            <div>
              <h3 class="font-playfair font-bold text-lg mb-1">${p.name}</h3>
              <p class="text-gray-400 text-sm mb-2">${p.description ?? ''}</p>
              <span class="font-bold text-lg text-orange-500">${Number(p.price).toFixed(2)} ₺</span>
            </div>
            <div class="flex items-center gap-3">
              <div class="quantity-control">
                <button class="quantity-btn minus" aria-label="Azalt">−</button>
                <span class="px-3 font-bold qty-val">1</span>
                <button class="quantity-btn plus" aria-label="Arttır">+</button>
              </div>
              <button class="add-btn" data-id="${p.id}">
                <i class="fas fa-plus"></i> Ekle
              </button>
            </div>
          </div>
        `;
                const qtySpan = div.querySelector('.qty-val');
                div.querySelector('.minus').addEventListener('click', () => {
                    const cur = Math.max(1, (parseInt(qtySpan.textContent || '1', 10) - 1));
                    qtySpan.textContent = String(cur);
                });
                div.querySelector('.plus').addEventListener('click', () => {
                    const cur = Math.max(1, (parseInt(qtySpan.textContent || '1', 10) + 1));
                    qtySpan.textContent = String(cur);
                });
                div.querySelector('.add-btn').addEventListener('click', () => {
                    const qty = Math.max(1, parseInt(qtySpan.textContent || '1', 10));
                    addToCart(p.id, qty);
                });
                productGrid.appendChild(div);
                setTimeout(() => div.classList.add('visible'), i * 100);
            });
        }

        // ====== Sepet işlemleri (UI + Server) ======
        async function addToCart(productId, qty = 1) {
            await fetch(ROUTES.add, {
                method: 'POST', headers: csrfHeader(),
                body: JSON.stringify({ product_id: productId, qty: qty })
            }).catch(() => { });
            await loadCartFromServer();
            updateCartUI(); showCartFab();
        }

        async function removeOne(productId) {
            await fetch(ROUTES.remove.replace('__ID__', productId), { method: 'POST', headers: csrfHeader() }).catch(() => { });
            await loadCartFromServer();
            updateCartUI();
        }

        async function clearCart() {
            await fetch(ROUTES.clear, { method: 'POST', headers: csrfHeader() }).catch(() => { });
            await loadCartFromServer();
            updateCartUI(); cartModal.classList.remove('active');
        }

        async function checkout() {
            if (cart.length === 0) return;
            const payload = {
                items: cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
            };
            await fetch(ROUTES.checkout, {
                method: 'POST', headers: csrfHeader(), body: JSON.stringify(payload)
            }).catch(() => null);
            await loadCartFromServer();
            cart = []; updateCartUI(); cartModal.classList.remove('active');
            showToast('Siparişiniz başarıyla iletildi.');
        }

        // ====== Yardımcılar ======
        function findProduct(id) {
            for (const cat of Object.values(CATEGORIES)) {
                const p = cat.products.find(pp => pp.id === id);
                if (p) return p;
            }
            return null;
        }
        function updateCartUI() {
            const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
            const totalPrice = cart.reduce((s, i) => s + i.quantity * Number(i.price || 0), 0);
            cartBadge.textContent = totalItems;
            cartTotal.textContent = totalPrice.toFixed(2) + ' ₺';

            cartItems.innerHTML = '';
            if (totalItems === 0) {
                cartItems.innerHTML = '<p class="text-gray-400 text-center py-8">Sepetiniz boş</p>';
                cartFab.classList.remove('hidden');
                return;
            }
            cart.forEach(item => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-4 bg-gray-800 rounded-lg p-3';
                row.innerHTML = `
          <div class="flex-1">
            <h4 class="font-medium">${item.name}</h4>
            <p class="text-orange-500 font-bold">${Number(item.price).toFixed(2)} ₺</p>
          </div>
          <div class="quantity-control">
            <button class="quantity-btn minus">−</button>
            <span class="px-3 font-bold">${item.quantity}</span>
            <button class="quantity-btn plus">+</button>
          </div>
        `;
                row.querySelector('.minus').addEventListener('click', () => removeOne(item.id));
                row.querySelector('.plus').addEventListener('click', () => addToCart(item.id, 1));
                cartItems.appendChild(row);
            });
        }
        function showCartFab() { cartFab.classList.remove('hidden'); }

        async function loadCartFromServer() {
            try {
                const res = await fetch(ROUTES.items);
                const data = await res.json();
                const items = Array.isArray(data.items) ? data.items : [];
                cart = items.map(x => ({ id: x.id, name: x.name, price: x.price, quantity: x.quantity }));
            } catch (_) { }
        }

        function showToast(message) {
            const t = document.getElementById('toast');
            if (!t) return;
            t.textContent = message || 'İşlem başarılı';
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 2000);
        }

        // ====== Modal ve butonlar ======
        document.getElementById('clearCartBtn').addEventListener('click', clearCart);
        document.getElementById('orderBtn').addEventListener('click', checkout);
        document.getElementById('closeCart').addEventListener('click', () => cartModal.classList.remove('active'));
        cartFab.addEventListener('click', () => cartModal.classList.add('active'));
        cartModal.addEventListener('click', (e) => { if (e.target === cartModal) cartModal.classList.remove('active'); });
        document.getElementById('categoryBackBtn').addEventListener('click', () => {
            categoryView.classList.remove('hidden');
            productView.classList.add('hidden');
            backBtn.classList.add('hidden');
        });

        // ====== Garson çağır & Ödeme ======
        document.getElementById('callWaiterBtn').addEventListener('click', async () => {
            await fetch(ROUTES.call, { method: 'POST', headers: csrfHeader() }).catch(() => { });
            showToast('Garson çağrınız iletildi. Lütfen bekleyiniz.');
        });

        // ====== Fade-in gözlemci ======
        function observeFadeIns() {
            const opts = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
            const io = new IntersectionObserver(entries => {
                entries.forEach(en => { if (en.isIntersecting) en.target.classList.add('visible'); });
            }, opts);
            document.querySelectorAll('.fade-in').forEach(el => io.observe(el));
        }

        // ====== Sayfa yüklenince ======
        (function init() {
            observeFadeIns();
            loadCartFromServer().then(() => { updateCartUI(); });
            try { cartFab.classList.remove('hidden'); } catch (e) { }
            window.addEventListener('pageshow', () => { loadCartFromServer().then(updateCartUI).catch(() => { }); });
            window.addEventListener('focus', () => { loadCartFromServer().then(updateCartUI).catch(() => { }); });
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) { loadCartFromServer().then(updateCartUI).catch(() => { }); }
            });
        })();

        // Kategori kartları (event delegation)
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.category-card');
            if (!card) return;
            const key = (card.dataset.category || '').toString().trim();
            if (!key) return; // normal link ise default
            e.preventDefault();
            showProductsSafe(key);
        });

        function showProductsSafe(categoryKey) {
            let data = CATEGORIES[categoryKey];
            if (!data) {
                const guess = Object.keys(CATEGORIES).find(k => k.toLowerCase() === categoryKey.toLowerCase());
                if (guess) data = CATEGORIES[guess], categoryKey = guess;
            }
            if (!data) {
                console.warn('Kategori bulunamadı:', categoryKey, '— anahtarlar:', Object.keys(CATEGORIES));
                alert('Bu kategori için ürün bulunamadı.');
                return;
            }
            showProducts(categoryKey);
        }
    </script>
</body>

</html>