<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Sipariş Bilgileri</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Sipariş No:</strong></td>
                <td>#{{ $order->id }}</td>
            </tr>
            <tr>
                <td><strong>Masa:</strong></td>
                <td>{{ $order->table->name ?? 'Masa Yok' }}</td>
            </tr>
            <tr>
                <td><strong>Durum:</strong></td>
                <td>
                    <span class="badge bg-{{ 
                        $order->status === 'pending' ? 'warning' : 
                        ($order->status === 'preparing' ? 'info' : 
                        ($order->status === 'delivered' ? 'success' : 
                        ($order->status === 'paid' ? 'dark' : 'danger'))) 
                    }}">
                        {{ 
                            $order->status === 'pending' ? 'Bekliyor' : 
                            ($order->status === 'preparing' ? 'Hazırlanıyor' : 
                            ($order->status === 'delivered' ? 'Teslim Edildi' : 
                            ($order->status === 'paid' ? 'Ödendi' : 'İptal Edildi'))) 
                        }}
                    </span>
                </td>
            </tr>
            <tr>
                <td><strong>Sipariş Tarihi:</strong></td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Güncelleme:</strong></td>
                <td>{{ $order->updated_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Toplam Tutar:</strong></td>
                <td><strong>₺{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6 class="fw-bold mb-3">Sipariş Detayları</h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Adet</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'Ürün Bulunamadı' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₺{{ number_format($item->price, 2) }}</td>
                            <td>
                                @if($item->product)
                                    <small class="text-muted">
                                        {{ $item->product->stock_quantity }}/{{ $item->product->max_stock_level }}
                                        <span class="badge bg-{{ $item->product->stock_status }} ms-1">
                                            {{ $item->product->stock_status_text }}
                                        </span>
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>₺{{ number_format($item->quantity * $item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="4">Toplam</th>
                        <th>₺{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@if($order->orderItems->count() > 0)
    <div class="mt-4">
        <h6 class="fw-bold mb-3">Hızlı İşlemler</h6>
        <div class="d-flex gap-2 flex-wrap">
            @if($order->status === 'pending')
                <button type="button" class="btn btn-info btn-sm" 
                        onclick="updateOrderStatus({{ $order->id }}, 'preparing')">
                    <i class="bi bi-gear"></i> Hazırlamaya Başla
                </button>
            @elseif($order->status === 'preparing')
                <button type="button" class="btn btn-success btn-sm" 
                        onclick="updateOrderStatus({{ $order->id }}, 'delivered')">
                    <i class="bi bi-check"></i> Teslim Et
                </button>
            @elseif($order->status === 'delivered')
                <button type="button" class="btn btn-dark btn-sm" 
                        onclick="updateOrderStatus({{ $order->id }}, 'paid')">
                    <i class="bi bi-credit-card"></i> Ödeme Tamamlandı
                </button>
            @endif
            
            @if(!in_array($order->status, ['paid', 'canceled']))
                <button type="button" class="btn btn-outline-warning btn-sm" 
                        onclick="updateOrderStatus({{ $order->id }}, 'canceled')">
                    <i class="bi bi-x-circle"></i> İptal Et
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm" 
                        onclick="deleteOrder({{ $order->id }})">
                    <i class="bi bi-trash"></i> Sil
                </button>
            @endif
            
            <button type="button" class="btn btn-outline-secondary btn-sm" 
                    onclick="printOrder({{ $order->id }})">
                <i class="bi bi-printer"></i> Yazdır
            </button>
        </div>
    </div>
@endif

<script>
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

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
                alert('Sipariş başarıyla silindi');
                window.location.href = '/admin/orders';
            } else {
                alert('Hata: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

function printOrder(orderId) {
    window.open(`/admin/orders/${orderId}/print`, '_blank');
}
</script>
