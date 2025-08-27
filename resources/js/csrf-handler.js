// CSRF Token Yönetimi ve 419 Hata Önleme Sistemi

class CSRFHandler {
    constructor() {
        this.token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.refreshInterval = 30 * 60 * 1000; // 30 dakika
        this.init();
    }

    init() {
        this.setupTokenRefresh();
        this.setupFormInterceptors();
        this.setupAjaxDefaults();
        this.handleSessionExpiry();
    }

    // CSRF token'ı otomatik yenile
    setupTokenRefresh() {
        setInterval(() => {
            this.refreshToken();
        }, this.refreshInterval);

        // Sayfa focus olduğunda da token'ı yenile
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshToken();
            }
        });
    }

    // Token yenileme
    async refreshToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateToken(data.token);
            }
        } catch (error) {
            console.warn('CSRF token yenilenemedi:', error);
        }
    }

    // Token'ı güncelle
    updateToken(newToken) {
        this.token = newToken;
        
        // Meta tag'i güncelle
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', newToken);
        }

        // Tüm hidden input'ları güncelle
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = newToken;
        });

        console.log('CSRF token güncellendi');
    }

    // Form submission'ları yakala ve token kontrolü yap
    setupFormInterceptors() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.method.toLowerCase() === 'post') {
                this.ensureTokenInForm(form);
            }
        });
    }

    // Form'da token olduğundan emin ol
    ensureTokenInForm(form) {
        let tokenInput = form.querySelector('input[name="_token"]');
        
        if (!tokenInput) {
            tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            form.appendChild(tokenInput);
        }
        
        tokenInput.value = this.token;
    }

    // AJAX istekleri için varsayılan ayarlar
    setupAjaxDefaults() {
        // Fetch API için
        const originalFetch = window.fetch;
        window.fetch = (url, options = {}) => {
            if (options.method && options.method.toUpperCase() !== 'GET') {
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = this.token;
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
            }
            return originalFetch(url, options);
        };

        // jQuery varsa
        if (window.jQuery) {
            jQuery.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': this.token
                }
            });
        }
    }

    // Session süresi dolduğunda handle et
    handleSessionExpiry() {
        document.addEventListener('DOMContentLoaded', () => {
            // 419 hatası yakalama
            window.addEventListener('unhandledrejection', (event) => {
                if (event.reason && event.reason.status === 419) {
                    this.handle419Error();
                }
            });
        });
    }

    // 419 hatası durumunda
    handle419Error() {
        const message = 'Oturumunuzun süresi dolmuş. Sayfa yenileniyor...';
        
        // Kullanıcıyı bilgilendir
        if (window.Swal) {
            Swal.fire({
                title: 'Oturum Süresi Doldu',
                text: message,
                icon: 'warning',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            alert(message);
            window.location.reload();
        }
    }

    // Manuel token yenileme
    async forceRefresh() {
        await this.refreshToken();
    }
}

// Global instance oluştur
window.csrfHandler = new CSRFHandler();

// Export for modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CSRFHandler;
}
