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
            --primary-light: #ff9655ff;
            --primary-dark: #e55a2b;
            --bg-dark: #0a0a0a;
            --bg-card: #f0d7c4ff;
            --text: #fff;
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

        .page-hero {
            padding-top: 110px;
            padding-bottom: 24px;
            background: 
                linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 245, 235, 0.6)),
                linear-gradient(45deg, rgba(255, 218, 185, 0.3) 0%, rgba(255, 228, 196, 0.2) 100%);
            border-bottom: 1px solid rgba(255, 140, 66, .15);
            backdrop-filter: blur(5px);
        }

        .category-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            height: 180px;
            cursor: pointer;
            transform: translateZ(0);
            transition: .4s cubic-bezier(.4, 0, .2, 1);
            text-decoration: none;
            color: inherit;
            display: block;
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

        .category-card.active {
            border: 2px solid var(--primary);
            box-shadow: 0 8px 25px rgba(255, 107, 53, .3);
        }

        .category-card.active::before {
            background: linear-gradient(135deg, rgba(255, 107, 53, .4), rgba(255, 140, 66, .3));
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

        .category-card.active .category-title::after {
            content: '';
            position: absolute;
            top: 10px;
            right: 20px;
            width: 24px;
            height: 24px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-card.active .category-title::before {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 20px;
            width: 24px;
            height: 24px;
            color: white;
            font-weight: bold;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
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
            background: linear-gradient(135deg, var(--primary-light), #ff8c42);
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
            background: rgba(0, 0, 0, 0.8);
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
            background: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold
        }

        @media (max-width:768px) {
            .category-card {
                height: 150px
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

            .mobile-menu-toggle {
                display: block;
                z-index: 1001
            }
        }
        /* === HERO GRİ TONU: tüm sayfaya uygula === */
:root{
  --hero-bg:
    linear-gradient(180deg, #f2f4f6 0%, #e8ebef 50%, #dde1e6 100%);
}

/* Sayfa zemini ve hero aynı gri */
body{ background: var(--hero-bg) !important; }
.page-hero{
  background: var(--hero-bg) !important;
  border-bottom: 1px solid rgba(0,0,0,.06) !important;
}

/* Ürünler bölümü ve container'lar şeffaf kalsın ki gri zemin görünsün */
main, .page-hero + main, .page-hero + main .container, section{
  background: transparent !important;
}

/* Footer da gri hero ile aynı görünsün */
footer, .footer, #public-footer{
  background: var(--hero-bg) !important;
  border-top: 1px solid rgba(0,0,0,.06) !important;
  color:#1a1a1a !important;
}
#public-footer a, .footer a, footer a{ color:#0f172a !important; }
#public-footer h3, .footer h3, footer h3{ color: var(--primary) !important; }

/* Kategori kutucukları açık görünsün (siyah film yok) */
.category-card{
  border:1px solid rgba(0,0,0,.08);
  box-shadow: 0 6px 20px rgba(0,0,0,.06);
}
.category-card::before{
  background: linear-gradient(135deg, rgba(255,255,255,.18), rgba(255,255,255,.10));
}
.category-title{
  background: linear-gradient(transparent, rgba(255,255,255,.75));
}
.category-title h3{ color:#111; }
.category-title p{ color:#333; }
.category-card.active{
  border-color: rgba(255,107,53,.55);
  box-shadow: 0 10px 28px rgba(255,107,53,.16);
}

/* (İsteğe bağlı) scroll sonrası header daha şık dursun */
.header.scrolled{
  background: rgba(0,0,0,.35) !important;
  border-bottom-color: rgba(0,0,0,.06) !important;
}
/* === NAVBAR & FOOTER METİNLERİ SİYAH === */

/* Logo ve menü linkleri siyah */
.logo{
  color:#111 !important;
  text-shadow:none !important;
}
.logo:hover{ color: var(--primary) !important; }

.nav-menu a{
  color:#111 !important;
}
.nav-menu a:hover{
  color: var(--primary) !important;
}

/* Scroll sonrası header'ı açık yap ki siyah yazılar okunaklı kalsın */
.header.scrolled{
  background: rgba(255,255,255,.88) !important;
  backdrop-filter: blur(10px) !important;
  border-bottom: 1px solid rgba(0,0,0,.06) !important;
}

/* Mobil menü açıldığında da zemin açık, yazılar siyah */
@media (max-width:768px){
  .nav-menu{
    background: rgba(255,255,255,.96) !important;
  }
  .nav-menu a{
    color:#111 !important;
  }
}

/* Footer metin ve linkleri siyah */
footer, .footer, #public-footer{ color:#111 !important; }
#public-footer a, .footer a, footer a{ color:#111 !important; }
#public-footer a:hover, .footer a:hover, footer a:hover{ color: var(--primary) !important; }
#public-footer h3, .footer h3, footer h3{ color:#111 !important; }

/* (İkonlar metin rengiyle uyumlu olsun) */
header i, footer i, .footer i, #public-footer i{ color: currentColor !important; }
/* === SoftFood turuncu, diğer metinler siyah === */
:root{ --primary:#ff6b35; }

/* Logo (header + footer içindeki .logo) turuncu */
.logo,
#public-footer .logo{
  color: var(--primary) !important;
  text-shadow: none !important;
}
.logo:hover{ color:#ff8c42 !important; }

/* Navbar linkleri siyah */
.nav-menu a{ color:#111 !important; }
.nav-menu a:hover{ color: var(--primary) !important; }

/* Header scrolled zemin açık kalsın ki siyah okunaklı olsun */
.header.scrolled{
  background: rgba(255,255,255,.88) !important;
  backdrop-filter: blur(10px) !important;
  border-bottom: 1px solid rgba(0,0,0,.06) !important;
}

/* Footer metin & linkler siyah, başlık/SoftFood turuncu */
footer, .footer, #public-footer{ color:#111 !important; }
#public-footer a, .footer a, footer a{ color:#111 !important; }
#public-footer a:hover, .footer a:hover, footer a:hover{ color: var(--primary) !important; }
#public-footer h1, #public-footer h2, #public-footer h3{ color:#111 !important; }
#public-footer .logo{ color: var(--primary) !important; } /* garanti için */

/* (İçerik kartlarını değiştirmiyoruz; siyah kart üstünde beyaz metin kalsın) */
/* === TEK YÜZEY: TÜM SAYFA HERO İLE AYNI GRİ === */
:root{
  /* Hero grisi (istersen tek tona çevir: #e9edf1) */
  --surface-bg: linear-gradient(180deg, #f2f4f6 0%, #e8ebef 50%, #dde1e6 100%);
  --surface-solid: #e9edf1; /* fallback */
}

/* Tüm sayfanın zemini */
html, body{
  background: var(--surface-bg) !important;
  background-color: var(--surface-solid) !important;
}

/* Bütün ana bölümler şeffaf olsun ki alttaki yüzey görünsün */
.page-hero,
main, section,
header, footer,
.footer, #public-footer,
.page-hero .container,
main .container,
#public-footer .footer-top,
#public-footer .footer-bottom{
  background: transparent !important;
}

/* Şerit yaratan kenarlıkları kapat */
.page-hero, footer, #public-footer{
  border: none !important;
  box-shadow: none !important;
}
.product-card { color: #111 !important; }   /* ör: siyah/metin rengi */
/* === Footer metinleri siyah === */
#public-footer,
#public-footer p,
#public-footer li,
#public-footer a,
#public-footer span,
#public-footer small,
#public-footer h1,
#public-footer h2,
#public-footer h3{
  color:#111 !important;
}

/* Footer'da gri sınıfları/opaklığı da ez */
#public-footer [class*="text-gray"],
#public-footer .opacity-50,
#public-footer .opacity-60{
  color:#111 !important;
  opacity:1 !important;
}

/* Link hover rengi (isteğe bağlı) */
#public-footer a:hover{ color: var(--primary) !important; }

/* === "Kahvaltılıklar" başlığını siyah yap === */
.page-hero h2{
  color:#111 !important;
}

/* Altındaki "5 ürün listeleniyor" satırı gri kalsın (istersen kapat) */
.page-hero p{
  color:#6b7280 !important; /* Tailwind gray-500 */
}

/* (İsteğe bağlı) çok hafif ayraç istersen: */
/* hr, .divider{ background: rgba(0,0,0,.06) !important; height:1px; border:0; } */
/* NAV: menüyü ortaya al (masaüstü) */
@media (min-width: 769px){
  .nav{
    display: grid !important;
    grid-template-columns: 1fr auto 1fr; /* sol boşluk/logo — MENÜ — sağ boşluk/toggle */
    align-items: center;
  }
  .nav .logo{ justify-self: start; }
  .nav .nav-menu{ justify-self: center; }
  .nav .mobile-menu-toggle{ justify-self: end; }
}

/* güvenlik: menü kendi içinde ortalı dursun */
.nav-menu{ margin: 0; }

/* mobilde mevcut overlay menü kalsın */
@media (max-width: 768px){
  .nav{ display:flex !important; justify-content: space-between; }
}
/* === KATEGORİ KARTLARINI ESKİ HALİNE DÖNDÜR === */
.category-card{
  border: none !important;
  box-shadow: none !important;
}
.category-card::before{
  /* orijinal koyu film */
  background: linear-gradient(135deg, rgba(0,0,0,.3), rgba(255,107,53,.15)) !important;
  opacity: 1 !important;
}
.category-card:hover::before{ opacity: .8 !important; }

.category-img{ filter: none !important; } /* daha önce açtıysak geri al */

.category-title{
  /* orijinal alt siyah şerit */
  background: linear-gradient(transparent, rgba(0,0,0,.8)) !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  padding: 20px !important;
}

/* aktif kart stili (eski) */
.category-card.active{
  border: 2px solid var(--primary) !important;
  box-shadow: 0 8px 25px rgba(255,107,53,.3) !important;
}
.category-card.active::before{
  background: linear-gradient(135deg, rgba(255,107,53,.4), rgba(255,140,66,.3)) !important;
}

/* metinleri sadeleştir (stroke/shadow kaldır) ve beyaz bırak */
.category-title h3,
.category-title p{
  color:#fff !important;
  text-shadow: none !important;
  -webkit-text-stroke: 0 !important;
}

/* === KART ÜSTÜ YAZILARI KÜÇÜLT === */
.category-title h3{ font-size: .95rem !important; font-weight: 700; }
.category-title p{ font-size: .75rem !important; opacity: .9; }

/* mobilde bir kademe daha küçült */
@media (max-width:640px){
  .category-title{ padding: 12px 12px 10px !important; }
  .category-title h3{ font-size: .9rem !important; }
  .category-title p{ font-size: .7rem !important; }
}
@media (max-width:640px){
  html{ font-size:15px; } /* genel ölçü bir tık küçülsün */
  /* ÜRÜN KARTLARI */
  .product-card{ border-radius:14px; }
  .product-card > div{ /* p-4'ü kompaktla */
    padding:12px !important;
    gap:12px !important;
  }
  .product-card h3{ font-size:1rem !important; }       /* başlık */
  .product-card p{ font-size:.9rem !important; }       /* açıklama */
  .product-card .text-orange-500{ font-size:1rem !important; } /* fiyat */

  /* Miktar ve butonlar */
  .quantity-control{ gap:8px; padding:4px; }
  .quantity-btn{ width:28px; height:28px; font-size:14px; }
  .add-btn{ padding:8px 14px; font-size:.9rem; border-radius:20px; }
}
/* === FOOTER: tüm metinler siyah === */
footer, .footer, #public-footer { color:#111 !important; }

/* Footer içindeki tüm tipografi siyah */
#public-footer p,
#public-footer li,
#public-footer a,
#public-footer span,
#public-footer small,
#public-footer h1,
#public-footer h2,
#public-footer h3,
#public-footer h4 {
  color:#111 !important;
}

/* Tailwind'in gri ve opacity yardımcılarını ez */
#public-footer [class*="text-gray"] { color:#111 !important; }
#public-footer [class*="opacity-"] { opacity:1 !important; }

/* İkonlar metin rengine uysun */
#public-footer i,
#public-footer svg { color: currentColor !important; fill: currentColor !important; }

/* Logo turuncu kalsın (isteğe bağlı) */
#public-footer .logo { color: var(--primary) !important; }

/* Link hover (isteğe bağlı) */
#public-footer a:hover { color: var(--primary) !important; }
/* === Sepet modalındaki kartlar menü kartı ile aynı dursun === */
#cartModal .product-card{
  background: var(--bg-card) !important;
  border: 1px solid var(--border) !important;
  border-radius: 16px !important;
  box-shadow: 0 12px 30px rgba(0,0,0,.35) !important;
  transform: none !important;          /* hover animasyonunu kapat */
}
#cartModal .product-card:hover{ transform: none !important; }

#cartModal .product-card h4{ color:#fff !important; }
#cartModal .product-card p{ margin:0 !important; }

/* Miktar kontrolü menü ile tutarlı görünsün */
#cartModal .quantity-control{
  background: rgba(255,255,255,.06) !important;
  padding: 6px 6px !important;
  gap: 10px !important;
}
#cartModal .quantity-btn{
  width: 32px; height: 32px;
  border-radius: 50%;
  background: var(--primary) !important;
  color:#fff !important; font-weight:700;
}

/* Modal gövdesi menüye yakışsın (isteğe bağlı) */
#cartModal .modal-content{
  background: var(--bg-card) !important;   /* koyu modal */
  border: 1px solid var(--border) !important;
  color:#fff !important;
}
#cartModal .modal-content h3,
#cartModal .modal-content span{ color:#fff !important; }
#cartModal .modal-content .add-btn{ background: linear-gradient(135deg, var(--primary), #ff8c42) !important; }
#cartModal .modal-content .back-chip{ color:#fff !important; }

    </style>
</head>

<body class="font-inter">

    {{-- Header --}}
    <header id="header" class="header">
        <nav class="nav container">
            <a href="#" class="logo">SoftFood</a>
            <ul id="navMenu" class="nav-menu">
        <li><a href="{{ route('customer.table.token', ['token' => $table->token, 'view'=>'dashboard']) }}">Menü</a></li>
                <li><a href="{{ route('customer.cart.view', ['token' => $table->token]) }}">Sepetim</a></li>
                <li><a
                        href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'orders']) }}">Siparişlerim</a>
                </li>
            </ul>
            <button class="mobile-menu-toggle" id="mobileToggle"><i class="fas fa-bars"></i></button>
        </nav>
    </header>

    {{-- Sayfa Hero / başlık --}}
    <section class="page-hero">
        <div class="container mx-auto px-4">
 

            {{-- Kategoriler Grid --}}
            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-6">
                @foreach(($categories ?? []) as $category)
                    @php 
                        $slug = $category->slug ?? \Illuminate\Support\Str::slug($category->name);
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
                    <a href="{{ route('customer.menu', ['token' => $table->token, 'category' => $slug]) }}"
                        class="category-card {{ $category->id == $activeCat->id ? 'active' : '' }}">
                        <img src="{{ $img }}" alt="{{ $category->name }}" class="category-img">
                        <div class="category-title">
                            <h3 class="font-playfair text-sm font-bold">{{ $category->name }}</h3>
                            <p class="text-xs text-gray-300">{{ $category->products->count() }} ürün</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="text-center">
                <h2 class="font-playfair text-xl font-bold">{{ $activeCat->name }}</h2>
                <p class="text-gray-400 mt-1">{{ $activeCat->products->count() }} ürün listeleniyor</p>
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
    <div id="nameModal" class="modal">
        <div class="modal-content">
            <h3 class="font-playfair text-xl font-bold mb-2">Sipariş için adınızı giriniz</h3>
            <p class="text-gray-400 text-sm mb-3">Aynı masada birden fazla kişi sipariş verebilir. Hazırlık ve servis için isminize ihtiyaç duyuyoruz.</p>
            <input id="nameInput" type="text" class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3" placeholder="Örn: Mehmet" maxlength="100" />
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
    <style>
