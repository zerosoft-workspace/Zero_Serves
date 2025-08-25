@extends('layouts.admin')

@section('title', 'Stok Yönetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Stok Yönetimi</h2>
        <p class="text-muted mb-0">Ürün stoklarını yönetin ve takip edin</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock.low-stock') }}" class="btn btn-warning">
            <i class="bi bi-exclamation-triangle"></i> Düşük Stok
            @if($lowStockProducts->count() > 0)
                <span class="badge bg-white text-warning ms-1">{{ $lowStockProducts->count() }}</span>
            @endif
        </a>
        <a href="{{ route('admin.stock.reports') }}" class="btn btn-info">
            <i class="bi bi-graph-up"></i> Raporlar
        </a>
        <a href="{{ route('admin.stock.export') }}" class="btn btn-success">
            <i class="bi bi-download"></i> Excel İndir
        </a>
    </div>
</div>

{{-- Stok Özet Kartları --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="bi bi-box-seam text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Toplam Ürün</h6>
                        <h4 class="mb-0">{{ $stockReport['total_products'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Düşük Stok</h6>
                        <h4 class="mb-0">{{ $stockReport['low_stock_count'] }}</h4>
                        <small class="text-muted">%{{ $stockReport['low_stock_percentage'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 rounded p-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Stok Bitti</h6>
                        <h4 class="mb-0">{{ $stockReport['out_of_stock_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="bi bi-currency-dollar text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Stok Değeri</h6>
                        <h4 class="mb-0">₺{{ number_format($stockReport['total_stock_value'], 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtreler --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Ürün Ara</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Ürün adı...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select class="form-select" name="category">
                    <option value="">Tüm Kategoriler</option>
                    @foreach(\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Stok Durumu</label>
                <select class="form-select" name="stock_status">
                    <option value="">Tümü</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Düşük Stok</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Stok Bitti</option>
                    <option value="ok" {{ request('stock_status') == 'ok' ? 'selected' : '' }}>Normal</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filtrele
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Ürün Listesi --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ürün Stokları</h5>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleBulkEdit()">
            <i class="bi bi-pencil-square"></i> Toplu Düzenle
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Ürün</th>
                        <th>Kategori</th>
                        <th>Mevcut Stok</th>
                        <th>Min. Stok</th>
                        <th>Fiyat</th>
                        <th>Stok Değeri</th>
                        <th>Durum</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr data-product-id="{{ $product->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input product-checkbox" 
                                       value="{{ $product->id }}">
                            </td>
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
                                        @if($product->description)
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $product->category->name ?? 'Kategorisiz' }}</span>
                            </td>
                            <td>
                                <span class="stock-quantity fw-bold" data-product-id="{{ $product->id }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="min-stock" data-product-id="{{ $product->id }}">
                                    {{ $product->min_stock_level }}
                                </span>
                            </td>
                            <td>₺{{ number_format($product->price, 2) }}</td>
                            <td>₺{{ number_format($product->stock_quantity * $product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->stock_status }} stock-status" 
                                      data-product-id="{{ $product->id }}">
                                    {{ $product->stock_status_text }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="editStock({{ $product->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info" 
                                            onclick="viewHistory({{ $product->id }})">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-box-seam fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Ürün bulunamadı</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>

{{-- Stok Düzenleme Modal --}}
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stok Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editStockForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ürün</label>
                        <input type="text" class="form-control" id="productName" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Mevcut Stok</label>
                            <input type="number" class="form-control" id="stockQuantity" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Minimum Stok</label>
                            <input type="number" class="form-control" id="minStockLevel" min="0" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Açıklama (İsteğe bağlı)</label>
                        <textarea class="form-control" id="reason" rows="2" 
                                  placeholder="Stok değişikliği sebebi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toplu Düzenleme Panel --}}
<div id="bulkEditPanel" class="position-fixed bottom-0 start-50 translate-middle-x bg-white border rounded-top shadow-lg p-3" 
     style="display: none; z-index: 1050; min-width: 400px;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Toplu Düzenleme</h6>
        <button type="button" class="btn-close btn-sm" onclick="toggleBulkEdit()"></button>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="bulkUpdateStock()">
            <i class="bi bi-check-all"></i> Seçilenleri Güncelle
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
            <i class="bi bi-x"></i> Seçimi Temizle
        </button>
    </div>
    <div class="mt-2">
        <small class="text-muted">
            <span id="selectedCount">0</span> ürün seçildi
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentProductId = null;
let bulkEditMode = false;

// Stok düzenleme
function editStock(productId) {
    currentProductId = productId;
    
    // Mevcut değerleri al
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    const productName = row.querySelector('h6').textContent;
    const stockQuantity = row.querySelector('.stock-quantity').textContent.trim();
    const minStock = row.querySelector('.min-stock').textContent.trim();
    
    // Modal'ı doldur
    document.getElementById('productName').value = productName;
    document.getElementById('stockQuantity').value = stockQuantity;
    document.getElementById('minStockLevel').value = minStock;
    document.getElementById('reason').value = '';
    
    // Modal'ı göster
    new bootstrap.Modal(document.getElementById('editStockModal')).show();
}

// Stok güncelleme form submit
document.getElementById('editStockForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        stock_quantity: document.getElementById('stockQuantity').value,
        min_stock_level: document.getElementById('minStockLevel').value,
        reason: document.getElementById('reason').value,
        _token: '{{ csrf_token() }}'
    };
    
    fetch(`/admin/stock/${currentProductId}/update`, {
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
            // Tabloyu güncelle
            updateProductRow(currentProductId, data.product);
            
            // Modal'ı kapat
            bootstrap.Modal.getInstance(document.getElementById('editStockModal')).hide();
            
            // Başarı mesajı
            showAlert('success', 'Stok başarıyla güncellendi');
        } else {
            showAlert('danger', data.message || 'Bir hata oluştu');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Bir hata oluştu');
    });
});

// Satırı güncelle
function updateProductRow(productId, product) {
    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    
    row.querySelector('.stock-quantity').textContent = product.stock_quantity;
    row.querySelector('.min-stock').textContent = product.min_stock_level;
    
    const statusBadge = row.querySelector('.stock-status');
    statusBadge.className = `badge bg-${product.stock_status} stock-status`;
    statusBadge.textContent = product.stock_status_text;
}

// Toplu düzenleme toggle
function toggleBulkEdit() {
    bulkEditMode = !bulkEditMode;
    const panel = document.getElementById('bulkEditPanel');
    
    if (bulkEditMode) {
        panel.style.display = 'block';
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.style.display = 'block';
        });
    } else {
        panel.style.display = 'none';
        clearSelection();
    }
}

// Seçimi temizle
function clearSelection() {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateSelectedCount();
}

// Seçilen sayıyı güncelle
function updateSelectedCount() {
    const selected = document.querySelectorAll('.product-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = selected;
}

// Tümünü seç/seçme
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

// Checkbox değişikliklerini dinle
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('product-checkbox')) {
        updateSelectedCount();
    }
});

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
    // Real-time stok güncellemeleri
    setInterval(function() {
        fetch('/admin/stock/stats')
            .then(response => response.json())
            .then(data => {
                // İstatistikleri güncelle
                console.log('Stok istatistikleri güncellendi');
            })
            .catch(error => console.error('Stok güncelleme hatası:', error));
    }, 30000); // 30 saniyede bir
});
</script>
@endpush
