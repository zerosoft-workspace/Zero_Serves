{{-- resources/views/reservation/create.blade.php --}}
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rezervasyon</title>

    {{-- Site stilin --}}
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>
<body>
    @include('layouts.partials.public-navbar')

    {{-- HERO + FORM --}}
    <section class="hero" style="align-items:flex-start;padding-top:120px;">
        <div class="hero-content" style="width:100%;max-width:950px;margin:0 auto;">

            <div class="text-center" style="margin-bottom:2rem;">
            
                <h1 class="hero-title">Rezervasyon</h1>
                <p class="hero-description">Hemen masa ayırtın; ekibimiz en kısa sürede sizi arayıp onaylasın.</p>
            </div>

            {{-- Başarı / Hata Mesajları --}}
            @if (session('success'))
                <div class="alert-success" style="background:rgba(40,167,69,.12);border:1px solid rgba(40,167,69,.35);color:#c3f3d1;padding:1rem 1.25rem;border-radius:12px;margin-bottom:1.25rem;">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert-error" style="background:rgba(220,53,69,.12);border:1px solid rgba(220,53,69,.35);color:#ffd5da;padding:1rem 1.25rem;border-radius:12px;margin-bottom:1.25rem;">
                    <strong>Formda eksik veya hatalı alanlar var.</strong>
                    <ul style="margin:.5rem 0 0 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $todayIso = \Illuminate\Support\Carbon::now('Europe/Istanbul')->format('Y-m-d');
                $nowTime  = \Illuminate\Support\Carbon::now('Europe/Istanbul')->format('H:i');
            @endphp

            {{-- FORM KARTI --}}
            <div class="card form-card" style="background:rgba(0,0,0,.55);border:1px solid rgba(255,107,53,.25);border-radius:20px;backdrop-filter:blur(12px);padding:2rem;box-shadow:0 20px 50px rgba(0,0,0,.5);">
                <form action="{{ route('reservation.store') }}" method="POST" class="form" novalidate>
                    @csrf
                    <div class="form-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.25rem;">
                        <div class="span-2">
                            <label for="name" class="sf-label">Ad Soyad</label>
                            <input type="text" id="name" name="name" class="sf-input"
                                   value="{{ old('name') }}" required placeholder="Adınız Soyadınız">
                        </div>

                        <div>
                            <label for="email" class="sf-label">E-posta</label>
                            <input type="email" id="email" name="email" class="sf-input"
                                   value="{{ old('email') }}" required placeholder="ornek@mail.com">
                        </div>
                        <div>
                            <label for="phone" class="sf-label">Telefon</label>
                            <input type="tel" id="phone" name="phone" class="sf-input"
                                   value="{{ old('phone') }}" required placeholder="05xx xxx xx xx">
                        </div>

                        <div>
                            <label for="people" class="sf-label">Kişi Sayısı</label>
                            <select id="people" name="people" class="sf-select" required>
                                @for ($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" @selected(old('people') == $i)>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="date" class="sf-label">Tarih</label>
                            <input type="text" id="date" name="date" class="sf-input"
                                   value="{{ old('date', $todayIso) }}" required>
                        </div>
                        <div>
                            <label for="time" class="sf-label">Saat</label>
                            <input type="text" id="time" name="time" class="sf-input"
                                   value="{{ old('time', $nowTime) }}" required>
                        </div>

                        <div class="span-2" style="margin-top:.5rem;">
                            <button type="submit" class="cta-button" style="width:100%;">Rezervasyon Yap</button>
                        </div>
                    </div>
                </form>
            </div>

            <p class="foot" style="opacity:.9;margin-top:1rem;text-align:center;">Talebiniz, uygunluk durumuna göre onaylanacaktır.</p>
        </div>
    </section>

    @include('layouts.partials.public-footer')

    {{-- Session Manager --}}
    <script src="{{ asset('js/session-manager.js') }}"></script>
    
    {{-- Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/tr.js"></script>
    <script>
        (function () {
            function nowTR() { return new Date(new Date().toLocaleString('en-US',{timeZone:'Europe/Istanbul'})); }
            function ymdTR(d){ return d.toLocaleDateString('en-CA',{timeZone:'Europe/Istanbul'}); }
            function hmTR(d){ return d.toLocaleTimeString('en-GB',{timeZone:'Europe/Istanbul',hour:'2-digit',minute:'2-digit',hour12:false}); }

            const trLocale = flatpickr.l10ns.tr;
            const fpDate = flatpickr("#date", {
                locale: trLocale, altInput:true, altFormat:"d.m.Y", dateFormat:"Y-m-d",
                minDate:"today", defaultDate:"{{ old('date', $todayIso) }}"
            });
            const fpTime = flatpickr("#time", {
                enableTime:true, noCalendar:true, time_24hr:true, minuteIncrement:5,
                dateFormat:"H:i", defaultDate:"{{ old('time', $nowTime) }}"
            });

            function adjustMinTime(){
                const today=ymdTR(nowTR()), selected=document.getElementById('date').value;
                if(selected===today){
                    const cur=hmTR(nowTR());
                    fpTime.set('minTime',cur);
                    if(fpTime.input.value && fpTime.input.value<cur){ fpTime.setDate(cur,true,'H:i'); }
                } else fpTime.set('minTime',null);
            }
            adjustMinTime(); fpDate.config.onChange.push(adjustMinTime);
        })();

        // Rezervasyon formu için CSRF token yenileme
        const reservationForm = document.querySelector('form[action*="reservation"]');
        if (reservationForm) {
            reservationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    // Token'ı yenile
                    const response = await fetch('/api/csrf-token');
                    const data = await response.json();
                    
                    // Form'daki token'ı güncelle
                    const tokenInput = this.querySelector('input[name="_token"]');
                    if (tokenInput) {
                        tokenInput.value = data.token;
                    }
                    
                    // Meta tag'ı da güncelle
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', data.token);
                    }
                    
                    // Formu gönder
                    this.submit();
                } catch (error) {
                    console.error('CSRF token yenileme hatası:', error);
                    // Hata durumunda formu olduğu gibi gönder
                    this.submit();
                }
            });
        }
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
        document.querySelectorAll('#navMenu a').forEach(a=>{
  a.addEventListener('click', ()=> navMenu.classList.remove('active'));
});

    </script>
</body>
</html>
