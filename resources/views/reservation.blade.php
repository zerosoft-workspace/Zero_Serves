{{-- resources/views/reservation/create.blade.php --}}
<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rezervasyon</title>

    {{-- site stilin --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Flatpickr (tarih-saat) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    @include('layouts.partials.public-navbar')

    {{-- Üstte hero başlık --}}
    <section class="hero">
        <div class="hero-content">
            <div class="hero-subtitle">Soft Food</div>
            <h1 class="hero-title">Rezervasyon</h1>
            <p class="hero-description">Hemen masa ayırtın; ekibimiz en kısa sürede sizi arayıp onaylasın.</p>
        </div>
    </section>

    {{-- İçerik --}}
    <section class="menu-section">
        <div class="container">

            <h2 class="section-title">Bilgilerinizi Girin</h2>

            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            @php
                // TR saatine göre varsayılanlar
                $todayIso = \Illuminate\Support\Carbon::now('Europe/Istanbul')->format('Y-m-d'); // gönderilecek değer
                $nowTime = \Illuminate\Support\Carbon::now('Europe/Istanbul')->format('H:i');   // gönderilecek değer
            @endphp

            <div class="card">
                <div class="form-card card-body">
                    <form action="{{ route('reservation.store') }}" method="POST" class="form">
                        @csrf

                        <div class="form-grid">
                            <div class="span-2">
                                <label for="name" class="sf-label">Ad Soyad</label>
                                <input type="text" id="name" name="name" class="sf-input" required>
                            </div>

                            <div>
                                <label for="email" class="sf-label">E-posta</label>
                                <input type="email" id="email" name="email" class="sf-input" required>
                            </div>

                            <div>
                                <label for="phone" class="sf-label">Telefon Numarası</label>
                                <input type="tel" id="phone" name="phone" class="sf-input" required>
                            </div>

                            <div>
                                <label for="people" class="sf-label">Kişi Sayısı</label>
                                <select id="people" name="people" class="sf-select" required>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>

                            {{-- TARİH (görünen: gg.aa.yyyy, gönderilen: Y-m-d) --}}
                            <div>
                                <label for="date" class="sf-label">Tarih</label>
                                <input type="text" id="date" name="date" class="sf-input" required
                                    value="{{ old('date', $todayIso) }}">
                            </div>

                            {{-- SAAT (görünen: 24s, gönderilen: H:i) --}}
                            <div>
                                <label for="time" class="sf-label">Saat</label>
                                <input type="text" id="time" name="time" class="sf-input" required
                                    value="{{ old('time', $nowTime) }}">
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

    {{-- Flatpickr JS + TR yerelleştirme --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script>
        (function () {
            // TR (Europe/Istanbul) "şimdi"
            function nowTR() {
                return new Date(new Date().toLocaleString('en-US', { timeZone: 'Europe/Istanbul' }));
            }
            function ymdTR(d) {
                return d.toLocaleDateString('en-CA', { timeZone: 'Europe/Istanbul' }); // YYYY-MM-DD
            }
            function hmTR(d) {
                return d.toLocaleTimeString('en-GB', { timeZone: 'Europe/Istanbul', hour: '2-digit', minute: '2-digit', hour12: false }); // HH:MM
            }

            const trLocale = flatpickr.l10ns.tr;

            // TARİH
            const fpDate = flatpickr("#date", {
                locale: trLocale,
                altInput: true,          // Kullanıcıya TR formatını göster
                altFormat: "d.m.Y",      // görünen
                dateFormat: "Y-m-d",     // gönderilen
                minDate: "today",
                defaultDate: "{{ old('date', $todayIso) }}",
            });

            // SAAT (24 saat)
            const fpTime = flatpickr("#time", {
                enableTime: true,
                noCalendar: true,
                time_24hr: true,
                dateFormat: "H:i",       // gönderilen
                defaultDate: "{{ old('time', $nowTime) }}",
            });

            // Seçilen tarih "bugün (TR)" ise saat için minTime = şu an (TR)
            function adjustMinTime() {
                const today = ymdTR(nowTR());
                const selected = document.getElementById('date').value; // Y-m-d (gönderilen değer)
                if (selected === today) {
                    const cur = hmTR(nowTR()); // HH:MM
                    fpTime.set('minTime', cur);
                    // mevcut saat daha eskiyse ileri çek
                    if (fpTime.input.value && fpTime.input.value < cur) {
                        fpTime.setDate(cur, true, 'H:i');
                    }
                } else {
                    fpTime.set('minTime', null);
                }
            }

            adjustMinTime();
            fpDate.config.onChange.push(adjustMinTime);
        })();
    </script>
</body>

</html>