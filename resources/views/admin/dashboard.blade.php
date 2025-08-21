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
                <a href="{{ route('admin.tables') }}" class="btn-action btn-primary">
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
    function refreshDashboardStats() {
        fetch('{{ route("admin.dashboard.stats") }}')
            .then(response => response.json())
            .then(data => {
                // burada Chart.js güncellemesi veya HTML içine değer basılacak
                console.log(data);
            });
    }

    // Sayfa yüklendiğinde ve belli aralıklarla çalıştır
    document.addEventListener("DOMContentLoaded", () => {
        refreshDashboardStats();
        setInterval(refreshDashboardStats, 10000); // 10 saniyede bir
    });
</script>
@endpush
