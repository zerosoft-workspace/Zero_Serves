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