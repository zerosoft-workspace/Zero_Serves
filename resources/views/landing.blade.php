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
            <a href="#" class="logo">Soft Food</a>

            <ul class="nav-menu" id="nav-menu">
                <li><a href="#anasayfa">Ana Sayfa</a></li>
                <li><a href="#hakkimizda">Hakkımızda</a></li>
                <li><a href="{{ route('public.menu') }}">Menü</a></li>
                <li><a href="#rezervasyon">Rezervasyon</a></li>
                <li><a href="#galeri">Galeri</a></li>
                <li><a href="#iletisim">İletişim</a></li>
                <li><a href="{{ route('admin.entry') }}">Admin Paneli</a></li>
                <li><a href="{{ route(name: 'waiter.entry') }}">Garson Paneli</a></li>
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
                    <p>Sadece en taze ve kaliteli malzemeleri kullanıyoruz. Her tabağımızda doğallığın tadını çıkarın.
                    </p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">👨‍🍳</div>
                    <h3>Deneyimli Şefler</h3>
                    <p>Uzman şeflerimiz yılların deneyimiyle eşsiz tatlar yaratıyor ve her yemeği sanat eserine
                        dönüştürüyor.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">🏪</div>
                    <h3>Sıcak Atmosfer</h3>
                    <p>Samimi ve unutulmaz bir ortam sunuyoruz. Aileniz ve sevdiklerinizle özel anlar yaşayın.</p>
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
                    geleneksel Türk mutfağını modern dokunuşlarla harmanlamaya devam ediyor.</p>
                <p>Her tabakta sevgiyle hazırlanan yemeklerimiz, unutulmaz bir deneyim yaşatıyor.
                    Ailenizle birlikte geçireceğiniz keyifli anların adresi olmaktan gurur duyuyoruz.</p>
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
            <div class="contact-header">
                <h2 class="fade-in">Bize Ulaşın</h2>
                <p class="fade-in">Sorularınız ve rezervasyon talepleriniz için iletişime geçin</p>
            </div>

            <div class="contact-content fade-in">
                <div class="contact-info">
                    <div class="contact-item">
                        <h4>📞 Telefon & 📧 E-posta</h4>
                        <p>
                            <a href="tel:+905551234567">+90 555 123 45 67</a><br>
                            <a href="mailto:info@softfood.com">info@softfood.com</a>
                        </p>
                    </div>
                    <div class="contact-item">
                        <h4>📍 Adres</h4>
                        <p>Merkez Mah. Lezzet Cad. No:123<br>Bandırma, Balıkesir/Türkiye</p>
                    </div>
                    <div class="contact-item">
                        <h4>🕒 Çalışma Saatleri</h4>
                        <p>
                            Pazartesi - Pazar<br>
                            10:00 - 23:00
                        </p>
                    </div>
                </div>

                <div class="map-container">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3048.8094950894443!2d27.977542315340!3d40.35316757937!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14b01e0e7e7c7c7d%3A0x7c7c7c7c7c7c7c7c!2sBand%C4%B1rma%2C%20Bal%C4%B1kesir!5e0!3m2!1str!2str!4v1632847293845!5m2!1str!2str"
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
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
                        <li><a href="#anasayfa">Ana Sayfa</a></li>
                        <li><a href="#hakkimizda">Hakkımızda</a></li>
                        <li><a href="#menu">Menü</a></li>
                        <li><a href="#rezervasyon">Rezervasyon</a></li>
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
        // Menü toggle
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("nav-menu");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        // Menü linklerine tıklandığında mobil menüyü kapat
        document.querySelectorAll(".nav-menu a").forEach((link) => {
            link.addEventListener("click", () => {
                hamburger.classList.remove("active");
                navMenu.classList.remove("active");
            });
        });

        // Navbar scroll efekti
        window.addEventListener("scroll", () => {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 100) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });

        // Intersection Observer için fade-in animasyonu
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("visible");
                    }
                });
            },
            { threshold: 0.1, rootMargin: "0px 0px -50px 0px" }
        );

        // Tüm fade-in elementlerini gözlemle
        document.querySelectorAll(".fade-in").forEach((el) => {
            observer.observe(el);
        });

        // Smooth scroll için
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
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

        history.replaceState(null, '', location.href);

    </script>
</body>

</html>