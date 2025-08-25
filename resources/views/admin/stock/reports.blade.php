@extends('layouts.admin')

@section('title', 'Stok Raporları')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Stok Raporları</h2>
        <p class="text-muted mb-0">Detaylı stok analizleri ve raporlar</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Stok Yönetimi
        </a>
        <button type="button" class="btn btn-success" onclick="exportAllReports()">
            <i class="bi bi-download"></i> Tüm Raporları İndir
        </button>
    </div>
</div>

{{-- Genel Stok Özeti --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 rounded-circle p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-box-seam text-primary fs-4"></i>
                </div>
                <h3 class="fw-bold text-primary">{{ $stockReport['total_products'] }}</h3>
                <p class="text-muted mb-0">Toplam Ürün</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold text-warning">{{ $stockReport['low_stock_count'] }}</h3>
                <p class="text-muted mb-0">Düşük Stok</p>
                <small class="text-muted">({{ $stockReport['low_stock_percentage'] }}%)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-x-circle text-danger fs-4"></i>
                </div>
                <h3 class="fw-bold text-danger">{{ $stockReport['out_of_stock_count'] }}</h3>
                <p class="text-muted mb-0">Stok Bitti</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="bg-success bg-opacity-10 rounded-circle p-3 mx-auto mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-currency-dollar text-success fs-4"></i>
                </div>
                <h3 class="fw-bold text-success">₺{{ number_format($stockReport['total_stock_value'], 0) }}</h3>
                <p class="text-muted mb-0">Toplam Stok Değeri</p>
            </div>
        </div>
    </div>
</div>

{{-- Kategori Bazında Analiz --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-pie-chart"></i> Kategori Bazında Stok Analizi
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Toplam Ürün</th>
                        <th>Düşük Stok</th>
                        <th>Stok Bitti</th>
                        <th>Stok Değeri</th>
                        <th>Durum</th>
                        <th>Grafik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryStockAnalysis as $analysis)
                        @php
                            $healthPercentage = $analysis['total_products'] > 0 
                                ? (($analysis['total_products'] - $analysis['low_stock_count'] - $analysis['out_of_stock_count']) / $analysis['total_products']) * 100 
                                : 100;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $analysis['category_name'] }}</strong>
                            </td>
                            <td>{{ $analysis['total_products'] }}</td>
                            <td>
                                <span class="badge bg-warning">{{ $analysis['low_stock_count'] }}</span>
                                @if($analysis['low_stock_percentage'] > 0)
                                    <small class="text-muted">({{ $analysis['low_stock_percentage'] }}%)</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $analysis['out_of_stock_count'] }}</span>
                            </td>
                            <td>₺{{ number_format($analysis['total_value'], 2) }}</td>
                            <td>
                                @if($healthPercentage >= 80)
                                    <span class="badge bg-success">İyi</span>
                                @elseif($healthPercentage >= 60)
                                    <span class="badge bg-warning">Orta</span>
                                @else
                                    <span class="badge bg-danger">Kötü</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ max(0, $healthPercentage) }}%"></div>
                                    <div class="progress-bar bg-warning" style="width: {{ $analysis['low_stock_percentage'] }}%"></div>
                                    <div class="progress-bar bg-danger" style="width: {{ $analysis['total_products'] > 0 ? ($analysis['out_of_stock_count'] / $analysis['total_products']) * 100 : 0 }}%"></div>
                                </div>
                                <small class="text-muted">{{ round($healthPercentage, 1) }}% sağlıklı</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Stok Trend Analizi --}}
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up"></i> Stok Dağılımı
                </h6>
            </div>
            <div class="card-body">
                <canvas id="stockDistributionChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-pie-chart"></i> Kategori Değer Dağılımı
                </h6>
            </div>
            <div class="card-body">
                <canvas id="categoryValueChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- En Kritik Ürünler --}}
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger bg-opacity-10">
                <h6 class="mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle"></i> En Kritik Ürünler
                </h6>
            </div>
            <div class="card-body">
                @php
                    $criticalProducts = collect($categoryStockAnalysis)
                        ->flatMap(function($category) {
                            return \App\Models\Product::where('is_active', true)
                                ->whereHas('category', function($q) use ($category) {
                                    $q->where('name', $category['category_name']);
                                })
                                ->get()
                                ->filter(function($product) {
                                    return $product->isLowStock();
                                })
                                ->sortBy('stock_quantity');
                        })
                        ->take(5);
                @endphp
                
                @if($criticalProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($criticalProducts as $product)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    <small class="text-muted">{{ $product->category->name ?? 'Kategorisiz' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }}">
                                        {{ $product->stock_quantity }} / {{ $product->min_stock_level }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success fs-1"></i>
                        <p class="text-muted mt-2">Kritik ürün yok</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success bg-opacity-10">
                <h6 class="mb-0 text-success">
                    <i class="bi bi-trophy"></i> En Değerli Stoklar
                </h6>
            </div>
            <div class="card-body">
                @php
                    $valuableProducts = \App\Models\Product::where('is_active', true)
                        ->get()
                        ->sortByDesc(function($product) {
                            return $product->stock_quantity * $product->price;
                        })
                        ->take(5);
                @endphp
                
                <div class="list-group list-group-flush">
                    @foreach($valuableProducts as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">{{ $product->stock_quantity }} adet × ₺{{ number_format($product->price, 2) }}</small>
                            </div>
                            <div class="text-end">
                                <strong class="text-success">₺{{ number_format($product->stock_quantity * $product->price, 2) }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Rapor Dışa Aktarma Seçenekleri --}}
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="bi bi-download"></i> Rapor Dışa Aktarma
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-success" onclick="exportStockReport()">
                        <i class="bi bi-file-earmark-excel"></i> Genel Stok Raporu (Excel)
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-info" onclick="exportCategoryReport()">
                        <i class="bi bi-file-earmark-text"></i> Kategori Analizi (PDF)
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-warning" onclick="exportLowStockReport()">
                        <i class="bi bi-file-earmark-pdf"></i> Düşük Stok Raporu (PDF)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Stok dağılımı grafiği
const stockDistributionCtx = document.getElementById('stockDistributionChart').getContext('2d');
new Chart(stockDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: ['Normal Stok', 'Düşük Stok', 'Stok Bitti'],
        datasets: [{
            data: [
                {{ $stockReport['total_products'] - $stockReport['low_stock_count'] - $stockReport['out_of_stock_count'] }},
                {{ $stockReport['low_stock_count'] }},
                {{ $stockReport['out_of_stock_count'] }}
            ],
            backgroundColor: ['#198754', '#ffc107', '#dc3545'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Kategori değer dağılımı grafiği
const categoryValueCtx = document.getElementById('categoryValueChart').getContext('2d');
new Chart(categoryValueCtx, {
    type: 'pie',
    data: {
        labels: [
            @foreach($categoryStockAnalysis as $analysis)
                '{{ $analysis["category_name"] }}',
            @endforeach
        ],
        datasets: [{
            data: [
                @foreach($categoryStockAnalysis as $analysis)
                    {{ $analysis['total_value'] }},
                @endforeach
            ],
            backgroundColor: [
                '#0d6efd', '#6610f2', '#6f42c1', '#d63384', '#dc3545',
                '#fd7e14', '#ffc107', '#198754', '#20c997', '#0dcaf0'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ₺' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});

// Rapor dışa aktarma fonksiyonları
function exportStockReport() {
    window.location.href = '{{ route("admin.stock.export") }}';
}

function exportCategoryReport() {
    // PDF export için backend endpoint oluşturulabilir
    showAlert('info', 'Kategori analizi raporu hazırlanıyor...');
}

function exportLowStockReport() {
    // PDF export için backend endpoint oluşturulabilir
    showAlert('info', 'Düşük stok raporu hazırlanıyor...');
}

function exportAllReports() {
    if (confirm('Tüm raporları indirmek istediğinizden emin misiniz?')) {
        exportStockReport();
        setTimeout(() => {
            exportCategoryReport();
        }, 1000);
        setTimeout(() => {
            exportLowStockReport();
        }, 2000);
    }
}

// Alert göster
function showAlert(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Otomatik rapor güncellemesi
    setInterval(function() {
        // Gerçek zamanlı veri güncellemesi için AJAX çağrısı
        fetch('/admin/stock/stats')
            .then(response => response.json())
            .then(data => {
                console.log('Stok istatistikleri güncellendi');
            })
            .catch(error => console.error('Güncelleme hatası:', error));
    }, 60000); // 1 dakikada bir
});
</script>
@endpush
