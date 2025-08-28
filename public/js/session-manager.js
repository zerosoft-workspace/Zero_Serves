/**
 * Oturum Yönetimi ve CSRF Token Yenileme Sistemi
 * - 419 Page Expired hatalarını önler
 * - Tarayıcı kapanması durumunda oturum sonlandırır
 * - Otomatik token yenileme
 */

class SessionManager {
    constructor() {
        this.tokenRefreshInterval = null;
        this.activityTimeout = null;
        this.isActive = true;
        this.lastActivity = Date.now();
        this.sessionTimeout = 30 * 60 * 1000; // 30 dakika
        this.browserCloseTimeout = 2 * 60 * 1000; // 2 dakika
        
        this.init();
    }

    init() {
        this.setupTokenRefresh();
        this.setupActivityTracking();
        this.setupBeforeUnloadHandler();
        this.setupVisibilityChangeHandler();
        this.setupHeartbeat();
    }

    /**
     * CSRF Token'ı otomatik olarak yeniler
     */
    setupTokenRefresh() {
        // Her 25 dakikada bir token'ı yenile (30 dakikalık session'dan önce)
        this.tokenRefreshInterval = setInterval(() => {
            this.refreshCSRFToken();
        }, 25 * 60 * 1000);

        // Sayfa yüklendiğinde de token'ı yenile
        this.refreshCSRFToken();
    }

    /**
     * Kullanıcı aktivitesini takip eder
     */
    setupActivityTracking() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.updateActivity();
            }, { passive: true });
        });

        // İnaktivite kontrolü
        this.checkInactivity();
    }

    /**
     * Tarayıcı kapanması durumunu yakalar
     */
    setupBeforeUnloadHandler() {
        window.addEventListener('beforeunload', (e) => {
            // Tarayıcı kapanıyor, oturum sonlandırma işaretini koy
            localStorage.setItem('browser_closing', Date.now().toString());
            
            // Sync request ile oturum sonlandır
            navigator.sendBeacon('/api/session/browser-close', JSON.stringify({
                _token: this.getCSRFToken(),
                timestamp: Date.now()
            }));
        });
    }

    /**
     * Sayfa görünürlük değişikliklerini takip eder
     */
    setupVisibilityChangeHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // Sayfa gizlendi (tab değişti veya minimize edildi)
                localStorage.setItem('page_hidden_time', Date.now().toString());
            } else {
                // Sayfa tekrar görünür oldu
                this.checkBrowserCloseTimeout();
            }
        });

        // Sayfa yüklendiğinde de kontrol et
        window.addEventListener('load', () => {
            this.checkBrowserCloseTimeout();
        });
    }

    /**
     * Sunucu ile düzenli iletişim kurar
     */
    setupHeartbeat() {
        // Her 5 dakikada bir sunucuya ping at
        setInterval(() => {
            if (this.isActive) {
                this.sendHeartbeat();
            }
        }, 5 * 60 * 1000);
    }

    /**
     * Kullanıcı aktivitesini günceller
     */
    updateActivity() {
        this.lastActivity = Date.now();
        this.isActive = true;
        
        // Activity timeout'unu sıfırla
        if (this.activityTimeout) {
            clearTimeout(this.activityTimeout);
        }
        
        this.checkInactivity();
    }

    /**
     * İnaktivite kontrolü yapar
     */
    checkInactivity() {
        this.activityTimeout = setTimeout(() => {
            this.isActive = false;
            this.handleSessionExpired();
        }, this.sessionTimeout);
    }

    /**
     * Tarayıcı kapanma timeout'unu kontrol eder
     */
    checkBrowserCloseTimeout() {
        const browserClosingTime = localStorage.getItem('browser_closing');
        const pageHiddenTime = localStorage.getItem('page_hidden_time');
        
        if (browserClosingTime) {
            const timeDiff = Date.now() - parseInt(browserClosingTime);
            
            // 2 dakikadan fazla geçmişse oturumu sonlandır
            if (timeDiff > this.browserCloseTimeout) {
                this.handleSessionExpired();
                return;
            }
        }
        
        if (pageHiddenTime) {
            const timeDiff = Date.now() - parseInt(pageHiddenTime);
            
            // 2 dakikadan fazla gizli kalmışsa kontrol et
            if (timeDiff > this.browserCloseTimeout) {
                this.checkSessionStatus();
            }
        }
        
        // Temizle
        localStorage.removeItem('browser_closing');
        localStorage.removeItem('page_hidden_time');
    }

    /**
     * CSRF Token'ı yeniler
     */
    async refreshCSRFToken() {
        try {
            const response = await fetch('/api/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                
                // Meta tag'ı güncelle
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                
                // Tüm formlardaki hidden input'ları güncelle
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.token;
                });
                
                console.log('CSRF token yenilendi');
            }
        } catch (error) {
            console.error('CSRF token yenileme hatası:', error);
        }
    }

    /**
     * Sunucuya heartbeat gönderir
     */
    async sendHeartbeat() {
        try {
            const response = await fetch('/api/session/heartbeat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    timestamp: Date.now()
                })
            });

            if (!response.ok) {
                if (response.status === 401) {
                    this.handleSessionExpired();
                }
            }
        } catch (error) {
            console.error('Heartbeat hatası:', error);
        }
    }

    /**
     * Oturum durumunu kontrol eder
     */
    async checkSessionStatus() {
        try {
            const response = await fetch('/api/session/status', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok || response.status === 401) {
                this.handleSessionExpired();
            }
        } catch (error) {
            console.error('Session status kontrol hatası:', error);
        }
    }

    /**
     * Oturum süresi dolduğunda çalışır
     */
    handleSessionExpired() {
        // Interval'ları temizle
        if (this.tokenRefreshInterval) {
            clearInterval(this.tokenRefreshInterval);
        }
        if (this.activityTimeout) {
            clearTimeout(this.activityTimeout);
        }

        // Session expired sayfasına yönlendir
        window.location.href = '/session-expired';
    }

    /**
     * CSRF Token'ı alır
     */
    getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        return metaTag ? metaTag.getAttribute('content') : '';
    }

    /**
     * Session Manager'ı durdur
     */
    destroy() {
        if (this.tokenRefreshInterval) {
            clearInterval(this.tokenRefreshInterval);
        }
        if (this.activityTimeout) {
            clearTimeout(this.activityTimeout);
        }
    }
}

// Sayfa yüklendiğinde Session Manager'ı başlat
document.addEventListener('DOMContentLoaded', () => {
    window.sessionManager = new SessionManager();
});

// Sayfa kapatılırken temizle
window.addEventListener('beforeunload', () => {
    if (window.sessionManager) {
        window.sessionManager.destroy();
    }
});
