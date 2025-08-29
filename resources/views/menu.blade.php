{{-- resources/views/menu.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SoftFood | Menü</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}"><!-- .btn-menu / .btn-secondary renkleri buradan -->

    <style>
        .font-playfair {
            font-family: 'Playfair Display', serif
        }

        .font-inter {
            font-family: 'Inter', sans-serif
        }

        .menu-bg {
            background: #0d0d0d
        }

        .scroll-smooth {
            scroll-behavior: smooth
        }

        .fade-in {
            opacity: 0;
            transform: translateY(18px);
            transition: all .45s ease
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0)
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, .7), rgba(0, 0, 0, .55)), url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3') center/cover;
            min-height: 52vh
        }

        /* Görseller her zaman solda */
        .menu-grid {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 34px;
            align-items: center;
        }

        @media (max-width: 1024px) {
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 20px
            }
        }

        .menu-photo {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: 0 12px 36px rgba(0, 0, 0, .4);
            background: #0e0e0e;
            width: 82%;
            justify-self: start;
        }

        .menu-photo--ratio {
            aspect-ratio: 4/3
        }

        .menu-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        @media (max-width:1400px) {
            .menu-photo {
                width: 68%
            }
        }

        @media (max-width:1200px) {
            .menu-photo {
                width: 74%
            }
        }

        @media (max-width:1024px) {
            .menu-photo {
                width: 100%
            }
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 14px;
            justify-content: center;
            margin-bottom: 18px
        }

        .section-title .line {
            flex: 1;
            height: 2px;
            background: rgba(255, 255, 255, .14)
        }

        .section-title span {
            font-weight: 800
        }

        .menu-row {
            display: flex;
            align-items: baseline;
            gap: .5rem
        }

        .menu-name {
            font-weight: 800;
            letter-spacing: .2px;
            white-space: nowrap
        }

        .menu-dots {
            flex: 1;
            border-bottom: 1px dashed rgba(255, 255, 255, .18);
            transform: translateY(-2px)
        }

        .menu-price {
            white-space: nowrap;
            font-weight: 700;
            color: #e7e7e7
        }

        .menu-desc {
            color: #bdbdbd;
            line-height: 1.45
        }

        .li-compact {
            padding: .55rem 0
        }

        @media (min-width:768px) {
            .li-compact {
                padding: .7rem 0
            }
        }

        .btn-chip {
            padding: .5rem 1rem !important;
            font-size: .85rem !important;
            border-radius: 9999px !important;
            text-transform: none !important;
            letter-spacing: .2px !important;
        }
    </style>
</head>

<body class="menu-bg text-white min-h-screen scroll-smooth">
    @include('layouts.partials.public-navbar')

    <!-- HERO -->
    <section class="hero-section flex items-center justify-center fade-in">
        <div class="text-center px-4">
            <h1 class="font-playfair text-4xl md:text-6xl font-bold mb-4">Menümüz</h1>
            <p class="font-inter text-gray-300 text-base md:text-lg max-w-2xl mx-auto mb-6">
                Lezzetlerimizi keşfedin. Dışarıdan rezervasyon için ürün seçip “Rezervasyon Yap” butonunu
                kullanabilirsiniz.
            </p>
            <a href="#menu" class="btn-menu"><i class="fa-solid fa-list"></i> Kategorilere Git</a>
        </div>
    </section>

    <!-- MAIN -->
    <main id="menu" class="py-12 md:py-16">
        <div class="container mx-auto px-4">

            <!-- Kısayollar -->
            @if($categories->count())
                <div class="flex flex-wrap justify-center gap-2 md:gap-3 mb-10 md:mb-14">
                    @foreach($categories as $cat)
                        <a href="#cat-{{ $cat->id }}" class="btn-menu btn-chip">{{ $cat->name }}</a>
                    @endforeach
                </div>
            @endif

            <!-- Bölümler -->
            @forelse($categories as $cat)
                @php
                    // fallback isimler kategorilere sırayla eşlensin
                    $fallbacks = [
                        asset('images/menu/anayemek.jpg'),
                        asset('images/menu/salata.jpg'),
                        asset('images/menu/kahvalti.jpg'),
                        asset('images/menu/icecek.jpg'),
                        asset('images/menu/kahve.jpg'),
                        asset('images/menu/tatli.jpg'),

                    ];
                    $img = $fallbacks[$loop->index % count($fallbacks)];
                @endphp

                @if($cat->products->count())
                    <section class="mb-16 md:mb-24" id="cat-{{ $cat->id }}">
                        <div class="section-title">
                            <div class="line"></div>
                            <span class="font-playfair text-xl md:text-3xl">{{ $cat->name }}</span>
                            <div class="line"></div>
                        </div>

                        <div class="menu-grid">
                            <!-- Görsel -->
                            <div class="menu-photo menu-photo--ratio fade-in">
                                <img src="{{ $img }}" alt="{{ $cat->name }} görseli">
                            </div>

                            <!-- Liste -->
                            <div class="fade-in">
                                <ul class="divide-y divide-white/10">
                                    @foreach($cat->products as $p)
                                        @php
                                            $price = is_numeric($p->price) ? number_format((float) $p->price, 2) . ' ₺' : '—';
                                        @endphp
                                        <li class="li-compact">
                                            <div class="menu-row">
                                                <h3 class="menu-name font-playfair text-sm md:text-base">{{ $p->name }}</h3>
                                                <div class="menu-dots"></div>
                                                <div class="menu-price text-sm md:text-base">{{ $price }}</div>
                                            </div>
                                            @if($p->description)
                                                <p class="menu-desc font-inter text-xs md:text-sm mt-1.5">
                                                    {{ \Illuminate\Support\Str::limit($p->description, 130) }}
                                                </p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </section>
                @endif
            @empty
                <div class="text-center py-16">
                    <div class="text-5xl mb-3">🍽️</div>
                    <h3 class="font-playfair text-xl md:text-2xl mb-1 text-gray-400">Menü Güncelleniyor</h3>
                    <p class="font-inter text-gray-500 text-sm">Şu an menüde görüntülenecek içerik bulunamadı.</p>
                </div>
            @endforelse


            <!-- CTA -->
            <div class="text-center py-12">
                <a href="{{ route('reservation.index') }}" class="btn-secondary">
                    <i class="fa-regular fa-calendar-check"></i> Rezervasyon Yap
                </a>
            </div>

            <p class="text-center text-gray-400 font-inter text-xs md:text-sm">
                Fiyatlar ₺ (TL) cinsindendir. Alerjen bilgisi için baristaya danışınız.
            </p>
        </div>
    </main>

    @include('layouts.partials.public-footer')

    <script>
        // fade-in
        const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible') }), { threshold: .1 });
        document.querySelectorAll('.fade-in').forEach(el => io.observe(el));

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
    </script>
</body>

</html>