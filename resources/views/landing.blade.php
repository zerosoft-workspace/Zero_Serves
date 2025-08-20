<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SoftFood</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="{{ url('/') }}" class="logo">Soft Food</a>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="#anasayfa">Ana Sayfa</a></li>
                <li><a href="#hakkimizda">Hakkımızda</a></li>
                <li><a href="#menu">Menü</a></li>
                <li><a href="#rezervasyon">Rezervasyon</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#iletisim">İletişim</a></li>

                @auth
                    @if(auth()->user()->role === 'waiter')
                        <li><a href="{{ route('waiter.dashboard') }}">Garson Paneli</a></li>
                    @elseif(auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}">Admin Paneli</a></li>
                    @endif
                @endauth
            </ul>

            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="anasayfa">
        <div class="hero-content">
            <p class="hero-subtitle">2010'dan Beri</p>
            <h1 class="hero-title">Değişmeyen Lezzet<br />Değişmeyen Sıcaklık</h1>
            <p class="hero-description">
                Modern mutfak sanatı ile geleneksel tatları buluşturan eşsiz bir deneyim
            </p>
            <a href="#menu" class="cta-button">Menüyü İncele</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="hakkimizda">
        <div class="features-container">
            <h2 class="section-title fade-in">Neden Bizi Seçmelisiniz?</h2>

            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">🍽️</div>
                    <h3>Kaliteli Malzemeler</h3>
                    <p>Sadece en taze ve kaliteli malzemeleri kullanıyoruz.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Deneyimli Şefler</h3>
                    <p>Uzman şeflerimiz eşsiz tatlar yaratıyor.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">🏪</div>
                    <h3>Sıcak Atmosfer</h3>
                    <p>Samimi ve unutulmaz bir ortam sunuyoruz.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="info-container">
            <div class="info-content fade-in">
                <h2>Hikayemiz</h2>
                <p>2010 yılından beri müşterilerimize hizmet veren restoranımız,
                    geleneksel Türk mutfağını modern dokunuşlarla harmanlıyor.</p>
                <p>Her tabakta sevgiyle hazırlanan yemeklerimiz, unutulmaz bir deneyim yaşatıyor.</p>
                <a href="#rezervasyon" class="cta-button">Rezervasyon Yap</a>
            </div>

            <div class="info-image fade-in">
                <div class="placeholder-image">
                    <div>
                        <h3>Restoran İç Mekan</h3>
                        <p>Modern ve şık tasarım</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="iletisim">
        <div class="contact-container">
            <h2 class="fade-in">Bize Ulaşın</h2>
            <p class="fade-in">Sorularınız ve rezervasyon talepleriniz için iletişime geçin</p>

            <div class="contact-info fade-in">
                <div class="contact-item">
                    <h4>Telefon</h4>
                    <p><a href="tel:+905551234567">+90 555 123 45 67</a></p>
                </div>
                <div class="contact-item">
                    <h4>E-posta</h4>
                    <p><a href="mailto:info@lezzetduragi.com">info@lezzetduragi.com</a></p>
                </div>
                <div class="contact-item">
                    <h4>Adres</h4>
                    <p>Merkez Mah. Lezzet Cad. No:123<br />İstanbul/Türkiye</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Menü toggle
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("nav-menu");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        document.querySelectorAll(".nav-menu a").forEach((link) => {
            link.addEventListener("click", () => {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            });
        });

        window.addEventListener("scroll", () => {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 100) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) entry.target.classList.add("visible");
                });
            },
            { threshold: 0.1 }
        );
        document.querySelectorAll(".fade-in").forEach((el) => observer.observe(el));
    </script>
</body>

</html>