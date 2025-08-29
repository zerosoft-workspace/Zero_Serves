<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zerosoft - Restoran</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
</head>

<body class="loading">
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-content">
            <div class="loader-logo">ZeroSoft</div>
            <div class="loader-bar">
                <div class="loader-progress"></div>
            </div>
        </div>
    </div>

    @include('layouts.partials.public-navbar')


    <!-- Hero Section -->
    <section class="hero" id="anasayfa">
        <div class="particles" id="particles"></div>
        <div class="hero-content floating">
            <h1>HOŞ GELDİNİZ</h1>
            <p class="hero-subtitle">Lezzetin en yumuşak, en samimi hali.</p>
            <div class="hero-buttons">
                <a href="{{ route('public.menu') }}" class="btn-menu">
                    <i class="fas fa-utensils"></i>
                    MENÜ
                </a>
                <a href="#hakkimizda" class="btn-secondary">
                    <i class="fas fa-info-circle"></i>
                    KEŞFEDİN
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about restaurant-bg" id="hakkimizda">
        <div class="container">
            <div class="about-grid">
                <div class="about-image">
                    <svg class="restaurant-image" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="tableGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#8B4513;stop-opacity:1" />
                                <stop offset="50%" style="stop-color:#A0522D;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#654321;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="wallGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#2F4F4F;stop-opacity:1" />
                                <stop offset="50%" style="stop-color:#4A6741;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#2F4F4F;stop-opacity:1" />
                            </linearGradient>
                            <radialGradient id="lightGradient" cx="50%" cy="20%" r="60%">
                                <stop offset="0%" style="stop-color:#FFD700;stop-opacity:0.8" />
                                <stop offset="100%" style="stop-color:#FFD700;stop-opacity:0" />
                            </radialGradient>
                        </defs>

                        <!-- Restaurant Interior Background -->
                        <rect width="600" height="400" fill="url(#wallGradient)" />

                        <!-- Ambient Lighting -->
                        <ellipse cx="300" cy="50" rx="200" ry="100" fill="url(#lightGradient)" />

                        <!-- Floor -->
                        <rect x="0" y="300" width="600" height="100" fill="#3C2414" />

                        <!-- Tables -->
                        <ellipse cx="150" cy="280" rx="60" ry="30" fill="url(#tableGradient)" />
                        <ellipse cx="450" cy="280" rx="60" ry="30" fill="url(#tableGradient)" />
                        <ellipse cx="300" cy="320" rx="80" ry="40" fill="url(#tableGradient)" />

                        <!-- Chairs -->
                        <rect x="120" y="250" width="15" height="40" rx="7" fill="#654321" />
                        <rect x="165" y="250" width="15" height="40" rx="7" fill="#654321" />
                        <rect x="420" y="250" width="15" height="40" rx="7" fill="#654321" />
                        <rect x="465" y="250" width="15" height="40" rx="7" fill="#654321" />

                        <!-- Hanging Lights -->
                        <circle cx="150" cy="80" r="25" fill="#B8860B" opacity="0.8" />
                        <circle cx="300" cy="90" r="30" fill="#B8860B" opacity="0.8" />
                        <circle cx="450" cy="80" r="25" fill="#B8860B" opacity="0.8" />

                        <!-- Light Cords -->
                        <line x1="150" y1="40" x2="150" y2="55" stroke="#333" stroke-width="2" />
                        <line x1="300" y1="50" x2="300" y2="60" stroke="#333" stroke-width="2" />
                        <line x1="450" y1="40" x2="450" y2="55" stroke="#333" stroke-width="2" />

                        <!-- Wall Decorations -->
                        <rect x="50" y="120" width="80" height="60" fill="#1A1A1A" opacity="0.7" />
                        <rect x="470" y="110" width="90" height="70" fill="#1A1A1A" opacity="0.7" />

                        <!-- Window/Light Effect -->
                        <rect x="20" y="50" width="100" height="150" fill="rgba(255,255,255,0.1)" opacity="0.3" />
                        <rect x="480" y="60" width="100" height="140" fill="rgba(255,255,255,0.1)" opacity="0.3" />

                        <!-- Table Settings -->
                        <circle cx="130" cy="275" r="8" fill="#FF6B35" opacity="0.8" />
                        <circle cx="170" cy="285" r="6" fill="#32CD32" opacity="0.7" />
                        <circle cx="430" cy="275" r="8" fill="#FF6B35" opacity="0.8" />
                        <circle cx="470" cy="285" r="6" fill="#32CD32" opacity="0.7" />
                        <circle cx="280" cy="315" r="10" fill="#FF6B35" opacity="0.8" />
                        <circle cx="320" cy="325" r="8" fill="#32CD32" opacity="0.7" />

                        <!-- Atmospheric particles -->
                        <circle cx="100" cy="150" r="2" fill="#FFD700" opacity="0.5" />
                        <circle cx="500" cy="180" r="1.5" fill="#FFD700" opacity="0.6" />
                        <circle cx="350" cy="120" r="2.5" fill="#FFD700" opacity="0.4" />
                        <circle cx="200" cy="200" r="1" fill="#FFD700" opacity="0.7" />
                    </svg>
                </div>
                <div class="about-text">
                    <blockquote>
                        "Lezzete Hoş Geldiniz! SoftFood, dost sohbetlerinin eşlik ettiği kahvelerden, özenle hazırlanmış
                        ana yemeklere; hafif atıştırmalıklardan tatlı sürprizlere kadar günün her anında yanınızda.
                        Amacımız, sizlere sadece iyi bir menü sunmak değil; her gelişinizde kendinizi evinizde
                        hissedeceğiniz, sıcak ve huzurlu bir atmosfer yaratmak."
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Neden Biz?</h2>
            <p class="section-subtitle">Her damak tadına hitap eden lezzetler, kaliteli malzemeler ve deneyimli
                ekibimizle sizlere unutulmaz bir deneyim sunuyoruz.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3>HER DAMAK TADINA MENÜ</h3>
                    <p>Geleneksel Türk mutfağından modern dünya lezzetlerine kadar geniş menü seçeneklerimizle her zevke
                        hitap ediyoruz.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <h3>KALİTELİ MALZEMELER</h3>
                    <p>Taze ve organik ürünlerle hazırlanan yemeklerimiz, sağlıklı ve lezzetli beslenme deneyimi sunar.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3>DENEYİMLİ MUTFAK EKİBİ</h3>
                    <p>Alanında uzman şeflerimiz ve deneyimli mutfak ekibimizle her tabakta mükemmelliği hedefliyoruz.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Working Hours Section -->
    <section class="working-hours">
        <div class="container">
            <div class="working-grid">
                <div class="chef-info">
                    <p>REZERVASYON</p>
                    <h2>Çalışma Saatlerimiz</h2>
                    <div style="display: flex; gap: 1.5rem; margin-top: 3rem; flex-wrap: wrap;">
                        <a href="{{ route('public.menu') }}" class="btn-menu">MENÜ</a>
                        <a href="#iletisim" class="btn-secondary">İLETİŞİM</a>
                    </div>
                </div>
                <div class="working-schedule">
                    <div class="schedule-item">
                        <span><strong>HAFTA İÇİ</strong></span>
                        <span>09:00 - 22:00</span>
                    </div>
                    <div class="schedule-item">
                        <span><strong>CUMARTESİ PAZAR</strong></span>
                        <span>09:00 - 24:00</span>
                    </div>
                    <div class="schedule-item">
                        <span><strong>TELEFON</strong></span>
                        <span>+90 532 XXX XX XX</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews">
        <div class="container">
            <h2 class="section-title">Müşteri Yorumlarımız</h2>
            <p class="section-subtitle">Misafirlerimizin deneyimleri ve memnuniyeti bizim için en değerli geri
                bildirimlerdir.</p>

            <div class="reviews-grid">
                <div class="review-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"SoftFood'da yediğim en lezzetli yemeklerden biri! Atmosfer muhteşem, personel güler yüzlü ve
                        hizmet kalitesi gerçekten üst düzey. Kesinlikle tekrar geleceğim."</p>
                    <div style="margin-top: 1rem; font-weight: 600; color: #ff6b35;">- Ahmet K.</div>
                </div>
                <div class="review-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"Çok kaliteli bir restoran. Yemekler çok lezzetli ve sunum harika. Özellikle balık menüsü
                        mükemmel. Ailecek gitmeyi tercih ettiğimiz mekanlardan biri."</p>
                    <div style="margin-top: 1rem; font-weight: 600; color: #ff6b35;">- Ayşe M.</div>
                </div>
                <div class="review-card">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"Bandırma'da bulabileceğiniz en iyi restoranlardan biri. Hem geleneksel hem de modern lezzetleri
                        bir arada sunmaları harika. Tavsiye ederim!"</p>
                    <div style="margin-top: 1rem; font-weight: 600; color: #ff6b35;">- Mehmet S.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section 
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">1287+</div>
                    <div>GÜNLÜK ZİYARETÇİ</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">578+</div>
                    <div>AYLIK TESLİMAT</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">1440+</div>
                    <div>POZİTİF GERİ BİLDİRİM</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5+</div>
                    <div>YILLIK DENEYİM</div>
                </div>
            </div>
        </div>
    </section>
