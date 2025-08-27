{{-- resources/views/reservation/create.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rezervasyon</title>

    {{-- style.css sitende zaten yüklüyse bunu kaldırabilirsin --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>

<body>
    @include('layouts.partials.public-navbar')
    {{-- Üstte hero başlık (senin kırmızı degrade stilini kullanır) --}}
    <section class="hero">
        <div class="hero-content">
            <div class="hero-subtitle">Soft Food</div>
            <h1 class="hero-title">Rezervasyon</h1>
            <p class="hero-description">Hemen masa ayırtın; ekibimiz en kısa sürede sizi arayıp onaylasın.</p>
        </div>
    </section>

    {{-- İçerik alanı --}}
    <section class="menu-section">
        <div class="container">

            <h2 class="section-title">Bilgilerinizi Girin</h2>

            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="form-card card-body">
                    <form action="{{ route('reservation.store') }}" method="POST" class="form">
                        @csrf

                        <div class="form-grid">
                            <div class="span-2">
                                <label for="name" class="sf-label">Ad Soyad</label>
                                <input type="text" id="name" name="name" class="sf-input" required>
                            </div>

                            <div class="">
                                <label for="email" class="sf-label">E-posta</label>
                                <input type="email" id="email" name="email" class="sf-input" required>
                            </div>
                            <div class="">
                                <label for="phone" class="sf-label">Telefon Numarası:</label>
                                <input type="tel" id="phone" name="phone" class="sf-input" required>
                            </div>

                            <div class="">
                                <label for="people" class="sf-label">Kişi Sayısı</label>
                                <select id="people" name="people" class="sf-select" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5+">5+</option>
                                </select>
                            </div>

                            <div>
                                <label for="date" class="sf-label">Tarih</label>
                                <input type="date" id="date" name="date" class="sf-input" required>
                            </div>

                            <div>
                                <label for="time" class="sf-label">Saat</label>
                                <input type="time" id="time" name="time" class="sf-input" required>
                            </div>

                            <div class="span-2" style="margin-top:6px;">
                                <button type="submit" class="cta-button" style="width:100%;">Rezervasyon Yap</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <p class="foot">Talebiniz, uygunluk durumuna göre onaylanacaktır.</p>
        </div>
    </section>
    @include('layouts.partials.public-footer')

    {{-- dilersen buraya site genel footerını include edebilirsin --}}
</body>

</html>