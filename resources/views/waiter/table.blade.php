@extends('layouts.waiter')

@section('title', $table->name ?? 'Masa Detayı')

@section('header_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> 
            <span class="d-none d-sm-inline">Masalar</span>
        </a>
        <button onclick="location.reload()" class="btn btn-sm btn-ghost">
            <i class="bi bi-arrow-clockwise me-1"></i>
            <span class="d-none d-sm-inline">Yenile</span>
        </button>
    </div>
@endsection

@section('content')
    @php
        $statusMap = [
            'pending' => 'Sipariş Bekliyor',
            'preparing' => 'Hazırlanıyor',
            'delivered' => 'Teslim Edildi',
            'paid' => 'Ödendi',
        ];
    @endphp

    {{-- Masa Başlığı --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-primary bg-gradient rounded-circle text-white">
                <i class="bi bi-table fs-4"></i>
            </div>
            <div>
                <h2 class="mb-0 fw-bold">{{ $table->name }}</h2>
                <p class="text-muted mb-0">Masa Yönetimi</p>
            </div>
        </div>
    </div>

    {{-- Mevcut Sipariş --}}
    @if(!$currentOrder)
        <div class="text-center py-5">
            <div class="p-4 bg-light rounded-circle d-inline-flex mb-3">
                <i class="bi bi-clipboard-x display-4 text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">Aktif Sipariş Yok</h5>
            <p class="text-muted">Bu masada şu anda aktif bir sipariş bulunmuyor.</p>
        </div>
    @else
        {{-- CSRF token'ı JS için gizli inputta tutalım --}}
        <input type="hidden" id="csrfToken" value="{{ csrf_token() }}">
        <input type="hidden" id="currentOrderStatus" value="{{ $currentOrder->status }}">

        <div class="card mb-4 border-0 shadow-sm" data-order-id="{{ $currentOrder->id }}">
            <div class="card-header bg-gradient-primary text-white border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt fs-5"></i>
                        <div>
                            <h5 class="mb-0">Sipariş #{{ $currentOrder->id }}</h5>
                            <small class="opacity-75">{{ $currentOrder->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    <span class="badge bg-light text-dark fs-6" id="order-status-text">
                        {{ $statusMap[$currentOrder->status] ?? strtoupper($currentOrder->status) }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- Ürünler --}}
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3 text-muted">Sipariş Detayları</h6>
                    <div class="list-group list-group-flush">
                        @foreach($currentOrder->items as $item)
                            <div class="list-group-item px-0 py-3 border-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $item->product->name ?? 'Ürün' }}</h6>
                                        <div class="d-flex align-items-center gap-3 small text-muted">
                                            <span class="d-flex align-items-center gap-1">
                                                <i class="bi bi-x-lg"></i>
                                                {{ $item->quantity }}
                                            </span>
                                            @isset($item->price)
                                                <span class="d-flex align-items-center gap-1">
                                                    <i class="bi bi-tag"></i>
                                                    {{ number_format($item->price, 2) }} ₺
                                                </span>
                                            @endisset
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">
                                            {{ number_format($item->line_total ?? (($item->price ?? 0) * ($item->quantity ?? 0)), 2) }} ₺
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Toplam --}}
                <div class="bg-light rounded p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Genel Toplam:</span>
                        <span class="fw-bold fs-4 text-primary">{{ number_format($currentOrder->total_amount, 2) }} ₺</span>
                    </div>
                </div>

                {{-- Durum geçiş butonları --}}
                <div class="mb-3">
                    <h6 class="fw-semibold mb-3 text-muted">Sipariş Durumu Güncelle</h6>
                    <div class="status-flow">
                        <button id="btn-preparing" class="btn btn-warning" @disabled($currentOrder->status !== 'pending')
                            data-route-url="{{ route('waiter.orders.status', $currentOrder->id) }}"
                            onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','preparing')">
                            <i class="bi bi-hourglass-split me-1"></i>
                            <span>Hazırlanıyor</span>
                        </button>

                        <button id="btn-delivered" class="btn btn-success" @disabled($currentOrder->status !== 'preparing')
                            data-route-url="{{ route('waiter.orders.status', $currentOrder->id) }}"
                            onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','delivered')">
                            <i class="bi bi-check-circle me-1"></i>
                            <span>Teslim Edildi</span>
                        </button>

                        <button id="btn-paid" class="btn btn-primary" @disabled(!in_array($currentOrder->status, ['delivered', 'paid']))
                            data-route-url="{{ route('waiter.orders.status', $currentOrder->id) }}"
                            onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','paid')">
                            <i class="bi bi-credit-card me-1"></i>
                            <span>Ödendi</span>
                        </button>
                    </div>
                </div>

                <div id="status-flash" class="mt-3" style="display:none;"></div>
            </div>
        </div>
    @endif
    <div class="text-center mt-4">
        <a href="{{ route('waiter.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Masalara Dön
        </a>
    </div>
    {{-- Geçmiş Siparişler --}}
    @if($pastOrders && $pastOrders->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-muted"></i>
                    <h5 class="mb-0 fw-semibold">Geçmiş Siparişler</h5>
                    <span class="badge bg-secondary">{{ $pastOrders->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="pastOrdersAccordion">
                    @foreach($pastOrders as $index => $pastOrder)
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">Sipariş #{{ $pastOrder->id }}</div>
                                                <small class="text-muted">{{ $pastOrder->created_at->format('d.m.Y H:i') }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">{{ number_format($pastOrder->total_amount, 2) }} ₺</div>
                                            <small class="text-muted">Tamamlandı</small>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                                aria-labelledby="heading{{ $index }}" data-bs-parent="#pastOrdersAccordion">
                                <div class="accordion-body bg-light">
                                    <div class="list-group list-group-flush">
                                        @foreach($pastOrder->items as $item)
                                            <div class="list-group-item bg-transparent px-0 py-2 border-0 border-bottom">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->product->name ?? 'Ürün' }}</div>
                                                        <small class="text-muted">{{ $item->quantity }} adet</small>
                                                    </div>
                                                    <span class="fw-semibold">{{ number_format($item->line_total, 2) }} ₺</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        // Sayfa yüklendiğinde mevcut duruma göre butonları ayarla
        document.addEventListener('DOMContentLoaded', function() {
            const currentStatus = document.getElementById('currentOrderStatus')?.value;
            if (currentStatus) {
                setButtonsByStatus(currentStatus);
            }
        });

        function setButtonsByStatus(status) {
            console.log('setButtonsByStatus çağrıldı, status:', status);
            
            const btnPreparing = document.getElementById('btn-preparing');
            const btnDelivered = document.getElementById('btn-delivered');
            const btnPaid = document.getElementById('btn-paid');

            if (!btnPreparing || !btnDelivered || !btnPaid) {
                console.log('Butonlar bulunamadı!');
                return;
            }

            // Tüm butonları önce temizle ve onclick eventlerini kaldır
            [btnPreparing, btnDelivered, btnPaid].forEach(btn => {
                btn.classList.remove('btn-warning', 'btn-success', 'btn-primary', 'btn-outline-secondary');
                btn.disabled = true; // Önce hepsini disable et
                btn.style.pointerEvents = 'none'; // Tıklanmayı engelle
                btn.onclick = null; // onclick eventini kaldır
            });

            // Status'e göre butonları ayarla
            switch(status) {
                case 'pending':
                    // Sadece "Hazırlanıyor" butonu aktif
                    btnPreparing.disabled = false;
                    btnPreparing.style.pointerEvents = 'auto';
                    btnPreparing.classList.add('btn-warning');
                    btnPreparing.onclick = function() { changeStatus('{{ $currentOrder ? route("waiter.orders.status", $currentOrder->id) : "#" }}', 'preparing'); };
                    
                    btnDelivered.classList.add('btn-outline-secondary');
                    btnPaid.classList.add('btn-outline-secondary');
                    break;
                    
                case 'preparing':
                    // Sadece "Teslim Edildi" butonu aktif
                    btnPreparing.classList.add('btn-outline-secondary');
                    
                    btnDelivered.disabled = false;
                    btnDelivered.style.pointerEvents = 'auto';
                    btnDelivered.classList.add('btn-success');
                    btnDelivered.onclick = function() { changeStatus('{{ $currentOrder ? route("waiter.orders.status", $currentOrder->id) : "#" }}', 'delivered'); };
                    
                    btnPaid.classList.add('btn-outline-secondary');
                    break;
                    
                case 'delivered':
                    // Sadece "Ödendi" butonu aktif
                    btnPreparing.classList.add('btn-outline-secondary');
                    btnDelivered.classList.add('btn-outline-secondary');
                    
                    btnPaid.disabled = false;
                    btnPaid.style.pointerEvents = 'auto';
                    btnPaid.classList.add('btn-primary');
                    btnPaid.onclick = function() { changeStatus('{{ $currentOrder ? route("waiter.orders.status", $currentOrder->id) : "#" }}', 'paid'); };
                    break;
                    
                case 'paid':
                    // Tüm butonlar pasif - onclick eventleri zaten null
                    btnPreparing.classList.add('btn-outline-secondary');
                    btnDelivered.classList.add('btn-outline-secondary');
                    btnPaid.classList.add('btn-outline-secondary');
                    break;
                    
                default:
                    console.log('Bilinmeyen status:', status);
                    break;
            }
            
            console.log('Buton durumları güncellendi:');
            console.log('- Hazırlanıyor disabled:', btnPreparing.disabled, 'onclick:', btnPreparing.onclick !== null);
            console.log('- Teslim Edildi disabled:', btnDelivered.disabled, 'onclick:', btnDelivered.onclick !== null);
            console.log('- Ödendi disabled:', btnPaid.disabled, 'onclick:', btnPaid.onclick !== null);
        }

        async function changeStatus(url, toStatus) {
            const token = document.getElementById('csrfToken')?.value;
            const flash = document.getElementById('status-flash');
            const statusText = document.getElementById('order-status-text');
            const currentOrderStatus = document.getElementById('currentOrderStatus');

            // Butonu disable et ve loading state'e geç
            const clickedButton = event.target.closest('button');
            const originalContent = clickedButton.innerHTML;
            const originalDisabled = clickedButton.disabled;
            
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Güncelleniyor...';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ to_status: toStatus })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Durum güncellenemedi.');
                }

                // UI güncelle
                const statusMap = {
                    'pending': 'Sipariş Bekliyor',
                    'preparing': 'Hazırlanıyor',
                    'delivered': 'Teslim Edildi',
                    'paid': 'Ödendi'
                };

                // Status badge'i güncelle
                if (statusText) {
                    statusText.textContent = statusMap[data.new_status] ?? data.new_status.toUpperCase();
                }

                // Hidden inputu güncelle
                if (currentOrderStatus) {
                    currentOrderStatus.value = data.new_status;
                }

                // Tıklanan butonu eski haline getir
                clickedButton.innerHTML = originalContent;

                // Butonları yeni duruma göre güncelle
                console.log('Yeni durum:', data.new_status);
                setButtonsByStatus(data.new_status);

                // Ana sayfayı da güncelle (dashboard)
                if (window.opener && !window.opener.closed) {
                    window.opener.location.reload();
                }
                
                // Bu sayfayı da yenile
                setTimeout(() => {
                    window.location.reload();
                }, 1000);

                // Başarı mesajı göster
                if (flash) {
                    flash.className = 'alert alert-success';
                    flash.innerHTML = `
                        <i class="bi bi-check-circle me-2"></i>
                        Durum başarıyla güncellendi: ${statusMap[data.new_status]}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    flash.style.display = 'block';

                    // 5 saniye sonra otomatik gizle
                    setTimeout(() => {
                        flash.style.display = 'none';
                    }, 5000);
                }

            } catch (err) {
                // Hata durumunda butonu eski haline getir
                clickedButton.disabled = originalDisabled;
                clickedButton.innerHTML = originalContent;

                if (flash) {
                    flash.className = 'alert alert-danger';
                    flash.innerHTML = `
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${err.message || 'Bir hata oluştu.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    flash.style.display = 'block';

                    // 10 saniye sonra otomatik gizle
                    setTimeout(() => {
                        flash.style.display = 'none';
                    }, 10000);
                }
                console.error('Durum güncelleme hatası:', err);
            }
        }

        // Global scope'a ekle
        window.changeStatus = changeStatus;
        window.setButtonsByStatus = setButtonsByStatus;
    </script>
@endpush