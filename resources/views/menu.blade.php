{{-- resources/views/menu.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SoftFood | Menü</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- İstersen Bootstrap de ekleyebilirsin:
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

    <style>
        /* Base Mobile-First Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: transparent;
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            position: relative;
        }

        .logo {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
            z-index: 1001;
        }

        .navbar.scrolled .logo {
            color: #333;
            text-shadow: none;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2.5rem;
            background: transparent;
        }

        .nav-menu a {
            text-decoration: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .navbar.scrolled .nav-menu a {
            color: #333;
            text-shadow: none;
        }

        .nav-menu a:hover {
            color: #e74c3c;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .navbar.scrolled .nav-menu a:hover {
            background: rgba(231, 76, 60, 0.1);
        }

        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1001;
        }

        .hamburger span {
            width: 28px;
            height: 3px;
            background: #ffffff;
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .navbar.scrolled .hamburger span {
            background: #333;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-8px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-8px, -6px);
        }


        /* Mobile Menu */
        .nav-menu.active {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 70px;
            left: 0;
            width: 100%;
            background: white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
            gap: 15px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #d4332a 0%, #b8271f 100%);
            color: white;
            padding: 120px 20px 80px;
            text-align: center;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .hero-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-description {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .cta-button {
            display: inline-block;
            background: white;
            color: #d4332a;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        /* Menu Section */
        .menu-section {
            padding: 80px 0;
            background: #f8f9fa;
        }

        /* Menu Shortcuts */
        .menu-shortcuts {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 60px;
            padding: 0 20px;
        }

        .badge-link {
            background: white;
            color: #333;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }

        .badge-link:hover {
            background: #d4332a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 51, 42, 0.3);
        }

        /* Category Titles */
        .menu-category-title {
            font-size: 2rem;
            color: #333;
            text-align: center;
            margin: 60px 0 40px;
            position: relative;
        }

        .menu-category-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #d4332a;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
            margin-bottom: 60px;
        }

        .card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-image img {
            transform: scale(1.1);
        }

        .card-body {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
            margin: 0;
            flex: 1;
            min-width: 200px;
        }

        .price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #d4332a;
            background: #f8f9fa;
            padding: 5px 15px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .card-text {
            color: #666;
            line-height: 1.6;
            flex-grow: 1;
        }

        /* Buttons */
        .btn-outline {
            display: inline-block;
            background: transparent;
            color: #d4332a;
            padding: 15px 40px;
            border: 2px solid #d4332a;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
            margin: 40px auto;
            max-width: 300px;
        }

        .btn-outline:hover {
            background: #d4332a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 51, 42, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #666;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 0 20px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3,
        .footer-section h4 {
            margin-bottom: 20px;
            color: #ecf0f1;
        }

        .footer-section p {
            line-height: 1.6;
            color: #bdc3c7;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-links a {
            font-size: 1.5rem;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .social-links a:hover {
            transform: scale(1.2);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: #ecf0f1;
        }

        .footer-contact p {
            margin-bottom: 10px;
            color: #bdc3c7;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #34495e;
            color: #95a5a6;
        }

        /* Fade-in Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Tablet Styles */
        @media (min-width: 768px) {
            .nav-menu {
                display: flex;
            }

            .hamburger {
                display: none;
            }

            .hero-title {
                font-size: 3rem;
            }

            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }

            .card {
                flex-direction: row;
                min-height: 200px;
            }

            .card-image {
                width: 40%;
                height: auto;
                min-height: 200px;
            }

            .card-body {
                width: 60%;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* Desktop Styles */
        @media (min-width: 1024px) {
            .container {
                padding: 0 40px;
            }

            .nav-container {
                padding: 0 40px;
            }

            .hero {
                padding: 140px 40px 100px;
                min-height: 500px;
            }

            .hero-title {
                font-size: 3.5rem;
            }

            .cards-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .card {
                flex-direction: column;
                min-height: auto;
            }

            .card-image {
                width: 100%;
                height: 220px;
            }

            .card-body {
                width: 100%;
            }

            .footer-content {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Large Desktop */
        @media (min-width: 1200px) {
            .cards-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .card-image {
                height: 200px;
            }
        }

        /* High DPI Displays */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .card-image img {
                image-rendering: -webkit-optimize-contrast;
            }
        }

        /* Print Styles */
        @media print {

            .navbar,
            .hamburger,
            .cta-button,
            .btn-outline,
            .footer {
                display: none;
            }

            .hero {
                background: none;
                color: black;
                padding: 20px;
            }

            .card {
                break-inside: avoid;
                margin-bottom: 20px;
            }
        }

        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            /* Dark mode styles removed - keeping light theme only */
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="#" class="logo">Soft Food</a>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="{{ url('/#anasayfa') }}">Ana Sayfa</a></li>
                <li><a href="{{ url('/#hakkimizda') }}">Hakkımızda</a></li>
                <li><a class="active" href="{{ route('public.menu') }}">Menü</a></li>
                <li><a href="{{ url('/#rezervasyon') }}">Rezervasyon</a></li>
                <li><a href="{{ url('/#galeri') }}">Galeri</a></li>
                <li><a href="{{ url('/#iletisim') }}">İletişim</a></li>
                <li><a href="{{ route('admin.entry') }}">Admin Paneli</a></li>
                <li><a href="{{ route('waiter.entry') }}">Garson Paneli</a></li>
            </ul>

            <div class="hamburger" id="hamburger">
                <span></span><span></span><span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero fade-in" id="menu-hero">
        <div class="hero-content">
            <p class="hero-subtitle">Soft Food</p>
            <h1 class="hero-title">Menümüz</h1>
            <p class="hero-description">Lezzetlerimizi keşfedin. Dışarıdan rezervasyon için ürün seçip "Rezervasyon Yap"
                butonunu kullanabilirsiniz.</p>
            <a href="#menu" class="cta-button">Kategorilere Git</a>
        </div>
    </section>

    <!-- Menu Content -->
    <section id="menu" class="menu-section">
        <div class="container">

            @if($categories->count())
                <div class="menu-shortcuts fade-in">
                    @foreach($categories as $cat)
                        <a href="#cat-{{ $cat->id }}" class="badge-link">{{ $cat->name }}</a>
                    @endforeach
                </div>
            @endif

            @forelse($categories as $cat)
                @php $items = $cat->products; @endphp
                @if($items->count())
                    <h2 id="cat-{{ $cat->id }}" class="menu-category-title fade-in">{{ $cat->name }}</h2>

                    <div class="cards-grid">
                        @foreach($items as $p)
                            <article class="card fade-in">
                                @php
                                    $src = $p->image ? asset('storage/' . $p->image) : asset('images/placeholder/food.jpg');
                                @endphp
                                <div class="card-image">
                                    <img src="{{ $src }}" alt="{{ $p->name }}" loading="lazy">
                                </div>

                                <div class="card-body">
                                    <div class="card-head">
                                        <h3 class="card-title">{{ $p->name }}</h3>
                                        <span class="price">{{ number_format($p->price, 2) }} ₺</span>
                                    </div>

                                    <p class="card-text">
                                        {{ $p->description ? \Illuminate\Support\Str::limit($p->description, 110) : ' ' }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @empty
                <div class="empty-state fade-in">Şu an menüde görüntülenecek içerik bulunamadı.</div>
            @endforelse

            <div class="fade-in" style="text-align: center;">
                <a href="{{ url('/#rezervasyon') }}" class="btn-outline">Rezervasyon Yap</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Soft Food</h3>
                    <p>2010'dan beri değişmeyen lezzet, değişmeyen sıcaklık ile hizmetinizdeyiz.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">📘</a>
                        <a href="#" aria-label="Instagram">📷</a>
                        <a href="#" aria-label="Twitter">🐦</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Hızlı Linkler</h4>
                    <ul>
                        <li><a href="{{ url('/#anasayfa') }}">Ana Sayfa</a></li>
                        <li><a href="{{ url('/#hakkimizda') }}">Hakkımızda</a></li>
                        <li><a href="{{ route('public.menu') }}">Menü</a></li>
                        <li><a href="{{ url('/#rezervasyon') }}">Rezervasyon</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>İletişim</h4>
                    <div class="footer-contact">
                        <p>📞 +90 555 123 45 67</p>
                        <p>📧 info@softfood.com</p>
                        <p>📍 Bandırma, Balıkesir</p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2024 Soft Food. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu toggle
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("nav-menu");

        hamburger?.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        // Close menu when clicking on links
        document.querySelectorAll(".nav-menu a").forEach((link) => {
            link.addEventListener("click", () => {
                hamburger?.classList.remove("active");
                navMenu?.classList.remove("active");
            });
        });

        // Close menu when clicking outside
        document.addEventListener("click", (e) => {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger?.classList.remove("active");
                navMenu?.classList.remove("active");
            }
        });

        // Navbar scroll effect
        window.addEventListener("scroll", () => {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 100) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });

        // Intersection Observer for fade-in animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px"
        });

        // Observe all fade-in elements
        document.querySelectorAll(".fade-in").forEach((el) => {
            observer.observe(el);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src || img.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                imageObserver.observe(img);
            });
        }

        // Performance optimization: throttle scroll events
        let ticking = false;
        function updateScrollEffects() {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 100) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
            ticking = false;
        }

        window.addEventListener("scroll", () => {
            if (!ticking) {
                requestAnimationFrame(updateScrollEffects);
                ticking = true;
            }
        });

        // Clean up URL
        history.replaceState(null, '', location.href);
    </script>
</body>

</html>