// Garson Paneli JavaScript İyileştirmeleri

document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation improvements
    initMobileNavigation();
    
    // Auto-refresh for calls page
    initAutoRefresh();
    
    // Touch gestures for cards
    initTouchGestures();
    
    // Loading states
    initLoadingStates();
    
    // Notification system
    initNotifications();
});

// Mobile Navigation
function initMobileNavigation() {
    // Swipe gestures for navigation
    let startX = 0;
    let startY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        if (!startX || !startY) return;
        
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Horizontal swipe detection
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            if (diffX > 0) {
                // Swipe left - next page
                handleSwipeLeft();
            } else {
                // Swipe right - previous page
                handleSwipeRight();
            }
        }
        
        startX = 0;
        startY = 0;
    });
}

function handleSwipeLeft() {
    // Navigate to calls page from dashboard
    if (window.location.pathname.includes('/waiter/dashboard')) {
        window.location.href = '/waiter/calls';
    }
}

function handleSwipeRight() {
    // Navigate to dashboard from calls
    if (window.location.pathname.includes('/waiter/calls')) {
        window.location.href = '/waiter/dashboard';
    }
}

// Auto-refresh functionality
function initAutoRefresh() {
    if (window.location.pathname.includes('/waiter/calls')) {
        // Auto-refresh calls page every 30 seconds
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 30000);
    }
    
    if (window.location.pathname.includes('/waiter/dashboard')) {
        // Auto-refresh dashboard every 60 seconds
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                window.location.reload();
            }
        }, 60000);
    }
}

// Touch gestures for cards
function initTouchGestures() {
    const cards = document.querySelectorAll('.table-card, .call-card');
    
    cards.forEach(card => {
        let pressTimer;
        
        card.addEventListener('touchstart', function(e) {
            pressTimer = setTimeout(() => {
                // Long press action
                card.classList.add('card-pressed');
                navigator.vibrate && navigator.vibrate(50);
            }, 500);
        });
        
        card.addEventListener('touchend', function(e) {
            clearTimeout(pressTimer);
            card.classList.remove('card-pressed');
        });
        
        card.addEventListener('touchmove', function(e) {
            clearTimeout(pressTimer);
        });
    });
}

// Loading states
function initLoadingStates() {
    const forms = document.querySelectorAll('form');
    const buttons = document.querySelectorAll('button[onclick]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner me-1"></span>Yükleniyor...';
            }
        });
    });
    
    buttons.forEach(button => {
        const originalOnClick = button.onclick;
        button.onclick = function(e) {
            if (button.disabled) return false;
            
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner me-1"></span>İşleniyor...';
            
            // Restore button after 3 seconds
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 3000);
            
            if (originalOnClick) {
                return originalOnClick.call(this, e);
            }
        };
    });
}

// Notification system
function initNotifications() {
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Check for new calls periodically
    if (window.location.pathname.includes('/waiter/dashboard')) {
        checkForNewCalls();
        setInterval(checkForNewCalls, 30000);
    }
}

async function checkForNewCalls() {
    try {
        const response = await fetch('/waiter/calls/count', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            const newCallsCount = data.new_calls || 0;
            
            // Update badge in navigation
            updateCallsBadge(newCallsCount);
            
            // Show notification for new calls
            if (newCallsCount > 0 && 'Notification' in window && Notification.permission === 'granted') {
                new Notification('Yeni Garson Çağrısı', {
                    body: `${newCallsCount} yeni çağrı var`,
                    icon: '/favicon.ico',
                    tag: 'waiter-call'
                });
            }
        }
    } catch (error) {
        console.log('Call count check failed:', error);
    }
}

function updateCallsBadge(count) {
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        if (badge.textContent.includes('çağrı') || badge.classList.contains('calls-badge')) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        }
    });
}

