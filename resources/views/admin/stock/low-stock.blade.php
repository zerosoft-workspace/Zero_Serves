@extends('layouts.admin')

@section('title', 'Düşük Stok Uyarıları')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-warning">
            <i class="bi bi-exclamation-triangle"></i> Düşük Stok Uyarıları
        </h2>
        <p class="text-muted mb-0">Minimum stok seviyesinin altındaki ürünler</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Stok Yönetimi
        </a>
        <button type="button" class="btn btn-primary" onclick="generateRestockReport()">
            <i class="bi bi-file-earmark-text"></i> Sipariş Önerisi
        </button>
    </div>
</div>

@if($lowStockProducts->count() > 0)
    {{-- Özet Bilgi --}}
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="alert-heading mb-2">
                    <i class="bi bi-exclamation-triangle"></i> {{ $lowStockProducts->count() }} ürün düşük stokta
                </h5>
                <p class="mb-0">Bu ürünler için acil stok takviyesi yapılması önerilir.</p>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-warning" onclick="bulkRestock()">
                    <i class="bi bi-plus-circle"></i> Toplu Stok Takviyesi
                </button>
            </div>
        </div>
    </div>

    {{-- Düşük Stoklu Ürünler --}}
    <div class="card shadow-sm">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="mb-0 text-warning">
                <i class="bi bi-list-ul"></i> Düşük Stoklu Ürünler
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ürün</th>
                            <th>Kategori</th>
                            <th>Mevcut Stok</th>
                            <th>Min. Stok</th>
                            <th>Eksik</th>
                            <th>Öncelik</th>
                            <th>Tahmini Maliyet</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockProducts as $product)
                            @php
                                $shortage = $product->min_stock_level - $product->stock_quantity;
                                $suggestedQuantity = max($product->min_stock_level * 2, 10);
                                $estimatedCost = $suggestedQuantity * $product->price;
                                $priority = $product->stock_quantity <= 0 ? 'Kritik' : ($shortage > $product->min_stock_level * 0.5 ? 'Yüksek' : 'Orta');
                                $priorityClass = $product->stock_quantity <= 0 ? 'danger' : ($shortage > $product->min_stock_level * 0.5 ? 'warning' : 'info');
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 class="rounded me-2" width="40" height="40" 
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $product->name }}</h6>
                                            <small class="text-muted">{{ $product->sku ?? 'SKU yok' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $product->category->name ?? 'Kategorisiz' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $product->stock_quantity <= 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                </td>
                                <td>{{ $product->min_stock_level }}</td>
                                <td>
                                    <span class="text-danger fw-bold">{{ $shortage }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $priorityClass }}">{{ $priority }}</span>
                                </td>
                                <td>₺{{ number_format($estimatedCost, 2) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="quickRestock({{ $product->id }}, {{ $suggestedQuantity }})">
                                            <i class="bi bi-plus"></i> Stok Ekle
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="viewDetails({{ $product->id }})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sipariş Önerileri --}}
    @if(count($suggestions) > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info bg-opacity-10">
                <h5 class="mb-0 text-info">
                    <i class="bi bi-lightbulb"></i> Otomatik Sipariş Önerileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted mb-3">Sistem tarafından önerilen stok sipariş miktarları:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Önerilen Miktar</th>
                                        <th>Tahmini Maliyet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_slice($suggestions, 0, 5) as $suggestion)
                                        <tr>
                                            <td>{{ $suggestion['product_name'] }}</td>
                                            <td>{{ $suggestion['suggested_quantity'] }} adet</td>
                                            <td>₺{{ number_format($suggestion['estimated_cost'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-3">
                            <h6 class="text-muted">Toplam Tahmini Maliyet</h6>
                            <h4 class="text-info mb-3">₺{{ number_format(collect($suggestions)->sum('estimated_cost'), 2) }}</h4>
                            
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-info" onclick="exportSuggestions()">
                                    <i class="bi bi-download"></i> Sipariş Listesi İndir
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="emailSuggestions()">
                                    <i class="bi bi-envelope"></i> E-posta Gönder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@else
    {{-- Stok Durumu İyi --}}
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
        </div>
        <h3 class="text-success mb-3">Harika! Tüm stoklar yeterli seviyede</h3>
        <p class="text-muted mb-4">Şu anda düşük stoklu ürün bulunmuyor.</p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('admin.stock.index') }}" class="btn btn-primary">
                <i class="bi bi-box-seam"></i> Stok Yönetimi
            </a>
            <a href="{{ route('admin.stock.reports') }}" class="btn btn-outline-info">
                <i class="bi bi-graph-up"></i> Stok Raporları
            </a>
        </div>
    </div>
@endif

{{-- Hızlı Stok Ekleme Modal --}}
<div class="modal fade" id="quickRestockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hızlı Stok Ekleme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRestockForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün</label>
                        <input type="text" class="form-control" id="quickProductName" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Mevcut Stok</label>
                            <input type="number" class="form-control" id="quickCurrentStock" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Eklenecek Miktar</label>
                            <input type="number" class="form-control" id="quickAddQuantity" min="1" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Yeni Toplam Stok</label>
                        <input type="number" class="form-control" id="quickNewTotal" readonly>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Açıklama</label>
                        <textarea class="form-control" id="quickReason" rows="2" 
                                  placeholder="Stok ekleme sebebi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Stok Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQuickProductId = null;

// Hızlı stok ekleme
function quickRestock(productId, suggestedQuantity) {
    currentQuickProductId = productId;
    
    // Ürün bilgilerini al
    const row = document.querySelector(`tr:has([onclick*="${productId}"])`);
    const productName = row.querySelector('h6').textContent;
    const currentStock = parseInt(row.querySelector('.fw-bold').textContent);
    
    // Modal'ı doldur
    document.getElementById('quickProductName').value = productName;
    document.getElementById('quickCurrentStock').value = currentStock;
    document.getElementById('quickAddQuantity').value = suggestedQuantity;
    document.getElementById('quickNewTotal').value = currentStock + suggestedQuantity;
    document.getElementById('quickReason').value = 'Düşük stok takviyesi';
    
    // Modal'ı göster
    new bootstrap.Modal(document.getElementById('quickRestockModal')).show();
}

// Eklenecek miktar değiştiğinde toplam hesapla
document.getElementById('quickAddQuantity').addEventListener('input', function() {
    const current = parseInt(document.getElementById('quickCurrentStock').value) || 0;
    const add = parseInt(this.value) || 0;
    document.getElementById('quickNewTotal').value = current + add;
});

// Hızlı stok ekleme form submit
document.getElementById('quickRestockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newTotal = parseInt(document.getElementById('quickNewTotal').value);
    const reason = document.getElementById('quickReason').value;
    
    const formData = {
        stock_quantity: newTotal,
        min_stock_level: document.querySelector(`tr:has([onclick*="${currentQuickProductId}"])`).children[4].textContent, // Mevcut min stok
        reason: reason,
        _token: '{{ csrf_token() }}'
    };
    
    fetch(`/admin/stock/${currentQuickProductId}/update`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Modal'ı kapat
            bootstrap.Modal.getInstance(document.getElementById('quickRestockModal')).hide();
            
            // Sayfayı yenile
            location.reload();
        } else {
            showAlert('danger', data.message || 'Bir hata oluştu');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Bir hata oluştu');
    });
});

// Sipariş önerilerini dışa aktar
function exportSuggestions() {
    const suggestions = @json($suggestions);
    
    let csv = 'Ürün Adı,Mevcut Stok,Minimum Stok,Önerilen Miktar,Tahmini Maliyet\n';
    
    suggestions.forEach(item => {
        csv += `"${item.product_name}",${item.current_stock},${item.min_stock},${item.suggested_quantity},"₺${item.estimated_cost.toFixed(2)}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `siparis_onerileri_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Toplu stok takviyesi
function bulkRestock() {
    if (confirm('Tüm düşük stoklu ürünler için önerilen miktarlarda stok takviyesi yapılsın mı?')) {
        const suggestions = @json($suggestions);
        
        const bulkData = {
            products: suggestions.map(item => ({
                id: item.product_id,
                stock_quantity: item.current_stock + item.suggested_quantity,
                min_stock_level: item.min_stock
            })),
            _token: '{{ csrf_token() }}'
        };
        
        fetch('/admin/stock/bulk-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(bulkData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `${data.updated_count} ürün başarıyla güncellendi`);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('danger', data.message || 'Bir hata oluştu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Bir hata oluştu');
        });
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

// Sayfa yüklendiğinde otomatik kontrol
document.addEventListener('DOMContentLoaded', function() {
    // Kritik stoklar için uyarı
    const criticalCount = {{ $lowStockProducts->where('stock_quantity', '<=', 0)->count() }};
    if (criticalCount > 0) {
        showAlert('danger', `${criticalCount} ürünün stoğu tamamen bitti!`);
    }
});
</script>
@endpush
