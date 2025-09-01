<!-- Eski navbar
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="#" class="logo">Soft Food</a>
        <ul class="nav-menu" id="nav-menu">
            <li><a href="{{ url('/#anasayfa') }}">Ana Sayfa</a></li>
            <li><a href="{{ url('/#hakkimizda') }}">Hakkımızda</a></li>
            <li><a class="active" href="{{ route('public.menu') }}">Menü</a></li>
            <li><a class="active" href="{{ route('reservation.index') }}">Rezervasyon</a></li>

            <li><a href="{{ url('/#iletisim') }}">İletişim</a></li>
            <li><a href="{{ route('admin.entry') }}">Admin Paneli</a></li>
            <li><a href="{{ route('waiter.entry') }}">Garson Paneli</a></li>
        </ul>
        <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
    </div>
</nav>
 -->
<header class="header" id="header">
    <div class="container">
        <nav class="nav">
            <div class="logo">SoftFood</div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="{{ url('/#anasayfa') }}">Anasayfa</a></li>
                <li><a href="{{ url('/#hakkimizda') }}">Hakkımızda</a></li>
                <li><a href="{{ route('public.menu') }}">Menü</a></li>
                <li><a href="{{ url('/#galeri') }}">Galeri</a></li>
                <li><a href="{{ url('/#iletisim') }}">İletişim</a></li>
            </ul>

            <div class="nav-buttons">
                <a href="{{ route('reservation.index') }}">
                    <button class="btn-reservation">REZERVASYON</button>
                </a>
                <button class="mobile-menu-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </div>
</header>