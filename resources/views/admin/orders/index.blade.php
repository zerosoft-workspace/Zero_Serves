@extends('layouts.admin')

@section('title', 'Sipariş Yönetimi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Sipariş Yönetimi</h2>
        <p class="text-muted mb-0">Tüm siparişleri yönetin ve takip edin</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-primary" onclick="refreshOrders()">
            <i class="bi bi-arrow-clockwise"></i> Yenile
        </button>
        <button type="button" class="btn btn-success" onclick="exportOrders()">
            <i class="bi bi-download"></i> Excel İndir
        </button>
    </div>
</div>

{{-- İstatistik Kartları --}}
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-primary bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-calendar-day text-primary"></i>
                </div>
                <h6 class="fw-bold text-primary mb-0" id="stat-total-today">{{ $stats['total_today'] }}</h6>
                <small class="text-muted">Bugün</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-warning bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-clock text-warning"></i>
                </div>
                <h6 class="fw-bold text-warning mb-0" id="stat-pending">{{ $stats['pending'] }}</h6>
                <small class="text-muted">Bekliyor</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-info bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-gear text-info"></i>
                </div>
                <h6 class="fw-bold text-info mb-0" id="stat-preparing">{{ $stats['preparing'] }}</h6>
                <small class="text-muted">Hazırlanıyor</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-success bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-check-circle text-success"></i>
                </div>
                <h6 class="fw-bold text-success mb-0" id="stat-delivered">{{ $stats['delivered'] ?? 0 }}</h6>
                <small class="text-muted">Teslim Edildi</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-dark bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-check-all text-dark"></i>
                </div>
                <h6 class="fw-bold text-dark mb-0" id="stat-paid">{{ $stats['paid'] ?? 0 }}</h6>
                <small class="text-muted">Ödendi</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-3">
                <div class="bg-success bg-opacity-10 rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                    <i class="bi bi-currency-dollar text-success"></i>
                </div>
                <h6 class="fw-bold text-success mb-0" id="stat-revenue">₺{{ number_format($stats['today_revenue'], 0) }}</h6>
                <small class="text-muted">Günlük Ciro</small>
            </div>
        </div>
    </div>
</div>

{{-- Filtreler --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3" id="filterForm">
            <div class="col-lg-2 col-md-4">
                <label class="form-label">Durum</label>
                <select class="form-select" name="status">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                    <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Hazırlanıyor</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Ödendi</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>İptal Edildi</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label">Masa</label>
                <select class="form-select" name="table_id">
                    <option value="">Tüm Masalar</option>
                    @foreach($tables as $table)
                        <option value="{{ $table->id }}" {{ request('table_id') == $table->id ? 'selected' : '' }}>
                            {{ $table->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Arama</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Sipariş no veya masa adı...">
            </div>
            <div class="col-lg-1 col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Toplu İşlemler --}}
<div id="bulkActionsPanel" class="card mb-3" style="display: none;">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted">
                    <span id="selectedCount">0</span> sipariş seçildi
                </span>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="bulkStatusSelect" style="width: auto;">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending">Bekliyor</option>
                    <option value="preparing">Hazırlanıyor</option>
                    <option value="delivered">Teslim Edildi</option>
                    <option value="paid">Ödendi</option>
                    <option value="canceled">İptal Edildi</option>
                </select>
                <button type="button" class="btn btn-primary btn-sm" onclick="bulkUpdateStatus()">
                    <i class="bi bi-check-all"></i> Uygula
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                    <i class="bi bi-x"></i> Temizle
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Sipariş Listesi --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Siparişler</h5>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label" for="selectAll">
                Tümünü Seç
            </label>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="30"></th>
                        <th>Sipariş</th>
                        <th>Masa</th>
                        <th>Durum</th>
                        <th>Ürünler</th>
                        <th>Tutar</th>
                        <th>Toplam Süre</th>
                        <th width="200">İşlemler</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    @forelse($orders as $order)
                        <tr data-order-id="{{ $order->id }}">
                            <td>
                                <input type="checkbox" class="form-check-input order-checkbox" 
                                       value="{{ $order->id }}">
                            </td>
                            <td>
                                <div>
                                    <strong>#{{ $order->id }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $order->table->name ?? 'Masa Yok' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $order->status === 'pending' ? 'warning' : 
                                    ($order->status === 'preparing' ? 'info' : 
                                    ($order->status === 'delivered' ? 'success' : 
                                    ($order->status === 'paid' ? 'secondary' : 'danger'))) 
                                }}">
                                    {{ 
                                        $order->status === 'pending' ? 'Bekliyor' : 
                                        ($order->status === 'preparing' ? 'Hazırlanıyor' : 
                                        ($order->status === 'delivered' ? 'Teslim Edildi' : 
                                        ($order->status === 'paid' ? 'Ödendi' : 'İptal Edildi'))) 
                                    }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-dark me-2">{{ $order->orderItems->count() }}</span>
                                    <button type="button" class="btn btn-link btn-sm p-0" 
                                            onclick="showOrderDetails({{ $order->id }})">
                                        Detay
                                    </button>
                                </div>
                            </td>
                            <td>
                                <strong>₺{{ number_format($order->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $order->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($order->status === 'pending')
                                        <button class="btn btn-sm btn-warning" onclick="updateOrderStatus({{ $order->id }}, 'preparing')">
                                            <i class="bi bi-clock"></i> Hazırlanıyor
                                        </button>
                                    @elseif($order->status === 'preparing')
                                        <button class="btn btn-sm btn-success" onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                                            <i class="bi bi-check"></i> Teslim Et
                                        </button>
                                    @elseif($order->status === 'delivered')
                                        <button class="btn btn-sm btn-secondary" onclick="updateOrderStatus({{ $order->id }}, 'paid')">
                                            <i class="bi bi-credit-card"></i> Ödendi
                                        </button>
                                    @endif
                                    
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" onclick="showOrderDetails({{ $order->id }})">
                                                <i class="bi bi-eye"></i> Detaylar
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="printOrder({{ $order->id }})">
                                                <i class="bi bi-printer"></i> Yazdır
                                            </a></li>
                                            @if(!in_array($order->status, ['paid', 'canceled']))
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-warning" href="#" 
                                                       onclick="updateOrderStatus({{ $order->id }}, 'canceled')">
                                                    <i class="bi bi-x-circle"></i> İptal Et
                                                </a></li>
                                                <li><a class="dropdown-item text-danger" href="#" 
                                                       onclick="deleteOrder({{ $order->id }})">
                                                    <i class="bi bi-trash"></i> Sil
                                                </a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                                <p class="text-muted mt-2">Sipariş bulunamadı</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    @endif
</div>

{{-- Sipariş Detay Modal --}}
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedOrders = [];

// Sipariş durumu güncelleme
function updateOrderStatus(orderId, status) {
    if (confirm('Sipariş durumunu güncellemek istediğinizden emin misiniz?')) {
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Satırı dinamik olarak güncelle
                updateOrderRowStatus(orderId, status);
                showAlert('success', data.message);
                updateStats();
            } else {
                showAlert('danger', data.message || 'Güncelleme başarısız');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Bağlantı hatası oluştu');
        });
    }
}

// Sipariş silme
function deleteOrder(orderId) {
    if (confirm('Bu siparişi tamamen silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
        fetch(`/admin/orders/${orderId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Satırı tablodan kaldır
                const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                if (row) {
                    row.remove();
                }
                showAlert('success', data.message);
                updateStats();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Bir hata oluştu');
        });
    }
}

// Sipariş satırını güncelle
function updateOrderRowStatus(orderId, newStatus) {
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    if (!row) return;
    
    // Durum badge'ini güncelle
    const statusCell = row.children[3]; // Durum sütunu (4. sütun)
    if (statusCell) {
        const statusBadge = statusCell.querySelector('.badge');
        if (statusBadge) {
            statusBadge.className = `badge bg-${getStatusColor(newStatus)}`;
            statusBadge.textContent = getStatusText(newStatus);
        }
    }
    
    // İşlemler sütunundaki butonları güncelle
    const actionsCell = row.children[6]; // İşlemler sütunu (7. sütun)
    if (actionsCell) {
        const buttonContainer = actionsCell.querySelector('.btn-group');
        if (buttonContainer) {
            buttonContainer.innerHTML = getStatusButtons(orderId, newStatus);
        }
    }
}

// Durum butonları
function getStatusButtons(orderId, status) {
    let buttonsHtml = '';
    
    if (status === 'pending') {
        buttonsHtml = `<button type="button" class="btn btn-warning btn-sm" onclick="updateOrderStatus(${orderId}, 'preparing')">
            <i class="bi bi-clock"></i> Hazırlanıyor
        </button>`;
    } else if (status === 'preparing') {
        buttonsHtml = `<button type="button" class="btn btn-success btn-sm" onclick="updateOrderStatus(${orderId}, 'delivered')">
            <i class="bi bi-check"></i> Teslim Et
        </button>`;
    } else if (status === 'delivered') {
        buttonsHtml = `<button type="button" class="btn btn-secondary btn-sm" onclick="updateOrderStatus(${orderId}, 'paid')">
            <i class="bi bi-credit-card"></i> Ödendi
        </button>`;
    }
    
    return buttonsHtml;
}

// Toplu durum güncelleme
function bulkUpdateStatus() {
    const status = document.getElementById('bulkStatusSelect').value;
    if (!status) {
        showAlert('warning', 'Lütfen bir durum seçin');
        return;
    }
    
    if (selectedOrders.length === 0) {
        showAlert('warning', 'Lütfen sipariş seçin');
        return;
    }
    
    if (confirm(`${selectedOrders.length} siparişin durumunu güncellemek istediğinizden emin misiniz?`)) {
        fetch('/admin/orders/bulk-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                order_ids: selectedOrders,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Bir hata oluştu');
        });
    }
}

// Sipariş detayları göster
function showOrderDetails(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();
    
    fetch(`/admin/orders/${orderId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('orderDetailContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('orderDetailContent').innerHTML = 
                '<div class="alert alert-danger">Sipariş detayları yüklenemedi</div>';
        });
}

// Sipariş yazdır
function printOrder(orderId) {
    window.open(`/admin/orders/${orderId}/print`, '_blank');
}

// Seçim yönetimi
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = this.checked;
        updateSelection(cb.value, cb.checked);
    });
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('order-checkbox')) {
        updateSelection(e.target.value, e.target.checked);
    }
});

function updateSelection(orderId, isSelected) {
    if (isSelected) {
        if (!selectedOrders.includes(orderId)) {
            selectedOrders.push(orderId);
        }
    } else {
        selectedOrders = selectedOrders.filter(id => id !== orderId);
    }
    
    updateSelectionUI();
}

function updateSelectionUI() {
    const count = selectedOrders.length;
    document.getElementById('selectedCount').textContent = count;
    
    const panel = document.getElementById('bulkActionsPanel');
    panel.style.display = count > 0 ? 'block' : 'none';
}

function clearSelection() {
    selectedOrders = [];
    document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelectionUI();
}

// İstatistikleri güncelle
function updateStats() {
    fetch('/admin/orders/stats/realtime')
        .then(response => response.json())
        .then(data => {
            document.getElementById('stat-total-today').textContent = data.stats.total_today;
            document.getElementById('stat-pending').textContent = data.stats.pending;
            document.getElementById('stat-preparing').textContent = data.stats.preparing;
            document.getElementById('stat-delivered').textContent = data.stats.delivered;
            document.getElementById('stat-paid').textContent = data.stats.paid;
            document.getElementById('stat-revenue').textContent = '₺' + data.stats.today_revenue.toLocaleString();
        })
        .catch(error => console.error('Stats update error:', error));
}

// Siparişleri yenile
function refreshOrders() {
    location.reload();
}

// Export
function exportOrders() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = `/admin/orders/export/csv?${params.toString()}`;
}

// Alert göster
function showAlert(type, message) {
    // Mevcut alertleri temizle
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Yeni alert oluştur
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // 4 saniye sonra otomatik kapat
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 4000);
}

// Real-time güncellemeler
setInterval(updateStats, 30000); // 30 saniyede bir

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    // Otomatik yenileme (5 dakikada bir)
    setInterval(refreshOrders, 300000);
});
</script>
@endpush