/* === MENU SAYFASI FOOTER OVERRIDES === */
/* Arka planı hero ile aynı (istersen transparent yap) ve tüm yazılar siyah */
.footer{
  background: var(--hero-bg) !important;   /* veya: background: transparent !important; */
  color:#111 !important;
}

/* Tüm tipografi siyah */
.footer p,
.footer li,
.footer a,
.footer span,
.footer small,
.footer h1,
.footer h2,
.footer h3,
.footer h4{
  color:#111 !important;
}

/* Link hover turuncu kalsın */
.footer a:hover{ color: var(--primary) !important; }

/* Sosyal ikonlar da siyah, hover turuncu */
.footer .social-icon{ color:#111 !important; }
.footer .social-icon:hover{ color: var(--primary) !important; }

/* Alt çizgi ve alt metin */
.footer-bottom{
  border-top: 1px solid rgba(0,0,0,.08) !important;
  color:#111 !important;
}
</style>

    <script>
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
        document.addEventListener('click', (e) => {
            if (e.target.matches('.quantity-btn.plus')) {
                const id = e.target.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const cur = Math.max(1, parseInt(span.textContent || '1', 10) + 1);
                span.textContent = cur;
            }
            if (e.target.matches('.quantity-btn.minus')) {
                const id = e.target.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const cur = Math.max(1, parseInt(span.textContent || '1', 10) - 1);
                span.textContent = cur;
            }
            if (e.target.matches('.add-btn')) {
                const id = e.target.dataset.id || e.target.closest('.add-btn')?.dataset.id;
                const span = document.querySelector('.qty-val[data-id="' + id + '"]');
                const qty = Math.max(1, parseInt(span?.textContent || '1', 10));
                addToCart(id, qty);
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
        function openNameModal(resolve) {
            const modal = document.getElementById('nameModal');
            const input = document.getElementById('nameInput');
            if (!modal || !input) { return resolve(null); }
            modal.classList.add('active');
            try{ input.value = (localStorage.getItem('customer_name') || '').trim(); }catch(_){ input.value=''; }
            input.focus();
            const confirmBtn = document.getElementById('nameConfirm');
            const cancelBtn = document.getElementById('nameCancel');
            function cleanup(){
                confirmBtn.removeEventListener('click', onConfirm);
                cancelBtn.removeEventListener('click', onCancel);
                modal.removeEventListener('click', onBackdrop);
            }
            function onConfirm(){
                const name = (input.value || '').trim();
                if (name.length < 2){ input.focus(); input.select(); return; }
                try{ localStorage.setItem('customer_name', name); }catch(_){}
                modal.classList.remove('active'); cleanup(); resolve(name);
            }
            function onCancel(){ modal.classList.remove('active'); cleanup(); resolve(null); }
            function onBackdrop(e){ if(e.target === modal){ onCancel(); } }
            confirmBtn.addEventListener('click', onConfirm);
            cancelBtn.addEventListener('click', onCancel);
            modal.addEventListener('click', onBackdrop);
        }

        function askName(){
            // Her siparişte modal açılsın, varsa isim önceden doldurulsun
            return new Promise((resolve)=>{
                openNameModal(resolve);
            });
        }

        async function checkout() {
            if (cart.length === 0) return;
            const name = await askName();
            if (!name) return;
            const payload = { 
                customer_name: name,
                items: cart.map(i => ({ product_id: i.id, quantity: i.quantity })) 
            };
            await fetch(ROUTES.checkout, { method: 'POST', headers: csrfHeader(), body: JSON.stringify(payload) }).catch(() => { });
            await loadCartFromServer(); cart = []; updateCartUI(); cartModal.classList.remove('active'); showToast('Siparişiniz başarıyla iletildi.');
        }

        function updateCartUI() {
  const cartBadge = document.getElementById('cartBadge');
  const cartItems  = document.getElementById('cartItems');
  const cartTotal  = document.getElementById('cartTotal');

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
    // MENÜDEKİ KART GÖRÜNÜMÜ
    row.className = 'product-card cart-item';
    row.innerHTML = `
      <div class="p-4 flex items-center justify-between gap-4">
        <div>
          <h4 class="font-playfair font-bold text-base md:text-lg mb-1">${item.name}</h4>
          <p class="text-orange-500 font-bold">${Number(item.price).toFixed(2)} ₺</p>
        </div>
        <div class="quantity-control">
          <button class="quantity-btn" data-action="minus" data-id="${item.id}">−</button>
          <span class="px-3 font-bold">${item.quantity}</span>
          <button class="quantity-btn" data-action="plus" data-id="${item.id}">+</button>
        </div>
      </div>
    `;
    row.querySelector('[data-action="minus"]').addEventListener('click', () => removeOne(item.id));
    row.querySelector('[data-action="plus"]').addEventListener('click', () => addToCart(item.id, 1));
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