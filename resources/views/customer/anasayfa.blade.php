<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoftFood - Dijital Menü</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        .font-playfair {
            font-family: 'Playfair Display', serif;
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #ff6b35;
            --primary-dark: #e55a2b;
            --bg-dark: #0a0a0a;
            --bg-card: #111214;
            --text: #ffffff;
            --text-muted: #a0a0a0;
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            background: var(--bg-dark);
            color: var(--text);
            overflow-x: hidden;
        }

        /* Enhanced Header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: transparent;
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(0px);
        }

        .header.scrolled {
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 107, 53, 0.2);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff6b35;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logo:hover {
            transform: scale(1.05);
            color: #ff8c42;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            align-items: center;
            margin: 0;
            padding: 0;
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .nav-menu a::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ff6b35, #ff8c42);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .nav-menu a:hover {
            color: #ff6b35;
            transform: translateY(-2px);
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: rgba(255, 107, 53, 0.2);
            color: #ff6b35;
        }

        /* Enhanced Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.1),
                    rgba(255, 107, 53, 0.08)),
                linear-gradient(rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.35)),
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
            filter: brightness(1.1) contrast(1.05) saturate(1.06);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 80%,
                    rgba(255, 177, 133, 0.1) 0%,
                    transparent 55%),
                radial-gradient(circle at 80% 20%,
                    rgba(255, 140, 66, 0.08) 0%,
                    transparent 55%);
            z-index: 1;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: -10% -10% -20% -10%;
            background: radial-gradient(ellipse at 50% 45%,
                    rgba(255, 255, 255, 0.12) 0%,
                    rgba(255, 255, 255, 0.06) 35%,
                    transparent 70%);
            z-index: 1;
            pointer-events: none;
        }

        .hero-content {
            animation: heroFadeIn 1.5s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
            position: relative;
            max-width: 900px;
        }

        .hero h1 {
            font-size: clamp(2.6rem, 8vw, 5.2rem);
            font-weight: 700;
            margin-bottom: 1.6rem;
            letter-spacing: 2px;
            color: #ffffff;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: clamp(1.02rem, 3vw, 1.45rem);
            margin-bottom: 2.5rem;
            color: #ffffff;
            opacity: 0.95;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .hero-buttons {
            display: flex;
            gap: 1.2rem;
            justify-content: center;
            flex-wrap: wrap;
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
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-menu:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.6);
            background: linear-gradient(135deg, #e55a2b, #ff6b35);
        }

        .btn-secondary {
            background: transparent;
            color: #fff;
            padding: 1rem 2.5rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-secondary:hover {
            border-color: #ff6b35;
            color: #ff6b35;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.2);
            background: rgba(255, 107, 53, 0.1);
        }

        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            height: 180px;
            cursor: pointer;
            transform: translateZ(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(255, 107, 53, 0.15));
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
        }

        .category-card:hover::before {
            opacity: 0.8;
        }

        .category-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .category-card:hover .category-img {
            transform: scale(1.1);
        }

        .category-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            z-index: 3;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        }

        .product-card {
            background: var(--bg-card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 107, 53, 0.3);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .add-btn {
            background: linear-gradient(135deg, var(--primary), #ff8c42);
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .add-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
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
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 8px 30px rgba(255, 107, 53, 0.4);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .cart-fab:hover {
            transform: scale(1.1);
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff0000;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 24px;
            width: 100%;
            max-width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            border: 1px solid var(--border);
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 25px;
            padding: 6px;
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
            font-weight: bold;
        }

        .slide-up {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .order-btn {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            padding: 12px 16px;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .category-card {
                height: 150px;
            }

            .modal-content {
                margin: 0;
                border-radius: 20px 20px 0 0;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                max-height: 70vh;
            }

            .nav-menu {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background: rgba(0, 0, 0, 0.98);
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 2rem;
                backdrop-filter: blur(10px);
                z-index: 999;
            }

            .nav-menu.active {
                display: flex;
            }

            .nav-menu a {
                font-size: 1.5rem;
                font-weight: 300;
            }

            .mobile-menu-toggle {
                display: block;
                z-index: 1001;
            }

            .nav-buttons {
                gap: 0.5rem;
            }

            .hero {
                height: auto;
                min-height: 100vh;
                overflow: visible;
                padding-top: 96px;
                padding-bottom: 40px;
                background-attachment: scroll;
            }

            .hero-content {
                max-width: 100%;
                padding: 0 16px;
            }

            .hero h1 {
                font-size: 2.5rem;
                margin-bottom: 1rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
                margin-bottom: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .btn-menu,
            .btn-secondary {
                width: 100%;
                max-width: 250px;
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Enhanced Header from original menu -->
    <header id="header" class="header">
        <nav class="nav container">
            <a href="#" class="logo">SoftFood</a>

            <ul id="navMenu" class="nav-menu">
                <li><a href="#home">Ana Sayfa</a></li>
                <li><a href="#menu">Menü</a></li>
                <li><a href="#about">Hakkımızda</a></li>
                <li><a href="#contact">İletişim</a></li>
            </ul>

            <div class="nav-buttons">
                <button id="backBtn" class="back-btn hidden">
                    <i class="fas fa-arrow-left"></i>
                    Geri
                </button>
                <div class="text-sm text-gray-400 ml-4">
                    <i class="fas fa-qrcode mr-2"></i>
                    Dijital Menü
                </div>
            </div>

            <button class="mobile-menu-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <!-- Hero Section -->
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
                <a href="#about" class="btn-secondary">
                    <i class="fas fa-info-circle"></i> Hakkımızda
                </a>
            </div>
        </div>
    </section>

    <!-- Ana Kategori Görünümü -->
    <main id="categoryView" class="container mx-auto px-4 py-6">
        <div id="menu" class="text-center mb-8">
            <h2 class="font-playfair text-3xl font-bold mb-2">Menümüzü Keşfedin</h2>
            <p class="text-gray-400">Kategori seçerek ürünleri görüntüleyebilir ve sipariş verebilirsiniz</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="category-card fade-in" data-category="yemek">
                <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400&h=300&fit=crop" alt="Yemek"
                    class="category-img">
                <div class="category-title">
                    <h3 class="font-playfair text-xl font-bold">Yemekler</h3>
                    <p class="text-sm text-gray-300">Ana yemekler</p>
                </div>
            </div>

            <div class="category-card fade-in" data-category="tatli" style="animation-delay: 0.1s">
                <img src="https://images.unsplash.com/photo-1551024601-bec78aea704b?w=400&h=300&fit=crop" alt="Tatlı"
                    class="category-img">
                <div class="category-title">
                    <h3 class="font-playfair text-xl font-bold">Tatlılar</h3>
                    <p class="text-sm text-gray-300">Ev yapımı tatlılar</p>
                </div>
            </div>

            <div class="category-card fade-in" data-category="icecek" style="animation-delay: 0.2s">
                <img src="https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop" alt="İçecek"
                    class="category-img">
                <div class="category-title">
                    <h3 class="font-playfair text-xl font-bold">İçecekler</h3>
                    <p class="text-sm text-gray-300">Sıcak & soğuk</p>
                </div>
            </div>

            <div class="category-card fade-in" data-category="vegan" style="animation-delay: 0.3s">
                <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&h=300&fit=crop" alt="Vegan"
                    class="category-img">
                <div class="category-title">
                    <h3 class="font-playfair text-xl font-bold">Vegan</h3>
                    <p class="text-sm text-gray-300">Bitkisel seçenekler</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Ürün Listesi Görünümü -->
    <main id="productView" class="container mx-auto px-4 py-6 hidden">
        <div class="mb-6">
            <h2 id="categoryTitle" class="font-playfair text-2xl font-bold mb-1"></h2>
            <p class="text-gray-400">Ürün seçerek sepetinize ekleyebilirsiniz</p>
        </div>

        <div id="productGrid" class="space-y-4">
            <!-- Ürünler buraya dinamik olarak eklenecek -->
        </div>
    </main>

    <!-- Sepet FAB -->
    <div id="cartFab" class="cart-fab hidden">
        <i class="fas fa-shopping-bag"></i>
        <span id="cartBadge" class="cart-badge">0</span>
    </div>

    <!-- Sepet Modal -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-playfair text-xl font-bold">Sepetim</h3>
                <button id="closeCart" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div id="cartItems" class="space-y-4 mb-6">
                <!-- Sepet ürünleri buraya eklenecek -->
            </div>

            <div class="border-t border-gray-700 pt-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="font-bold text-lg">Toplam:</span>
                    <span id="cartTotal" class="font-bold text-xl text-orange-500">0.00 ₺</span>
                </div>
                <button id="orderBtn" class="order-btn">
                    <i class="fas fa-check mr-2"></i>
                    Sipariş Ver
                </button>
            </div>
        </div>
    </div>

    <script>
        // Veri yapısı
        const categories = {
            yemek: {
                name: 'Yemekler',
                products: [
                    { id: 1, name: 'Izgara Köfte', price: 45.00, description: 'Özel baharatlarla hazırlanmış ev yapımı köfte', image: 'https://images.unsplash.com/photo-1529692236671-f1f6cf9683ba?w=300&h=200&fit=crop' },
                    { id: 2, name: 'Tavuk Şiş', price: 42.00, description: 'Marine edilmiş tavuk parçaları', image: 'https://images.unsplash.com/photo-1598515213692-d1b14ad13179?w=300&h=200&fit=crop' },
                    { id: 3, name: 'Karışık Izgara', price: 65.00, description: 'Köfte, tavuk ve et karışımı', image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=300&h=200&fit=crop' },
                    { id: 4, name: 'Mantı', price: 38.00, description: 'Ev yapımı mantı, yoğurt ve tereyağı ile', image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=300&h=200&fit=crop' },
                ]
            },
            tatli: {
                name: 'Tatlılar',
                products: [
                    { id: 11, name: 'Baklava', price: 28.00, description: 'Antep fıstıklı geleneksel baklava', image: 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=300&h=200&fit=crop' },
                    { id: 12, name: 'Künefe', price: 32.00, description: 'Sıcak künefe, dondurma ile servis', image: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&h=200&fit=crop' },
                    { id: 13, name: 'Sütlaç', price: 22.00, description: 'Ev yapımı sütlaç, tarçın ile', image: 'https://images.unsplash.com/photo-1571197119792-1d65b4c5de4a?w=300&h=200&fit=crop' },
                ]
            },
            icecek: {
                name: 'İçecekler',
                products: [
                    { id: 21, name: 'Türk Kahvesi', price: 15.00, description: 'Geleneksel Türk kahvesi', image: 'https://images.unsplash.com/photo-1544787219-7f47ccb76574?w=300&h=200&fit=crop' },
                    { id: 22, name: 'Çay', price: 8.00, description: 'Taze demlenmiş çay', image: 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&h=200&fit=crop' },
                    { id: 23, name: 'Ayran', price: 12.00, description: 'Ev yapımı ayran', image: 'https://images.unsplash.com/photo-1553909489-cd47e0ef937f?w=300&h=200&fit=crop' },
                    { id: 24, name: 'Fresh Limonata', price: 18.00, description: 'Taze sıkılmış limon suyu', image: 'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=300&h=200&fit=crop' },
                ]
            },
            vegan: {
                name: 'Vegan Seçenekler',
                products: [
                    { id: 31, name: 'Quinoa Salata', price: 35.00, description: 'Protein açısından zengin quinoa salatası', image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=200&fit=crop' },
                    { id: 32, name: 'Vegan Burger', price: 38.00, description: 'Bitki bazlı protein ile hazırlanmış burger', image: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=300&h=200&fit=crop' },
                    { id: 33, name: 'Humus Tabağı', price: 25.00, description: 'Ev yapımı humus, sebzeler ile', image: 'https://images.unsplash.com/photo-1571197119792-1d65b4c5de4a?w=300&h=200&fit=crop' },
                ]
            }
        };

        let cart = [];
        let currentCategory = null;

        // DOM elementleri
        const categoryView = document.getElementById('categoryView');
        const productView = document.getElementById('productView');
        const categoryTitle = document.getElementById('categoryTitle');
        const productGrid = document.getElementById('productGrid');
        const cartFab = document.getElementById('cartFab');
        const cartBadge = document.getElementById('cartBadge');
        const cartModal = document.getElementById('cartModal');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const backBtn = document.getElementById('backBtn');

        // Kategori kartlarına tıklama olayı
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.dataset.category;
                showProducts(category);
            });
        });

        // Header Scroll Effect
        window.addEventListener("scroll", () => {
            const header = document.getElementById("header");
            header.classList.toggle("scrolled", window.scrollY > 50);
        });

        // Mobile Menu
        const mobileToggle = document.getElementById("mobileToggle");
        const navMenu = document.getElementById("navMenu");

        mobileToggle.addEventListener("click", () => {
            navMenu.classList.toggle("active");
        });

        document.querySelectorAll('#navMenu a').forEach(a => {
            a.addEventListener('click', () => navMenu.classList.remove('active'));
        });

        // Hero menu button
        document.getElementById('heroMenuBtn').addEventListener('click', (e) => {
            e.preventDefault();
            document.getElementById('menu').scrollIntoView({
                behavior: 'smooth'
            });
        });

        // Smooth scrolling for nav links
        document.querySelectorAll('.nav-menu a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Ürünleri göster
        function showProducts(category) {
            currentCategory = category;
            const categoryData = categories[category];

            categoryTitle.textContent = categoryData.name;
            categoryView.classList.add('hidden');
            productView.classList.remove('hidden');
            backBtn.classList.remove('hidden');

            productGrid.innerHTML = '';

            categoryData.products.forEach((product, index) => {
                const productCard = createProductCard(product);
                productGrid.appendChild(productCard);

                // Animasyon gecikmesi
                setTimeout(() => {
                    productCard.classList.add('visible');
                }, index * 100);
            });
        }

        // Ürün kartı oluştur
        function createProductCard(product) {
            const div = document.createElement('div');
            div.className = 'product-card fade-in';
            div.innerHTML = `
                <div class="flex gap-4 p-4">
                    <img src="${product.image}" alt="${product.name}" 
                         class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1">
                        <h3 class="font-playfair font-bold text-lg mb-1">${product.name}</h3>
                        <p class="text-gray-400 text-sm mb-2">${product.description}</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg text-orange-500">${product.price.toFixed(2)} ₺</span>
                            <button class="add-btn" onclick="addToCart(${product.id})">
                                <i class="fas fa-plus"></i>
                                Ekle
                            </button>
                        </div>
                    </div>
                </div>
            `;
            return div;
        }

        // Sepete ekle
        function addToCart(productId) {
            const product = findProductById(productId);
            if (!product) return;

            const existingItem = cart.find(item => item.id === productId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...product, quantity: 1 });
            }

            updateCartUI();
            showCartFab();
        }

        // Ürünü ID ile bul
        function findProductById(id) {
            for (const category of Object.values(categories)) {
                const product = category.products.find(p => p.id === id);
                if (product) return product;
            }
            return null;
        }

        // Sepet UI güncelle
        function updateCartUI() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            cartBadge.textContent = totalItems;
            cartTotal.textContent = totalPrice.toFixed(2) + ' ₺';

            cartItems.innerHTML = '';
            cart.forEach(item => {
                const cartItem = createCartItem(item);
                cartItems.appendChild(cartItem);
            });

            if (totalItems === 0) {
                cartItems.innerHTML = '<p class="text-gray-400 text-center py-8">Sepetiniz boş</p>';
                cartFab.classList.add('hidden');
            }
        }

        // Sepet ürünü oluştur
        function createCartItem(item) {
            const div = document.createElement('div');
            div.className = 'flex items-center gap-4 bg-gray-800 rounded-lg p-3';
            div.innerHTML = `
                <img src="${item.image}" alt="${item.name}" 
                     class="w-12 h-12 rounded-lg object-cover">
                <div class="flex-1">
                    <h4 class="font-medium">${item.name}</h4>
                    <p class="text-orange-500 font-bold">${item.price.toFixed(2)} ₺</p>
                </div>
                <div class="quantity-control">
                    <button class="quantity-btn" onclick="changeQuantity(${item.id}, -1)">−</button>
                    <span class="px-3 font-bold">${item.quantity}</span>
                    <button class="quantity-btn" onclick="changeQuantity(${item.id}, 1)">+</button>
                </div>
            `;
            return div;
        }

        // Miktar değiştir
        function changeQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (!item) return;

            item.quantity += change;

            if (item.quantity <= 0) {
                cart = cart.filter(cartItem => cartItem.id !== productId);
            }

            updateCartUI();
        }

        // Sepet FAB göster
        function showCartFab() {
            if (cart.length > 0) {
                cartFab.classList.remove('hidden');
            }
        }

        // Geri dön
        backBtn.addEventListener('click', () => {
            categoryView.classList.remove('hidden');
            productView.classList.add('hidden');
            backBtn.classList.add('hidden');
        });

        // Sepet modal
        cartFab.addEventListener('click', () => {
            cartModal.classList.add('active');
        });

        document.getElementById('closeCart').addEventListener('click', () => {
            cartModal.classList.remove('active');
        });

        // Sipariş ver
        document.getElementById('orderBtn').addEventListener('click', () => {
            if (cart.length === 0) return;

            alert('Siparişiniz başarıyla alındı! Teşekkür ederiz.');
            cart = [];
            updateCartUI();
            cartModal.classList.remove('active');
        });

        // Modal dışına tıklayınca kapat
        cartModal.addEventListener('click', (e) => {
            if (e.target === cartModal) {
                cartModal.classList.remove('active');
            }
        });

        // Fade-in animasyonu
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Tüm fade-in elementleri observe et
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>

</html>