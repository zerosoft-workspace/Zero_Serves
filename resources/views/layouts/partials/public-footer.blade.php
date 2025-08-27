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
                    <li><a href="{{ url('/#anasayfa') }}">Ana Sayfa</a></li>
                    <li><a href="{{ url('/#hakkimizda') }}">Hakkımızda</a></li>
                    <li><a href="{{ route('public.menu') }}">Menü</a></li>
                    <li><a href="{{ url('/#rezervasyon') }}">Rezervasyon</a></li>
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
            <p>&copy; {{ now()->year }} Soft Food. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>