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
    <link rel="stylesheet" href="{{ asset('css/public.css') }}"><!-- .btn-menu / .btn-secondary renkleri buradan -->

    {{-- CSRF --}}
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
            --text: #ffffff;
            --text-muted: #a0a0a0;
            --border: rgba(255, 255, 255, .1)
        }

        body {
            background: var(--bg-dark);
            color: var(--text);
            overflow-x: hidden
        }

        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: transparent;
            z-index: 1000;
            padding: 1rem 0;
            transition: all .4s cubic-bezier(.4, 0, .2, 1);
            backdrop-filter: blur(0)
        }

        .header.scrolled {
            background: rgba(0, 0, 0, .95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 107, 53, .2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, .3)
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff6b35;
            text-shadow: 0 2px 4px rgba(0, 0, 0, .3);
            transition: .3s;
            text-decoration: none
        }

        .logo:hover {
            transform: scale(1.05);
            color: #ff8c42
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
            margin: 0;
            padding: 0
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none;
            transition: .3s cubic-bezier(.4, 0, .2, 1);
            position: relative;
            font-weight: 500;
            letter-spacing: .5px
        }

        .nav-menu a::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ff6b35, #ff8c42);
            transition: .3s;
            transform: translateX(-50%)
        }

        .nav-menu a:hover {
            color: #ff6b35;
            transform: translateY(-2px)
        }

        .nav-menu a:hover::after {
            width: 100%
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: .5rem;
            border-radius: 5px;
            transition: .3s
        }

        .mobile-menu-toggle:hover {
            background: rgba(255, 107, 53, .2);
            color: #ff6b35
        }

        .hero {
            height: 100vh;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, .1), rgba(255, 107, 53, .08)),
                linear-gradient(rgba(0, 0, 0, .18), rgba(0, 0, 0, .35)),
                url("https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3");
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            filter: brightness(1.1) contrast(1.05) saturate(1.06)
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(255, 177, 133, .1) 0%, transparent 55%),
                radial-gradient(circle at 80% 20%, rgba(255, 140, 66, .08) 0%, transparent 55%);
            z-index: 1
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: -10% -10% -20% -10%;
            background:
                radial-gradient(ellipse at 50% 45%, rgba(255, 255, 255, .12) 0%, rgba(255, 255, 255, .06) 35%, transparent 70%);
            z-index: 1;
            pointer-events: none
        }

        .hero-content {
            animation: heroFadeIn 1.5s cubic-bezier(.4, 0, .2, 1);
            z-index: 2;
            position: relative;
            max-width: 900px
        }

        .hero h1 {
            font-size: clamp(2.6rem, 8vw, 5.2rem);
            font-weight: 700;
            margin-bottom: 1.6rem;
            letter-spacing: 2px;
            color: #fff;
            text-shadow: 0 2px 6px rgba(0, 0, 0, .3)
        }

        .hero-subtitle {
            font-size: clamp(1.02rem, 3vw, 1.45rem);
            margin-bottom: 2.5rem;
            color: #fff;
            opacity: .95;
            text-shadow: 0 1px 4px rgba(0, 0, 0, .25);
            font-weight: 500;
            letter-spacing: .5px
        }

        .hero-buttons {
            display: flex;
            gap: 1.2rem;
            justify-content: center;
            flex-wrap: wrap
        }

        .btn-menu {
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            color: #fff;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: .4s cubic-bezier(.4, 0, .2, 1);
            box-shadow: 0 8px 30px rgba(255, 107, 53, .4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .5rem
        }

        .btn-menu:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 107, 53, .6);
            background: linear-gradient(135deg, #e55a2b, #ff6b35)
        }

        .btn-secondary {
            background: transparent;
            color: #fff;
            padding: 1rem 2.5rem;
            border: 2px solid rgba(255, 255, 255, .3);
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: .4s cubic-bezier(.4, 0, .2, 1);
            backdrop-filter: blur(10px);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .5rem
        }

        .btn-secondary:hover {
            border-color: #ff6b35;
            color: #ff6b35;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, .2);
            background: rgba(255, 107, 53, .1)
        }

        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(.95)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            height: 180px;
            cursor: pointer;
            transform: translateZ(0);
            transition: .4s cubic-bezier(.4, 0, .2, 1)
        }

        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, .3), rgba(255, 107, 53, .15));
            z-index: 2;
            transition: opacity .3s
        }

        .category-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 107, 53, .2)
        }

        .category-card:hover::before {
            opacity: .8
        }

        .category-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .4s
        }

        .category-card:hover .category-img {
            transform: scale(1.1)
        }

        .category-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            z-index: 3;
            background: linear-gradient(transparent, rgba(0, 0, 0, .8))
        }

        .product-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: .3s;
            position: relative
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 107, 53, .3);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .4)
        }

        .add-btn {
            background: linear-gradient(135deg, var(--primary), #ff8c42);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: .3s;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .add-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 107, 53, .4)
        }

        .cart-fab {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), #ff8c42);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(255, 107, 53, .4);
            z-index: 1000;
            transition: .3s
        }

        .cart-fab:hover {
            transform: scale(1.1)
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff0000;
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .8);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px
        }

        .modal.active {
            display: flex
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 24px;
            width: 100%;
            max-width: 420px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--border)
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, .05);
            border-radius: 25px;
            padding: 6px
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold
        }

        .slide-up {
            animation: slideUp .3s ease-out
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: .6s
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0)
        }

        .order-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #fff;
            border: none;
            padding: 16px 32px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: .5px;
            cursor: pointer;
            transition: .3s;
            width: 100%
        }

        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, .4)
        }

        .back-btn {
            background: rgba(255, 255, 255, .1);
            border: none;
            padding: 12px 16px;
            border-radius: 12px;
            color: #fff;
            cursor: pointer;
            transition: .3s;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, .2)
        }

        @media (max-width: 768px) {
            .category-card {
                height: 150px
            }

            .modal-content {
                margin: 0;
                border-radius: 20px 20px 0 0;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                max-height: 70vh
            }

            .nav-menu {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: rgba(0, 0, 0, .98);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 2rem;
                backdrop-filter: blur(10px);
                z-index: 999
            }

            .nav-menu.active {
                display: flex
            }

            .nav-menu a {
                font-size: 1.5rem;
                font-weight: 300
            }

            .mobile-menu-toggle {
                display: block;
                z-index: 1001
            }

            .hero {
                height: auto;
                min-height: 100vh;
                overflow: visible;
                padding-top: 96px;
                padding-bottom: 40px;
                background-attachment: scroll
            }

            .hero-content {
                max-width: 100%;
                padding: 0 16px
            }

            .hero h1 {
                font-size: 2.5rem;
                margin-bottom: 1rem
            }

            .hero-subtitle {
                font-size: 1.1rem;
                margin-bottom: 2rem
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
                gap: 1rem
            }

            .btn-menu,
            .btn-secondary {
                width: 100%;
                max-width: 250px;
                padding: 1rem 2rem;
                font-size: 1rem
            }
        }
    </style>
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
        Siparişiniz başarıyla iletildi.</div>

    {{-- Backend’den gelen veriler --}}
    @php
        // $categories -> each has: id, name, (optional slug), products (id, name, price, description, image_url)
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
        let cart = [];            // UI senkronu (server ile senkron tutulacak)
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

        // ====== Kategori kartlarını üret ======

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
            const res = await fetch(ROUTES.checkout, {
                method: 'POST', headers: csrfHeader(), body: JSON.stringify(payload)
            }).catch(() => null);
            // Bildirim (alert yerine toast) ve sepeti temizle
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
            } catch (_) {
                // ignore
            }
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
        // Kategori başlığındaki geri butonu
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
        // Ödeme butonu eklemek istersen:
        // await fetch(ROUTES.pay, { method:'POST', headers: csrfHeader(), body: JSON.stringify({method:'cash'}) })

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
            // Server ile senkron başlat
            loadCartFromServer().then(() => {
                updateCartUI();
            });
            // FAB her zaman görünür olsun ve sayfa geri döndüğünde sepeti yenile
            try { cartFab.classList.remove('hidden'); } catch (e) { }
            window.addEventListener('pageshow', () => { loadCartFromServer().then(updateCartUI).catch(() => { }); });
            window.addEventListener('focus', () => { loadCartFromServer().then(updateCartUI).catch(() => { }); });
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    loadCartFromServer().then(updateCartUI).catch(() => { });
                }
            });
        })();
        // Olay delegasyonu: hem Blade hem JS ile basılan kartlarda çalışır
        document.addEventListener('click', (e) => {
            const card = e.target.closest('.category-card');
            if (!card) return;
            const key = (card.dataset.category || '').toString().trim();
            // If no data-category, it's a normal link to menu page → allow default navigation
            if (!key) return;
            e.preventDefault();
            showProductsSafe(key);
        });

        function showProductsSafe(categoryKey) {
            let data = CATEGORIES[categoryKey];
            if (!data) {
                // Gevşek eşleştirme (büyük/küçük, tire vs.)
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