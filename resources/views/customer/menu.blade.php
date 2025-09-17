<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeCat->name }} | SoftFood - Dijital Menü</title>

    {{-- Tailwind & Icons --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- Fonts --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Global palette--}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    {{-- Sayfaya özgü CSS --}}
    <link rel="stylesheet" href="{{ asset('css/qr_customer.css') }}">

    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-inter page-menu">
    {{-- Header --}}
    <header id="header" class="header">
        <nav class="nav container">
            <a href="#" class="logo">SoftFood</a>
            <ul id="navMenu" class="nav-menu">
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'dashboard']) }}">Menü</a>
                </li>
                <li><a href="{{ route('customer.cart.view', ['token' => $table->token]) }}">Sepetim</a></li>
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'orders']) }}">Siparişlerim</a>
                </li>
            </ul>
            {{-- SAĞ BLOK --}}
            <div class="nav-actions">
                <div class="nav-buttons">
                    <button id="backBtn" class="back-btn hidden">
                        <i class="fas fa-arrow-left"></i> Geri
                    </button>
                    {{-- <div class="text-sm text-gray-400 ml-4">
                        <i class="fas fa-qrcode mr-2"></i>
                        {{ $table->name ?? 'Dijital Menü' }}
                    </div> --}}
                </div>

                {{-- Garson çağır (mobilden gizli; isterse hidden’ı kaldırabilirsin) --}}
                <button class="btn-secondary btn-call-waiter  sm:inline-flex items-center gap-2 text-black"
                    data-url="{{ route('customer.call', ['token' => $table->token]) }}" type="button">
                    <i class="fa-solid fa-bell-concierge"></i>
                    <span>Garson</span>
                </button>

                <button class="mobile-menu-toggle" id="mobileToggle" aria-label="Menüyü Aç/Kapat">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>

    {{-- Sayfa Hero / başlık --}}
    <section class="page-hero">
        <div class="container mx-auto px-4">
            {{-- Kategoriler Grid --}}
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                @php
                    $cats = collect($categories ?? []);
                    $tableToken = $table?->token;
                @endphp

                @forelse($cats as $category)
                    @php
                        $catName = $category->name ?? 'Kategori';
                        $slug = $category->slug ?? \Illuminate\Support\Str::slug($catName);
                        $fallbacks = [
                            asset('images/menu/anayemek.jpg'),
                            asset('images/menu/salata.jpg'),
                            asset('images/menu/kahvalti.jpg'),
                            asset('images/menu/icecek.jpg'),
                            asset('images/menu/kahve.jpg'),
                            asset('images/menu/tatli.jpg'),
                        ];
                        $img = $fallbacks[$loop->index % count($fallbacks)];
                        $isActive = (($activeCat?->id ?? null) === ($category->id ?? null)) ? 'active' : '';
                        $productCount = 0;
                        if (isset($category->products)) {
                            $productCount =
                                method_exists($category->products, 'count')
                                ? ($category->products?->count() ?? 0)
                                : (is_iterable($category->products) ? count($category->products) : 0);
                        }
                        $href = $tableToken
                            ? route('customer.menu', ['token' => $tableToken, 'category' => $slug])
                            : 'javascript:void(0)';
                        $disabledAttr = $tableToken ? '' : 'aria-disabled=true';
                    @endphp

                    <a href="{{ $href }}" {!! $disabledAttr !!} class="category-card {{ $isActive }}">
                        <img src="{{ $img }}" alt="{{ $catName }}" class="category-img">
                        <div class="category-title">
                            <h3 class="font-playfair text-sm font-bold">{{ $catName }}</h3>
                            <p class="text-xs text-gray-300">{{ $productCount }} ürün</p>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center text-sm text-gray-400">
                        Şu anda listelenecek kategori bulunamadı.
                    </div>
                @endforelse
            </div>

            {{-- Aktif kategori başlığı --}}
            @php
                $activeName = $activeCat?->name ?? 'Tüm Ürünler';
                $activeCount =
                    isset($activeCat?->products)
                    ? (method_exists($activeCat->products, 'count')
                        ? ($activeCat->products?->count() ?? 0)
                        : (is_iterable($activeCat->products) ? count($activeCat->products) : 0))
                    : 0;
            @endphp

            <div class="text-center">
                <h2 class="font-playfair text-xl font-bold text-black">{{ $activeName }}</h2>
                <p class="text-gray-400 mt-1">{{ $activeCount }} ürün listeleniyor</p>
            </div>
        </div>
    </section>

    {{-- Ürün Grid --}}
    <main class="container mx-auto px-4 py-8">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($activeCat->products as $p)
                <div class="product-card">
                    <div class="p-4 flex justify-between items-center gap-4">
                        <div>
                            <h3 class="font-playfair font-bold text-lg mb-1">{{ $p->name }}</h3>
                            @if(!empty($p->description))
                                <p class="text-gray-400 text-sm mb-2">{{ $p->description }}</p>
                            @endif
                            <span
                                class="font-bold text-lg text-orange-500">{{ number_format((float) ($p->price ?? 0), 2, ',', '.') }}
                                ₺</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="quantity-control">
                                <button class="quantity-btn minus" data-id="{{ $p->id }}">−</button>
                                <span class="px-3 font-bold qty-val" data-id="{{ $p->id }}">1</span>
                                <button class="quantity-btn plus" data-id="{{ $p->id }}">+</button>
                            </div>
                            <button class="add-btn" data-id="{{ $p->id }}">
                                <i class="fas fa-plus"></i> Ekle
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400">Bu kategoride ürün yok.</p>
            @endforelse
        </div>
    </main>

    {{-- Sepet FAB --}}
    <div id="cartFab" class="cart-fab">
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
            <div id="cartItems" class="space-y-4 mb-6"></div>
            <div class="border-t border-gray-700 pt-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-lg">Toplam:</span>
                    <span id="cartTotal" class="font-bold text-xl text-orange-500">0.00 ₺</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button id="clearCartBtn" class="back-chip justify-center"><i class="fa-solid fa-trash-can"></i>
                        Temizle</button>
                    <button id="orderBtn" class="add-btn justify-center"><i class="fas fa-check"></i> Sipariş
                        Ver</button>
                </div>
            </div>
        </div>
    </div>

    {{-- İsim alma modalı --}}
    <div id="nameModal" class="modal">
        <div class="modal-content">
            <h3 class="font-playfair text-xl font-bold mb-2">Sipariş için adınızı giriniz</h3>
            <p class="text-gray-400 text-sm mb-3">Aynı masada birden fazla kişi sipariş verebilir. Hazırlık ve servis
                için isminize ihtiyaç duyuyoruz.</p>
            <input id="nameInput" type="text" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3"
                placeholder="Örn: Mehmet" maxlength="100" />
            <div class="grid grid-cols-2 gap-3">
                <button id="nameCancel" class="back-btn justify-center">İptal</button>
                <button id="nameConfirm" class="add-btn justify-center">Devam Et</button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg hidden">
        Siparişiniz başarıyla iletildi.
    </div>

    {{-- Footer --}}
    @include('layouts.partials.public-footer')

    <script>
        (function setupGlobalCallWaiter() {
            function _csrfHeader() {
                const el = document.querySelector('meta[name="csrf-token"]');
                return { 'X-CSRF-TOKEN': el ? el.content : '', 'Accept': 'application/json', 'Content-Type': 'application/json' };
            }
            function _toast(msg) {
                if (typeof showToast === 'function') return showToast(msg);
                // basit fallback
                try {
                    let t = document.getElementById('toast');
                    if (!t) { alert(msg); return; }
                    t.textContent = msg || 'İşlem başarılı';
                    t.classList.remove('hidden'); setTimeout(() => t.classList.add('hidden'), 2000);
                } catch (_) { alert(msg); }
            }
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('.btn-call-waiter');
                if (!btn) return;

                if (window.btnMarkBusy) btnMarkBusy(btn, '<i class="fa-solid fa-bell-concierge fa-shake"></i> Çağırılıyor...');
                else btn.disabled = true;

                try {
                    const url = btn.dataset.url;
                    await fetch(url, { method: 'POST', headers: _csrfHeader() });
                    _toast('Garson çağrınız iletildi. Lütfen bekleyiniz.');
                } catch (_) {
                    _toast('Bir sorun oldu, tekrar dener misiniz?');
                } finally {
                    if (window.btnClearBusy) btnClearBusy(btn);
                    else btn.disabled = false;
                }
            });
        })();
        function btnMarkBusy(btn, html) {
            if (!btn) return;
            if (!btn.dataset._oldHtml) btn.dataset._oldHtml = btn.innerHTML;
            btn.classList.add('is-busy');
            btn.setAttribute('aria-disabled', 'true');
            btn.disabled = true;
            if (html) btn.innerHTML = html;
        }
        function btnClearBusy(btn) {
            if (!btn) return;
            btn.disabled = false;
            btn.removeAttribute('aria-disabled');
            btn.classList.remove('is-busy', 'is-success');
            if (btn.dataset._oldHtml) {
                btn.innerHTML = btn.dataset._oldHtml;
                delete btn.dataset._oldHtml;
            }
        }
        /* Ekle butonu için: 2 sn kilitle + ✓ göster, sonra geri al */
        function flashAddSuccess(btn, duration = 2000) {
            btnMarkBusy(btn, '<i class="fas fa-check"></i> Eklendi');
            btn.classList.add('is-success');
            setTimeout(() => btnClearBusy(btn), duration);
        }

        // Header scroll efekti + mobil menü
        const headerEl = document.getElementById('header');
        window.addEventListener("scroll", () => headerEl.classList.toggle("scrolled", window.scrollY > 50));
        const mobileToggle = document.getElementById("mobileToggle");
        const navMenu = document.getElementById("navMenu");
        if (mobileToggle && navMenu) {
            mobileToggle.addEventListener("click", () => navMenu.classList.toggle("active"));
            document.querySelectorAll('#navMenu a').forEach(a => a.addEventListener('click', () => navMenu.classList.remove('active')));
        }

        // CSRF
        function csrfHeader() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            return { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' };
        }

        // Backend sabitleri
        const TOKEN = @json($table->token);
        const ROUTES = {
            add: @json(route('customer.cart.add', ['token' => $table->token])),
            remove: @json(route('customer.cart.remove', ['token' => $table->token, 'productId' => '__ID__'])),
            clear: @json(route('customer.cart.clear', ['token' => $table->token])),
            checkout: @json(route('customer.checkout', ['token' => $table->token])),
            items: @json(route('customer.cart.items', ['token' => $table->token])),
            call: @json(route('customer.call', ['token' => $table->token])),
        };

        // Sepet state
        let cart = [];

        // Qty kontrolü
        document.addEventListener('click', async (e) => {
            const plusBtn = e.target.closest('.quantity-btn.plus');
            const minusBtn = e.target.closest('.quantity-btn.minus');

            if (plusBtn) {
                const id = plusBtn.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const cur = Math.max(1, parseInt(span?.textContent || '1', 10) + 1);
                span.textContent = cur;
                return;
            }

            if (minusBtn) {
                const id = minusBtn.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const cur = Math.max(1, parseInt(span?.textContent || '1', 10) - 1);
                span.textContent = cur;
                return;
            }

            const addBtn = e.target.closest('.add-btn');
            if (addBtn) {
                if (addBtn.classList.contains('is-busy')) return;
                const id = addBtn.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const qty = Math.max(1, parseInt(span?.textContent || '1', 10));
                flashAddSuccess(addBtn, 2000);  // 2 sn “✓ Eklendi”
                await addToCart(id, qty);
            }
        });


        async function addToCart(productId, qty = 1) {
            await fetch(ROUTES.add, { method: 'POST', headers: csrfHeader(), body: JSON.stringify({ product_id: productId, qty }) }).catch(() => { });
            await loadCartFromServer(); updateCartUI(); showCartModalFab();
        }
        async function removeOne(productId) {
            await fetch(ROUTES.remove.replace('__ID__', productId), { method: 'POST', headers: csrfHeader() }).catch(() => { });
            await loadCartFromServer(); updateCartUI();
        }
        async function clearCart() {
            await fetch(ROUTES.clear, { method: 'POST', headers: csrfHeader() }).catch(() => { });
            await loadCartFromServer(); updateCartUI(); cartModal.classList.remove('active');
        }
        // Ürünü TAMAMEN kaldır (mevcut /remove ucu 1 azaltıyor; qty kadar çağırıyoruz)
        async function removeItemAll(productId) {
            const it = cart.find(i => String(i.id) === String(productId));
            if (!it) return;
            for (let k = 0; k < it.quantity; k++) {
                await fetch(ROUTES.remove.replace('__ID__', productId), {
                    method: 'POST', headers: csrfHeader()
                }).catch(() => { });
            }
            await loadCartFromServer();
            updateCartUI();
        }

        function openNameModal(resolve) {
            const modal = document.getElementById('nameModal');
            const input = document.getElementById('nameInput');
            if (!modal || !input) { return resolve(null); }
            modal.classList.add('active');
            try { input.value = (localStorage.getItem('customer_name') || '').trim(); } catch (_) { input.value = ''; }
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
                try { localStorage.setItem('customer_name', name); } catch (_) { }
                modal.classList.remove('active'); cleanup(); resolve(name);
            }
            function onCancel() { modal.classList.remove('active'); cleanup(); resolve(null); }
            function onBackdrop(e) { if (e.target === modal) { onCancel(); } }
            confirmBtn.addEventListener('click', onConfirm);
            cancelBtn.addEventListener('click', onCancel);
            modal.addEventListener('click', onBackdrop);
        }
        function askName() { return new Promise((resolve) => { openNameModal(resolve); }); }

        async function checkout() {
            if (cart.length === 0) return;
            const orderBtnEl = document.getElementById('orderBtn');
            btnMarkBusy(orderBtnEl, '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...');

            try {
                const name = await askName();
                if (!name) return;

                const payload = {
                    customer_name: name,
                    items: cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
                };
                await fetch(ROUTES.checkout, { method: 'POST', headers: csrfHeader(), body: JSON.stringify(payload) }).catch(() => { });

                await loadCartFromServer();
                cart = [];
                updateCartUI();
                cartModal.classList.remove('active');
                showToast('Siparişiniz başarıyla iletildi.');
            } finally {
                btnClearBusy(orderBtnEl);
            }
        }


        function updateCartUI() {
            const cartBadge = document.getElementById('cartBadge');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');

            const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
            const totalPrice = cart.reduce((s, i) => s + i.quantity * Number(i.price || 0), 0);

            cartBadge.textContent = totalItems;
            cartTotal.textContent = totalPrice.toFixed(2) + ' ₺';

            cartItems.innerHTML = '';
            if (totalItems === 0) {
                cartItems.innerHTML = '<p class="text-gray-400 text-center py-8">Sepetiniz boş</p>';
                return;
            }

            cart.forEach(item => {
                const row = document.createElement('div');
                row.className = 'product-card cart-item';
                row.innerHTML = `
    <div class="p-4 flex items-center justify-between gap-4">
      <div>
        <h4 class="font-playfair font-bold text-base md:text-lg mb-1">${item.name}</h4>
        <p class="text-orange-500 font-bold">${Number(item.price).toFixed(2)} ₺</p>
      </div>

      <div class="flex items-center gap-3">
        <button class="item-remove" data-id="${item.id}" aria-label="Ürünü kaldır">
          <i class="fa-solid fa-trash-can"></i>
        </button>
        <div class="quantity-control">
          <button class="quantity-btn" data-action="minus" data-id="${item.id}">−</button>
          <span class="px-3 font-bold">${item.quantity}</span>
          <button class="quantity-btn" data-action="plus" data-id="${item.id}">+</button>
        </div>
      </div>
    </div>
  `;

                row.querySelector('[data-action="minus"]').addEventListener('click', () => removeOne(item.id));
                row.querySelector('[data-action="plus"]').addEventListener('click', () => addToCart(item.id, 1));
                row.querySelector('.item-remove').addEventListener('click', () => removeItemAll(item.id));

                cartItems.appendChild(row);
            });

        }

        async function loadCartFromServer() {
            try {
                const res = await fetch(ROUTES.items);
                const data = await res.json();
                const items = Array.isArray(data.items) ? data.items : [];
                cart = items.map(x => ({ id: x.id, name: x.name, price: x.price, quantity: x.quantity }));
            } catch (_) { }
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.textContent = msg || 'İşlem başarılı';
            t.classList.remove('hidden');
            setTimeout(() => t.classList.add('hidden'), 2000);
        }

        // Modal ve FAB
        const cartFab = document.getElementById('cartFab');
        const cartModal = document.getElementById('cartModal');
        document.getElementById('closeCart').addEventListener('click', () => cartModal.classList.remove('active'));
        cartFab.addEventListener('click', () => cartModal.classList.add('active'));
        cartModal.addEventListener('click', (e) => { if (e.target === cartModal) cartModal.classList.remove('active'); });
        document.getElementById('clearCartBtn').addEventListener('click', clearCart);
        document.getElementById('orderBtn').addEventListener('click', checkout);
        function showCartModalFab() { cartFab.classList.remove('hidden'); }

        // Init
        (async function () {
            await loadCartFromServer();
            updateCartUI();
        })();
    </script>
</body>

</html>