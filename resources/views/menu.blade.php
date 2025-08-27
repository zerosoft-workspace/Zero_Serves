{{-- resources/views/menu.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SoftFood | Menü</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <!-- ====== Navigation (seninki) ====== -->
    @include('layouts.partials.public-navbar')

    <!-- ====== Hero ====== -->
    <section class="hero fade-in" id="menu-hero">
        <div class="hero-content">
            <p class="hero-subtitle">Soft Food</p>
            <h1 class="hero-title">Menümüz</h1>
            <p class="hero-description">Lezzetlerimizi keşfedin. Dışarıdan rezervasyon için ürün seçip “Rezervasyon Yap”
                butonunu kullanabilirsiniz.</p>
            <a href="#menu" class="cta-button">Kategorilere Git</a>
        </div>
    </section>

    <!-- ====== Menü ====== -->
    <main id="menu" class="menu-section">
        <div class="container">

            <!-- Araçlar -->
            <div class="tools fade-in">
                <div class="search">
                    🔎 <input id="search" type="search" placeholder="Ara: kahve, limonata, çay" />
                </div>

            </div>

            <!-- Kategori kısayolları -->
            @if($categories->count())
                <div class="menu-shortcuts fade-in">
                    @foreach($categories as $cat)
                        <a href="#cat-{{ $cat->id }}" class="badge-link">{{ $cat->name }}</a>
                    @endforeach
                </div>
            @endif

            <!-- Kategoriler / Ürünler (DB’den) -->
            @forelse($categories as $cat)
                @php $items = $cat->products; @endphp
                @if($items->count())
                    <h2 id="cat-{{ $cat->id }}" class="menu-category-title fade-in">{{ $cat->name }}</h2>

                    <div class="cards-grid">
                        @foreach($items as $p)
                            @php
                                // Arama için data-title/tags oluştur
                                $tags = [];
                                if (property_exists($p, 'is_vegan') && $p->is_vegan)
                                    $tags[] = 'vegan';
                                if (property_exists($p, 'is_gluten_free') && $p->is_gluten_free)
                                    $tags[] = 'glütensiz';
                                if (property_exists($p, 'is_cold') && $p->is_cold)
                                    $tags[] = 'soğuk';
                                if (property_exists($p, 'is_hot') && $p->is_hot)
                                    $tags[] = 'sıcak';
                                $dataTags = implode(' ', $tags);
                                $price = is_numeric($p->price) ? number_format((float) $p->price, 2) . ' ₺' : '—';
                            @endphp

                            <article class="card fade-in" data-title="{{ Str::lower($p->name) }}" data-tags="{{ $dataTags }}">
                                {{-- İstersen popüler etiketini ürün tablosunda bir alanla yönetebilirsin --}}
                                {{-- @if($p->is_featured ?? false) <span class="hit">Popüler</span> @endif --}}

                                <div class="content">
                                    <h3 class="title">{{ $p->name }}</h3>
                                    <p class="desc">
                                        {{ $p->description ? \Illuminate\Support\Str::limit($p->description, 140) : ' ' }}
                                    </p>
                                    <div class="meta">

                                        <div class="price">{{ $price }}</div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            @empty
                <div class="empty-state fade-in" style="text-align:center;color:var(--muted);padding:60px 0;">
                    Şu an menüde görüntülenecek içerik bulunamadı.
                </div>
            @endforelse

            <div class="fade-in" style="text-align:center;">
                <a href="{{ url('/#rezervasyon') }}" class="cta-button"
                    style="background:transparent;border:2px solid var(--primary);color:var(--primary)">Rezervasyon
                    Yap</a>
            </div>

            <p class="foot">Fiyatlar ₺ (TL) cinsindendir. Alerjen bilgisi için baristaya danışınız.</p>
        </div>
    </main>

    <!-- Footer -->

    @include('layouts.partials.public-footer')

    <script>
        // === Navbar toggle & scroll
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("nav-menu");
        hamburger?.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });
        document.querySelectorAll(".nav-menu a").forEach((link) => {
            link.addEventListener("click", () => {
                hamburger?.classList.remove("active");
                navMenu?.classList.remove("active");
            });
        });
        function onScroll() {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 100) navbar.classList.add("scrolled");
            else navbar.classList.remove("scrolled");
        }
        window.addEventListener("scroll", onScroll, { passive: true });
        onScroll();

        // === Fade-in observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add("visible"); } });
        }, { threshold: 0.1, rootMargin: "0px 0px -50px 0px" });
        document.querySelectorAll(".fade-in").forEach((el) => observer.observe(el));

        // === Smooth scroll (anchor)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const hash = this.getAttribute('href');
                if (!hash || hash === '#') return;
                e.preventDefault();
                const target = document.querySelector(hash);
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({ top: offsetTop, behavior: 'smooth' });
                }
            });
        });

        // === Arama filtresi (title + tags)
        const q = document.getElementById('search');
        const cards = Array.from(document.querySelectorAll('.card'));
        q.addEventListener('input', () => {
            const val = q.value.toLowerCase().trim();
            cards.forEach(c => {
                const t = ((c.dataset.title || '') + ' ' + (c.dataset.tags || '')).toLowerCase();
                c.style.display = t.includes(val) ? '' : 'none';
            });
        });

        // URL temiz
        history.replaceState(null, '', location.href);
    </script>
</body>

</html>