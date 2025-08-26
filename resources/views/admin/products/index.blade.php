@extends('layouts.admin')

@section('title', 'Ürün Yönetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Ürün Yönetimi</h2>
        <p class="text-muted mb-0">Ürünleri yönetin ve stok bilgilerini güncelleyin</p>
    </div>
    <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#productForm">
        <i class="bi bi-plus-circle"></i> Yeni Ürün
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Ürün Ekleme Formu --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-plus-circle text-primary me-2"></i>
            Yeni Ürün Ekle
        </h5>
        <button class="btn btn-outline-secondary btn-sm d-none d-md-block" type="button" data-bs-toggle="collapse" data-bs-target="#productForm">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="productForm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf
                <div class="row g-3">
                    {{-- Temel Bilgiler --}}
                    <div class="col-12">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Temel Bilgiler
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Ürün adını giriniz" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Fiyat (₺) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₺</span>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select">
                            <option value="">Kategori seçiniz</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stok Yönetimi --}}
                    <div class="col-12 mt-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-boxes me-1"></i>
                            Stok Yönetimi
                        </h6>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Başlangıç Stok Miktarı</label>
                            <div class="input-group">
                                <input type="number" name="stock_quantity" class="form-control" placeholder="100" min="0" required>
                                <span class="input-group-text">adet</span>
                            </div>
                            <small class="form-text text-muted">Başlangıç stok miktarı</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Minimum Stok Seviyesi</label>
                            <div class="input-group">
                                <input type="number" name="min_stock_level" class="form-control" placeholder="10" min="0" required>
                                <span class="input-group-text">adet</span>
                            </div>
                            <small class="form-text text-muted">Kritik stok eşiği</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Maksimum Stok Kapasitesi</label>
                            <div class="input-group">
                                <input type="number" name="max_stock_level" class="form-control" placeholder="1000" min="0" required>
                                <span class="input-group-text">adet</span>
                            </div>
                            <small class="form-text text-muted">Maksimum stok kapasitesi</small>
                        </div>
                    </div>

                    {{-- Ek Bilgiler --}}
                    <div class="col-12 mt-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-gear me-1"></i>
                            Ek Ayarlar
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Ürün açıklaması (isteğe bağlı)"></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Durum</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                            <label class="form-check-label" for="isActive">
                                Ürün aktif olsun
                            </label>
                        </div>
                        <div class="form-text">Aktif ürünler menüde görüntülenir</div>
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-1"></i>
                                Temizle
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i>
                                Ürün Ekle
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Ürün Listesi --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-ul text-primary me-2"></i>
            Ürün Listesi
        </h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshProducts()">
                <i class="bi bi-arrow-clockwise"></i>
                <span class="d-none d-md-inline ms-1">Yenile</span>
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-funnel"></i>
                    <span class="d-none d-md-inline ms-1">Filtre</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?filter=active">Aktif Ürünler</a></li>
                    <li><a class="dropdown-item" href="?filter=inactive">Pasif Ürünler</a></li>
                    <li><a class="dropdown-item" href="?filter=low_stock">Düşük Stok</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.products.index') }}">Tümü</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>Ürün</th>
                        <th class="d-none d-md-table-cell">Kategori</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th class="d-none d-lg-table-cell">Min. Stok</th>
                        <th class="d-none d-xl-table-cell">Max. Stok</th>
                        <th class="d-none d-md-table-cell">Durum</th>
                        <th width="120">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td class="fw-bold text-muted">{{ $p->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($p->image)
                                        <img src="{{ asset('storage/' . $p->image) }}" 
                                             class="rounded me-2" width="32" height="32" 
                                             style="object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 32px; height: 32px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $p->name }}</h6>
                                        @if($p->description)
                                            <small class="text-muted d-none d-md-block">{{ Str::limit($p->description, 30) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($p->category)
                                    <span class="badge bg-secondary">{{ $p->category->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-bold">₺{{ number_format($p->price, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold me-2">{{ $p->stock_quantity ?? $p->stock ?? 0 }}</span>
                                    <small class="text-muted me-2">/{{ $p->max_stock_level ?? 1000 }}</small>
                                    @php
                                        $currentStock = $p->stock_quantity ?? $p->stock ?? 0;
                                        $minStock = $p->min_stock_level ?? $p->low_stock_threshold ?? 0;
                                    @endphp
                                    @if($minStock > 0 && $currentStock <= $minStock)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            Düşük
                                        </span>
                                    @elseif($currentStock == 0)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i>
                                            Bitti
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="d-none d-lg-table-cell text-muted">
                                {{ $p->min_stock_level ?? $p->low_stock_threshold ?? 0 }}
                            </td>
                            <td class="d-none d-xl-table-cell text-muted">
                                {{ $p->max_stock_level ?? 1000 }}
                            </td>
                            <td class="d-none d-md-table-cell">
                                @if($p->is_active)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-pause-circle"></i>
                                        Pasif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" 
                                            onclick="editProduct({{ $p->id }})" title="Düzenle">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    @if($p->is_active)
                                        <form method="POST" action="{{ route('admin.products.deactivate', $p->id) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-outline-warning" type="submit" title="Pasifleştir">
                                                <i class="bi bi-pause"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.products.activate', $p->id) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-outline-success" type="submit" title="Aktifleştir">
                                                <i class="bi bi-play"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteProduct({{ $p->id }})" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-box-seam fs-1 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Henüz ürün eklenmemiş</p>
                                <small class="text-muted">Yukarıdaki formu kullanarak yeni ürün ekleyebilirsiniz</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($products, 'hasPages') && $products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Form temizleme
function resetForm() {
    document.querySelector('#productForm form').reset();
    // Varsayılan değerleri geri yükle
    document.querySelector('input[name="stock_quantity"]').value = '100';
    document.querySelector('input[name="min_stock_level"]').value = '10';
    document.querySelector('input[name="max_stock_level"]').value = '1000';
    document.querySelector('input[name="is_active"]').checked = true;
}

// Ürün listesini yenile
function refreshProducts() {
    location.reload();
}

// Ürün düzenle (placeholder)
function editProduct(productId) {
    // Modal açılacak veya edit sayfasına yönlendirilecek
    alert('Ürün düzenleme özelliği yakında eklenecek. ID: ' + productId);
}

// Ürün sil
function deleteProduct(productId) {
    if (confirm('Bu ürünü silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${productId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#productForm form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const stockQuantity = parseInt(document.querySelector('input[name="stock_quantity"]').value) || 0;
            const minStock = parseInt(document.querySelector('input[name="min_stock_level"]').value) || 0;
            const maxStock = parseInt(document.querySelector('input[name="max_stock_level"]').value) || 0;
            
            if (minStock > stockQuantity) {
                e.preventDefault();
                alert('Minimum stok seviyesi, başlangıç stok miktarından büyük olamaz!');
                return false;
            }
            
            if (maxStock < stockQuantity) {
                e.preventDefault();
                alert('Maksimum stok seviyesi, başlangıç stok miktarından küçük olamaz!');
                return false;
            }
            
            if (minStock >= maxStock) {
                e.preventDefault();
                alert('Minimum stok seviyesi, maksimum stok seviyesinden küçük olmalıdır!');
                return false;
            }
        });
    }
});
</script>
@endpush