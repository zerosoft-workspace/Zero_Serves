@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title mb-1">Dashboard</h1>
            <p class="text-muted mb-0">
                Hoş geldin, <strong>{{ auth()->user()->name }}</strong>! 
                <small class="text-muted">{{ $currentDate }} - {{ $currentTime }}</small>
            </p>
        </div>
        <div class="page-actions">
            <span class="badge bg-success">
                <i class="bi bi-wifi"></i> Çevrimiçi
            </span>
            @if($pendingOrders > 0)
                <span class="badge bg-danger ms-2">
                    {{ $pendingOrders }} bekleyen sipariş
                </span>
            @endif
        </div>
    </div>
@endsection

@section('content')
<div class="dashboard-header mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-muted">
            Hoş geldin, <strong>{{ auth()->user()->name }}</strong>! 
            {{ \Carbon\Carbon::now()->format('d F Y, l - H:i') }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-success">
            <i class="bi bi-circle-fill me-1"></i> Çevrimiçi
        </span>
        @if($pendingOrders > 0)
            <span class="badge bg-danger">
                {{ $pendingOrders }} bekleyen sipariş
            </span>
        @endif
    </div>
</div>
    {{-- Quick Stats --}}
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-header">
                <div class="stats-icon primary">
                    <i class="bi bi-grid-3x3-gap"></i>
                </div>
            </div>
            <div class="stats-value">{{ $totalTables }}</div>
            <div class="stats-label">Toplam Masa</div>
            <div class="stats-meta mt-2">
                <small class="text-success">
                    <i class="bi bi-people-fill"></i> 
                    {{ $activeTables }} aktif masa
                </small>
                @if($tableStatus['reserved'] > 0)
                    <br><small class="text-warning">
                        <i class="bi bi-calendar-check"></i> 
                        {{ $tableStatus['reserved'] }} rezerve
                    </small>
                @endif
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-header">
                <div class="stats-icon success">
                    <i class="bi bi-receipt"></i>
                </div>
            </div>
            <div class="stats-value">{{ $todayOrders }}</div>
            <div class="stats-label">Bugünkü Sipariş</div>
            <div class="stats-meta mt-2">
                @if($pendingOrders > 0)
                    <small class="text-warning">
                        <i class="bi bi-clock"></i> 
                        {{ $pendingOrders }} beklemede
                    </small>
                @else
                    <small class="text-success">
                        <i class="bi bi-check-circle"></i> 
                        Tüm siparişler güncel
                    </small>
                @endif
                <br><small class="text-muted">
                    <i class="bi bi-check-circle-fill"></i> 
                    {{ $completedOrders }} tamamlandı
                </small>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-header">
                <div class="stats-icon warning">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <div class="stats-value">₺{{ number_format($todayRevenue, 0, ',', '.') }}</div>
            <div class="stats-label">Günlük Ciro</div>
            <div class="stats-meta mt-2">
                @if($revenueGrowth > 0)
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i> 
                        %{{ $revenueGrowth }} artış
                    </small>
                @elseif($revenueGrowth < 0)
                    <small class="text-danger">
                        <i class="bi bi-arrow-down"></i> 
                        %{{ abs($revenueGrowth) }} azalış
                    </small>
                @else
                    <small class="text-muted">
                        <i class="bi bi-dash"></i> 
                        Değişim yok
                    </small>
                @endif
                <br><small class="text-muted">
                    Ort: ₺{{ number_format($averageOrderValue, 0) }}
                </small>
            </div>
        </div>

        <div class="stats-card">
            <div class="stats-header">
                <div class="stats-icon danger">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="stats-value">{{ $todayCustomers }}</div>
            <div class="stats-label">Bugünkü Müşteri</div>
            <div class="stats-meta mt-2">
                <small class="text-info">
                    <i class="bi bi-person-check"></i> 
                    {{ $activeUsers }} aktif kullanıcı
                </small>
                @if($lowStockProducts > 0)
                    <br><small class="text-danger">
                        <i class="bi bi-exclamation-triangle"></i> 
                        {{ $lowStockProducts }} düşük stok
                    </small>
                @endif
            </div>
        </div>
    </div>

    {{-- Category Sales & Quick Actions --}}
    <div class="action-grid">
        <div class="action-card">
            <h5>
                <i class="bi bi-lightning-charge text-primary me-2"></i>
                Hızlı İşlemler
            </h5>
            <div class="action-buttons">
                <a href="{{ route('admin.tables.index') }}" class="btn-action btn-primary">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Masaları Yönet
                    @if($pendingOrders > 0)
                        <span class="badge bg-white text-primary ms-2">{{ $pendingOrders }}</span>
                    @endif
                </a>
                <a href="#" class="btn-action btn-success" onclick="window.open('{{ url('/') }}/customer/menu/demo-token', '_blank')">
                    <i class="bi bi-eye"></i>
                    Müşteri Görünümü
                </a>
                <a href="#" class="btn-action btn-warning">
                    <i class="bi bi-graph-up"></i>
                    Günlük Rapor
                </a>
            </div>
        </div>

        <div class="action-card">
            <h5>
                <i class="bi bi-pie-chart text-success me-2"></i>
                Kategori Satışları (Bugün)
            </h5>
            @if($categoryStats->count() > 0)
                <div class="category-stats">
                    @foreach($categoryStats->take(4) as $category)
                        <div class="category-item">
                            <div class="category-info">
                                <strong>{{ $category->category_name }}</strong>
                                <small class="text-muted">{{ $category->total_quantity }} adet</small>
                            </div>
                            <div class="category-revenue">
                                ₺{{ number_format($category->total_revenue, 0) }}
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">Detaylı Rapor</a>
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-pie-chart-fill fs-1 opacity-25"></i>
                    <p class="mb-0 mt-2">Henüz kategori satışı bulunmuyor</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Charts Row --}}
    @if($weeklyData->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="action-card">
                    <h5>
                        <i class="bi bi-graph-up-arrow text-info me-2"></i>
                        Haftalık Trend
                    </h5>
                    <div class="weekly-chart">
                        <canvas id="weeklyChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Recent Activity & System Status --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="action-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>
                        <i class="bi bi-clock-history text-info me-2"></i>
                        Son Aktiviteler
                        <span class="badge bg-primary ms-2">{{ $recentActivities->count() }}</span>
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshActivities()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <a href="#" class="btn btn-sm btn-outline-primary">Tümünü Gör</a>
                    </div>
                </div>
                
                <div class="activity-list" id="activityList">
                    @forelse($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity['status'] === 'paid' ? 'bg-success' : ($activity['status'] === 'pending' ? 'bg-warning' : 'bg-info') }}">
                                @switch($activity['status'])
                                    @case('paid')
                                        <i class="bi bi-check-circle"></i>
                                        @break
                                    @case('pending')
                                        <i class="bi bi-clock"></i>
                                        @break
                                    @case('preparing')
                                        <i class="bi bi-gear"></i>
                                        @break
                                    @case('delivered')
                                        <i class="bi bi-check-circle-fill"></i>
                                        @break
                                    @default
                                        <i class="bi bi-circle"></i>
                                @endswitch
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    {{ $activity['table_name'] }} - 
                                    @switch($activity['status'])
                                        @case('paid')
                                            Ödeme tamamlandı
                                            @break
                                        @case('pending')
                                            Yeni sipariş alındı
                                            @break
                                        @case('preparing')
                                            Sipariş hazırlanıyor
                                            @break
                                        @case('delivered')
                                            Sipariş teslim edildi
                                            @break
                                        @default
                                            Sipariş güncellendi
                                    @endswitch
                                </div>
                                <div class="activity-time">
                                    {{ $activity['time_ago'] }}
                                    <small class="text-muted ms-2">{{ $activity['items_count'] }} ürün</small>
                                </div>
                            </div>
                            <div class="activity-amount {{ $activity['status'] === 'paid' ? 'text-success' : 'text-muted' }}">
                                ₺{{ number_format($activity['total_amount'], 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-clock-history fs-1 opacity-25"></i>
                            <p class="mb-0 mt-2">Bugün henüz aktivite bulunmuyor</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- System Status --}}
            <div class="action-card mb-4">
                <h5>
                    <i class="bi bi-speedometer2 text-success me-2"></i>
                    Sistem Durumu
                </h5>
                
                <div class="system-status">
                    <div class="status-item">
                        <div class="status-label">Sunucu Durumu</div>
                        <div class="status-indicator">
                            <span class="badge bg-success">Çevrimiçi</span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-label">Aktif Kullanıcı</div>
                        <div class="status-indicator">
                            <span class="badge bg-info">{{ $activeUsers }}</span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-label">Bekleyen Sipariş</div>
                        <div class="status-indicator">
                            <span class="badge {{ $pendingOrders > 0 ? 'bg-warning' : 'bg-success' }}">
                                {{ $pendingOrders }}
                            </span>
                        </div>
                    </div>

                    @if($lowStockProducts > 0)
                        <div class="status-item">
                            <div class="status-label">Düşük Stok Uyarısı</div>
                            <div class="status-indicator">
                                <span class="badge bg-danger">{{ $lowStockProducts }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Today's Summary --}}
            <div class="action-card">
                <h6 class="text-muted mb-3">
                    <i class="bi bi-calendar-day me-2"></i>
                    Bugünkü Özet
                </h6>
                <div class="quick-stats">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Sipariş:</span>
                        <strong>{{ $todayOrders }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ortalama Sipariş:</span>
                        <strong>₺{{ number_format($averageOrderValue, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Gelir:</span>
                        <strong class="text-success">₺{{ number_format($todayRevenue, 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>En Popüler:</span>
                        <strong>{{ $popularProduct }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Aylık Toplam:</span>
                        <strong class="text-primary">₺{{ number_format($monthlyRevenue, 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* Category Stats */
.category-stats {
    max-height: 300px;
    overflow-y: auto;
}

.category-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.category-item:last-child {
    border-bottom: none;
}

.category-info {
    flex: 1;
}

.category-revenue {
    font-weight: 600;
    color: var(--success-color);
}

/* Weekly Chart Container */
.weekly-chart {
    position: relative;
    height: 300px;
    margin-top: 1rem;
}

/* Existing styles from previous dashboard remain... */
.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    color: var(--dark-color);
    margin-bottom: 0.25rem;
}

.activity-time {
    font-size: 0.875rem;
    color: var(--secondary-color);
}

.activity-amount {
    font-weight: 600;
    font-size: 0.875rem;
}

.status-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.75rem 0;
}

.status-item:not(:last-child) {
    border-bottom: 1px solid #e2e8f0;
}

.status-label {
    flex: 1;
    color: var(--dark-color);
    font-size: 0.875rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stats-meta {
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .activity-item {
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    
    .activity-amount {
        align-self: flex-end;
        margin-top: -1.5rem;
    }
    
    .status-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Real-time dashboard updates
    let lastUpdateTime = null;
    
    function updateDashboardStats(data) {
        // Güncel saati güncelle
        const timeElement = document.querySelector('.page-title + p small');
        if (timeElement) {
            timeElement.textContent = data.lastUpdate;
        }
        
        // İstatistikleri güncelle
        updateStatCard('totalTables', data.totalTables);
        updateStatCard('todayOrders', data.todayOrders);
        updateStatCard('todayRevenue', '₺' + new Intl.NumberFormat('tr-TR').format(data.todayRevenue));
        updateStatCard('todayCustomers', data.todayCustomers);
        
        // Bekleyen sipariş badge'ini güncelle
        const pendingBadges = document.querySelectorAll('.badge.bg-danger');
        pendingBadges.forEach(badge => {
            if (badge.textContent.includes('bekleyen')) {
                badge.textContent = data.pendingOrders + ' bekleyen sipariş';
                badge.style.display = data.pendingOrders > 0 ? 'inline' : 'none';
            }
        });
        
        // Son aktiviteleri güncelle
        if (data.hasNewActivities) {
            updateRecentActivities(data.recentActivities);
        }
        
        // Sistem durumu güncelle
        updateSystemStatus(data);
    }
    
    function updateStatCard(type, value) {
        const cards = document.querySelectorAll('.stats-card');
        cards.forEach(card => {
            const valueElement = card.querySelector('.stats-value');
            const labelElement = card.querySelector('.stats-label');
            
            if (labelElement) {
                const label = labelElement.textContent.toLowerCase();
                if ((type === 'totalTables' && label.includes('masa')) ||
                    (type === 'todayOrders' && label.includes('sipariş')) ||
                    (type === 'todayRevenue' && label.includes('ciro')) ||
                    (type === 'todayCustomers' && label.includes('müşteri'))) {
                    if (valueElement) {
                        valueElement.textContent = value;
                        // Animasyon efekti
                        valueElement.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            valueElement.style.transform = 'scale(1)';
                        }, 200);
                    }
                }
            }
        });
    }
    
    function updateRecentActivities(activities) {
        const activityList = document.getElementById('activityList');
        if (activityList && activities.length > 0) {
            // Yeni aktiviteleri en üste ekle
            activities.forEach(activity => {
                const activityHtml = `
                    <div class="activity-item new-activity">
                        <div class="activity-icon bg-${
                            activity.status === 'paid' ? 'success' : 
                            activity.status === 'pending' ? 'warning' : 'info'
                        }">
                            <i class="bi bi-${
                                activity.status === 'paid' ? 'check-circle' :
                                activity.status === 'pending' ? 'clock' : 'circle'
                            }"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                ${activity.table_name} - 
                                ${
                                    activity.status === 'paid' ? 'Ödeme tamamlandı' :
                                    activity.status === 'pending' ? 'Yeni sipariş alındı' :
                                    'Sipariş güncellendi'
                                }
                            </div>
                            <div class="activity-time">
                                ${activity.time_ago}
                                <small class="text-muted ms-2">${activity.items_count} ürün</small>
                            </div>
                        </div>
                        <div class="activity-amount text-${
                            activity.status === 'paid' ? 'success' : 'muted'
                        }">
                            ₺${new Intl.NumberFormat('tr-TR', {minimumFractionDigits: 2}).format(activity.total_amount)}
                        </div>
                    </div>
                `;
                activityList.insertAdjacentHTML('afterbegin', activityHtml);
            });
            
            // Yeni aktivite animasyonu
            setTimeout(() => {
                document.querySelectorAll('.new-activity').forEach(el => {
                    el.classList.remove('new-activity');
                });
            }, 1000);
        }
    }
    
    function updateSystemStatus(data) {
        const statusItems = document.querySelectorAll('.status-item');
        statusItems.forEach(item => {
            const label = item.querySelector('.status-label');
            const indicator = item.querySelector('.status-indicator .badge');
            
            if (label && indicator) {
                const labelText = label.textContent.toLowerCase();
                if (labelText.includes('bekleyen sipariş')) {
                    indicator.textContent = data.pendingOrders;
                    indicator.className = `badge ${data.pendingOrders > 0 ? 'bg-warning' : 'bg-success'}`;
                } else if (labelText.includes('aktif kullanıcı')) {
                    indicator.textContent = data.activeUsers;
                } else if (labelText.includes('düşük stok')) {
                    if (data.lowStockProducts > 0) {
                        indicator.textContent = data.lowStockProducts;
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                }
            }
        });
    }
    
    function refreshDashboardStats() {
        fetch('{{ route("admin.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                updateDashboardStats(data);
                lastUpdateTime = new Date();
            })
            .catch(error => {
                console.error('Dashboard güncelleme hatası:', error);
            });
    }
    
    function checkNotifications() {
        fetch('{{ route("admin.notifications") }}')
            .then(response => response.json())
            .then(notifications => {
                notifications.forEach(notification => {
                    showNotification(notification);
                });
            })
            .catch(error => {
                console.error('Bildirim kontrol hatası:', error);
            });
    }
    
    function showNotification(notification) {
        // Toast bildirimi göster
        const toast = document.createElement('div');
        toast.className = `alert alert-${
            notification.type === 'new_order' ? 'success' : 'warning'
        } alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <strong>${notification.title}</strong>
            <div>${notification.message}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // 5 saniye sonra otomatik kapat
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }
    
    // Sayfa yüklendiğinde başlat
    document.addEventListener('DOMContentLoaded', function() {
        // İlk yükleme
        refreshDashboardStats();
        checkNotifications();
        
        // Periyodik güncellemeler
        setInterval(refreshDashboardStats, 10000); // 10 saniyede bir
        setInterval(checkNotifications, 30000); // 30 saniyede bir
        
        // Manuel yenileme butonu
        const refreshBtn = document.querySelector('[onclick="refreshActivities()"]');
        if (refreshBtn) {
            refreshBtn.onclick = function(e) {
                e.preventDefault();
                refreshDashboardStats();
                this.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i>';
                setTimeout(() => {
                    this.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
                }, 1000);
            };
        }
    });
</script>

<style>
.new-activity {
    background: #f0f9ff;
    border-left: 3px solid #0ea5e9;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush
