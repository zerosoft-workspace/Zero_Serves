# Oturum Yönetimi Sistemi - Dokümantasyon

## Özellikler

### ✅ Tamamlanan Özellikler

1. **Çoklu Oturum Desteği**
   - Admin, garson ve müşteri panelleri aynı anda kullanılabilir
   - Her rol kendi paneline yönlendirilir
   - Çapraz erişim engellenir

2. **30 Dakika İnaktivite Timeout**
   - Kullanıcı 30 dakika boyunca işlem yapmazsa oturum sonlanır
   - Session ayarları güncellendi (`config/session.php`)
   - Otomatik yönlendirme sistemi

3. **Oturum Süresi Dolma Sayfası**
   - Güzel tasarımlı uyarı sayfası (`/session-expired`)
   - 5 saniye sonra ana sayfaya otomatik yönlendirme
   - Kullanıcı dostu mesajlar

4. **Tarayıcı Kapanması Kontrolü**
   - Tarayıcı kapatıldığında 2 dakika sonra oturum sonlanır
   - `beforeunload` event'i ile algılama
   - Visibility API ile tab değişikliği takibi

5. **419 Page Expired Koruması**
   - Otomatik CSRF token yenileme (25 dakikada bir)
   - Heartbeat sistemi ile sunucu iletişimi
   - Token süresi dolmadan önce yenileme

## Dosya Yapısı

### Middleware'ler
- `app/Http/Middleware/SessionActivityTracker.php` - Aktivite takibi
- `app/Http/Middleware/MultiSessionAuth.php` - Çoklu oturum yönetimi

### Controller'lar
- `app/Http/Controllers/SessionController.php` - API endpoint'leri

### JavaScript
- `public/js/session-manager.js` - Frontend oturum yönetimi

### View'lar
- `resources/views/auth/session-expired.blade.php` - Oturum dolma sayfası

### Route'lar
- `/api/csrf-token` - CSRF token yenileme
- `/api/session/status` - Oturum durumu kontrolü
- `/api/session/heartbeat` - Aktivite bildirimi
- `/api/session/browser-close` - Tarayıcı kapanma bildirimi
- `/session-expired` - Oturum dolma sayfası

## Kullanım

### 1. Migration Çalıştırma
```bash
php artisan migrate
```

### 2. Çoklu Oturum Sistemi
- **Admin Guard**: `auth:admin` - Admin kullanıcıları için ayrı session
- **Waiter Guard**: `auth:waiter` - Garson kullanıcıları için ayrı session
- **Customer Guard**: `auth:customer` - Müşteri kullanıcıları için ayrı session
- Her guard bağımsız session yönetimi

### 3. Layout Dosyalarına Eklenen Script'ler
- `session-manager.js` tüm layout'lara eklendi
- Admin, garson ve müşteri panellerinde aktif

### 4. Middleware Kayıtları
- `bootstrap/app.php` dosyasında middleware'ler kaydedildi
- Global olarak `SessionActivityTracker` aktif
- Route'larda guard-specific middleware kullanımı

## Güvenlik Özellikleri

1. **Otomatik Token Yenileme**
   - 25 dakikada bir CSRF token yenilenir
   - Form'lardaki hidden input'lar güncellenir

2. **Aktivite Takibi**
   - Mouse, keyboard, scroll aktiviteleri izlenir
   - `users` tablosunda `last_activity` kaydedilir

3. **Oturum Temizleme**
   - Tarayıcı kapanması durumunda temizlik
   - localStorage ve sessionStorage temizlenir

## Test Senaryoları

### 1. İnaktivite Testi
- Herhangi bir panele giriş yapın
- 30 dakika bekleyin
- Otomatik olarak `/session-expired` sayfasına yönlendirilmelisiniz

### 2. Çoklu Oturum Testi (Gerçek Çoklu Kullanıcı)
- Admin kullanıcısı ile `http://127.0.0.1:8000/admin/login` giriş yapın
- Yeni sekmede garson kullanıcısı ile `http://127.0.0.1:8000/waiter/login` giriş yapın
- Her iki panel de aynı anda bağımsız çalışmalı
- Farklı kullanıcılar farklı guard'larda oturum açabilir

### 3. Tarayıcı Kapanma Testi
- Panele giriş yapın
- Tarayıcıyı kapatın
- 2 dakika sonra tekrar açın
- Oturum sonlanmış olmalı

### 4. CSRF Token Testi
- Panelde 25+ dakika kalın
- Form gönderimi yapın
- 419 hatası almamalısınız

## Konfigürasyon

### Session Ayarları (`config/session.php`)
```php
'lifetime' => 30, // 30 dakika
'driver' => 'database', // Veritabanı tabanlı
```

### JavaScript Ayarları (`session-manager.js`)
```javascript
sessionTimeout: 30 * 60 * 1000, // 30 dakika
browserCloseTimeout: 2 * 60 * 1000, // 2 dakika
tokenRefreshInterval: 25 * 60 * 1000, // 25 dakika
```

## Sorun Giderme

### 1. Session Expired Sayfası Görünmüyor
- Route'ların doğru tanımlandığını kontrol edin
- Middleware'lerin aktif olduğunu kontrol edin

### 2. CSRF Token Yenilenmiyor
- `session-manager.js` dosyasının yüklendiğini kontrol edin
- Network sekmesinde API çağrılarını kontrol edin

### 3. Çoklu Oturum Çalışmıyor
- `MultiSessionAuth` middleware'inin doğru parametrelerle çağrıldığını kontrol edin
- Route gruplarında doğru middleware'lerin kullanıldığını kontrol edin

## Gelecek Geliştirmeler

1. **Real-time Bildirimler**
   - WebSocket ile anlık oturum durumu bildirimleri
   - Diğer cihazlardan giriş uyarıları

2. **Gelişmiş Güvenlik**
   - IP adresi değişikliği kontrolü
   - Şüpheli aktivite algılama

3. **Kullanıcı Deneyimi**
   - Oturum uzatma seçeneği
   - Aktivite uyarı sayacı

## Notlar

- Sistem production ortamında test edilmelidir
- Log dosyalarını düzenli olarak kontrol edin
- Performans metrikleri takip edilmelidir