-->
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
                            Pazartesi - Cuma: 9:00 - 22:00<br>

                            Cumartesi - Pazar: 9:00 - 24:00

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


    <!-- End Stats Section
    <section class="newsletter">
        <div class="container">
            <p
                style="color: #ff6b35; font-size: 0.9rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase;">
                HABER BÜLTENİ</p>
            <h2>Haberdar Olmak İster misiniz?</h2>
            <p>Yeni menülerimiz, özel kampanyalarımız ve etkinliklerimizden haberdar olmak için e-bültenimize abone
                olun.</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="E-posta adresinizi girin...">
                <button type="submit" class="newsletter-btn">ABONE OL</button>
            </form>
        </div>
    </section>
                   -->
    @include('layouts.partials.public-footer')

    <!-- Scripts -->
    <script>
        // Loader
        window.addEventListener("load", () => {
            document.body.classList.remove("loading");
            document.getElementById("pageLoader").classList.add("hidden");
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

        // Particles
        const particlesContainer = document.getElementById("particles");
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement("div");
            particle.className = "particle";
            particle.style.width = particle.style.height = Math.random() * 6 + 4 + "px";
            particle.style.left = Math.random() * 100 + "vw";
            particle.style.animationDuration = (Math.random() * 5 + 4) + "s";
            particle.style.animationDelay = Math.random() * 5 + "s";
            particlesContainer.appendChild(particle);
        }
    </script>
</body>

</html>