// Sipariş durumu değiştirme fonksiyonu
async function changeStatus(url, status) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Loading state
    button.disabled = true;
    button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Güncelleniyor...';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'status': status,
                '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Başarı mesajı göster
            showStatusMessage(data.message || 'Sipariş durumu güncellendi!', 'success');
            
            // Sayfa içeriğini güncelle
            await updateTableContent();
            
        } else {
            throw new Error(data.message || 'Güncelleme başarısız');
        }
        
    } catch (error) {
        console.error('Status update error:', error);
        showStatusMessage('Bağlantı hatası: ' + error.message, 'error');
        
        // Butonu eski haline getir
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Debug için console log ekle
window.changeStatus = changeStatus;

// Çağrı tamamlama fonksiyonu
async function completeCall(callId) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Loading state
    button.disabled = true;
    button.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Tamamlanıyor...';
    
    try {
        const response = await fetch(`/waiter/calls/${callId}/respond`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                'action': 'complete',
                '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Başarı mesajı göster
            showStatusMessage(data.message || 'Çağrı tamamlandı!', 'success');
            
            // Çağrı kartını güncelle
            const callCard = button.closest('.card');
            if (callCard) {
                // Buton alanını güncelle
                const buttonContainer = button.closest('.d-grid');
                buttonContainer.innerHTML = `
                    <div class="text-center py-3 mb-2">
                        <div class="p-3 bg-success bg-opacity-10 rounded-circle d-inline-flex mb-2">
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        </div>
                        <div class="fw-semibold text-success">Tamamlandı</div>
                        <small class="text-muted">Çağrı başarıyla çözüldü</small>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCall(${callId})">
                        <i class="bi bi-trash me-1"></i>
                        <span>Sil</span>
                    </button>
                `;
                
                // Status badge'ini güncelle
                const statusBadge = callCard.querySelector('.badge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>Tamamlandı';
                }
            }
            
        } else {
            throw new Error(data.message || 'Tamamlama başarısız');
        }
        
    } catch (error) {
        console.error('Call completion error:', error);
        showStatusMessage('Bağlantı hatası: ' + error.message, 'error');
        
        // Butonu eski haline getir
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Çağrı silme fonksiyonu
async function deleteCall(callId) {
    if (!confirm('Bu çağrıyı silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Loading state
    button.disabled = true;
    button.innerHTML = '<i class="spinner-border spinner-border-sm"></i>';
    
    try {
        const response = await fetch(`/waiter/calls/${callId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            })
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Çağrı kartını DOM'dan kaldır
            const callCard = button.closest('.col-md-6');
            if (callCard) {
                callCard.style.transition = 'opacity 0.3s';
                callCard.style.opacity = '0';
                setTimeout(() => {
                    callCard.remove();
                }, 300);
            }
            
            showStatusMessage(data.message || 'Çağrı silindi!', 'success');
            
        } else {
            throw new Error(data.message || 'Silme başarısız');
        }
        
    } catch (error) {
        console.error('Call deletion error:', error);
        showStatusMessage('Silme hatası: ' + error.message, 'error');
        
        // Butonu eski haline getir
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

// Global fonksiyon olarak tanımla
window.completeCall = completeCall;
window.deleteCall = deleteCall;

// Sayfa yüklendiğinde test et
document.addEventListener('DOMContentLoaded', function() {
    console.log('Waiter.js loaded');
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
});

// Masa içeriğini AJAX ile güncelle
async function updateTableContent() {
    try {
        const currentUrl = window.location.href;
        const response = await fetch(currentUrl, {
            headers: {
                'Accept': 'text/html',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Sipariş durumu badge'ini güncelle
            const newStatusBadge = doc.querySelector('#order-status-text');
            const currentStatusBadge = document.querySelector('#order-status-text');
            if (newStatusBadge && currentStatusBadge) {
                currentStatusBadge.textContent = newStatusBadge.textContent;
                currentStatusBadge.className = newStatusBadge.className;
            }
            
            // Butonları güncelle
            const newButtons = doc.querySelectorAll('.status-flow button');
            const currentButtons = document.querySelectorAll('.status-flow button');
            
            newButtons.forEach((newBtn, index) => {
                if (currentButtons[index]) {
                    currentButtons[index].disabled = newBtn.disabled;
                    currentButtons[index].className = newBtn.className;
                    currentButtons[index].innerHTML = newBtn.innerHTML;
                    currentButtons[index].onclick = newBtn.onclick;
                }
            });
            
            // Dashboard'u da güncelle (eğer başka sekmede açıksa)
            updateDashboardIfOpen();
        }
        
    } catch (error) {
        console.error('Content update error:', error);
    }
}

// Dashboard güncellemesi
async function updateDashboardIfOpen() {
    // Dashboard sayfasındaysak masaları güncelle
    if (window.location.pathname.includes('/waiter/dashboard')) {
        location.reload();
    }
}

// Durum mesajı göster
function showStatusMessage(message, type = 'success') {
    const flashDiv = document.getElementById('status-flash');
    if (!flashDiv) return;
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    flashDiv.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    flashDiv.style.display = 'block';
    
    // 5 saniye sonra otomatik gizle
    setTimeout(() => {
        const alert = flashDiv.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                flashDiv.style.display = 'none';
            }, 150);
        }
    }, 5000);
}

// Utility functions
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Export functions for global use
window.waiterPanel = {
    showToast,
    updateCallsBadge,
    checkForNewCalls
};
