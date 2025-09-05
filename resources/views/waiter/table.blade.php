{{-- resources/views/waiter/table.blade.php --}}
@extends('layouts.waiter')

@section('title', 'Masa Yönetimi')

@push('styles')
    <style>
        .order-row {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .order-row:hover {
            background-color: #f8f9fa !important;
        }

        .order-row.table-primary {
            background-color: #cce7ff !important;
            border-left-color: #007bff;
        }

        .status-pending {
            border-left-color: #6c757d;
        }

        .status-preparing {
            border-left-color: #ffc107;
        }

        .status-delivered {
            border-left-color: #28a745;
        }

        .status-paid {
            border-left-color: #28a745;
        }

        .status-cancelled {
            border-left-color: #dc3545;
        }

        .action-panel {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 2px solid #e9ecef;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }

        .btn-action {
            min-height: 45px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-action:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .selected-count {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border-radius: 20px;
            padding: 8px 16px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0, 123, 255, 0.3);
        }

        .table-summary {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .btn-action {
                font-size: 14px;
                padding: 12px;
            }

            .action-panel {
                padding: 15px 10px;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3">
        {{-- Masa Bilgisi ve Özet --}}
        <div class="table-summary">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1 fw-bold">
                        <i class="bi bi-table me-2"></i>
                        {{ $table->name ?? ('#' . $table->id) }}
                    </h4>
                    <p class="text-muted mb-0 small">Aktif sipariş yönetimi</p>
                </div>
                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                    <div class="row g-2">
                        <div class="col-6 col-md-4">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-primary">{{ $flatItems->count() ?? 0 }}</div>
                                <small class="text-muted">Toplam Kalem</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-success">{{ number_format(($flatTotal ?? 0), 2, ',', '.') }} ₺
                                </div>
                                <small class="text-muted">Genel Toplam</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="text-center">
                                <span id="selectedCount" class="selected-count">0 Seçili</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tümünü Seç Butonu --}}
        @if(($flatItems ?? collect())->count())
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input custom-checkbox" type="checkbox" id="selectAll">
                    <label class="form-check-label fw-semibold" for="selectAll">Tümünü Seç</label>
                </div>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary" onclick="selectByStatus('pending')">
                        <i class="bi bi-clock me-1"></i>Bekleyenler
                    </button>
                    <button class="btn btn-outline-warning" onclick="selectByStatus('preparing')">
                        <i class="bi bi-hourglass-split me-1"></i>Hazırlanıyor
                    </button>
                    <button class="btn btn-outline-success" onclick="selectByStatus('delivered')">
                        <i class="bi bi-truck me-1"></i>Teslim Edildi
                    </button>
                </div>
            </div>
        @endif

        {{-- Sipariş Listesi --}}
        <div class="table-responsive" id="ordersContainer">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px"></th>
                        <th style="width:90px">Saat</th>
                        <th>Ürün</th>
                        <th style="width:150px" class="text-center">Adet × Fiyat</th>
                        <th style="width:120px" class="text-end">Tutar</th>
                        <th style="width:160px">Siparişi Veren</th>
                        <th style="width:140px">Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($flatItems ?? collect()) as $i => $item)
                        @php
                            $order = $item->order;
                            $product = $item->product;
                            $unit = $item->price ?? ($product->price ?? 0);
                            $qty = $item->quantity ?? 1;
                            $total = $unit * $qty;
                            $name = trim($order->customer_name ?? '') !== '' ? $order->customer_name : 'Misafir';
                            $created = $item->created_at ?? $order->created_at;
                            $status = (string) ($order->status ?? 'pending');

                            $statusLabels = [
                                'pending' => 'Bekliyor',
                                'preparing' => 'Hazırlanıyor',
                                'delivered' => 'Teslim Edildi',
                                'paid' => 'Ödendi',
                                'cancelled' => 'İptal',
                            ];

                            $statusIcons = [
                                'pending' => 'clock',
                                'preparing' => 'hourglass-split',
                                'delivered' => 'truck',
                                'paid' => 'credit-card',
                                'cancelled' => 'x-circle',
                            ];
                        @endphp
                        <tr class="order-row status-{{ $status }}" data-order-id="{{ $order->id }}" data-status="{{ $status }}">
                            <td>
                                <input class="form-check-input order-checkbox custom-checkbox" type="checkbox"
                                    value="{{ $order->id }}" id="order_{{ $order->id }}_{{ $i }}" data-status="{{ $status }}">
                            </td>
                            <td><small class="text-muted"><i
                                        class="bi bi-clock me-1"></i>{{ optional($created)->format('H:i') }}</small></td>
                            <td>
                                <div class="fw-semibold">{{ $product->name ?? ('Ürün #' . $item->product_id) }}</div>
                                @if(!empty($item->note))
                                    <div class="small text-muted"><i class="bi bi-sticky me-1"></i>{{ $item->note }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $qty }}</span>
                                <span class="text-muted">× {{ number_format($unit, 2, ',', '.') }} ₺</span>
                            </td>
                            <td class="text-end fw-bold text-success">{{ number_format($total, 2, ',', '.') }} ₺</td>
                            <td><span class="badge bg-dark-subtle text-dark">{{ $name }}</span></td>
                            <td>
                                <span
                                    class="badge px-3 py-2 bg-{{ ['pending' => 'secondary', 'preparing' => 'warning', 'delivered' => 'success', 'paid' => 'success', 'cancelled' => 'danger'][$status] ?? 'secondary' }}">
                                    <i class="bi bi-{{ $statusIcons[$status] ?? 'question' }} me-1"></i>
                                    {{ $statusLabels[$status] ?? strtoupper($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <div class="text-muted mt-3">Bu masada aktif sipariş bulunmuyor</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Alt İşlem Paneli --}}
        @if(($flatItems ?? collect())->count())
            <div class="action-panel">
                <div class="container-fluid">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <button class="btn btn-warning w-100 btn-action" id="btnPreparing" disabled>
                                <i class="bi bi-hourglass-split me-1"></i>
                                <span class="d-none d-sm-inline">Hazırlanıyor</span>
                                <span class="d-sm-none">Hazırla</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-success w-100 btn-action" id="btnDelivered" disabled>
                                <i class="bi bi-truck me-1"></i>
                                <span class="d-none d-sm-inline">Teslim Edildi</span>
                                <span class="d-sm-none">Teslim</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-primary w-100 btn-action" id="btnPaid" disabled>
                                <i class="bi bi-credit-card me-1"></i>
                                <span class="d-none d-sm-inline">Ödendi</span>
                                <span class="d-sm-none">Öde</span>
                            </button>
                        </div>
                        <div class="col-6 col-md-3">
                            <button class="btn btn-danger w-100 btn-action" id="btnCancelled" disabled>
                                <i class="bi bi-x-circle me-1"></i>
                                <span class="d-none d-sm-inline">İptal edildi</span>
                                <span class="d-sm-none">İptal</span>
                            </button>
                        </div>
                    </div>

                    {{-- Masa Kapatma Butonu --}}
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-danger" id="btnCloseTable" disabled>
                            <i class="bi bi-door-closed me-1"></i>Masayı Kapat
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectAllCheckbox = document.getElementById('selectAll');
                const selectedCountEl = document.getElementById('selectedCount');

                const btnPreparing = document.getElementById('btnPreparing');
                const btnDelivered = document.getElementById('btnDelivered');
                const btnPaid = document.getElementById('btnPaid');
                const btnCancelled = document.getElementById('btnCancelled');
                const btnCloseTable = document.getElementById('btnCloseTable');

                // Dinamik seçim yardımcıları
                const getOrderCheckboxes = () => Array.from(document.querySelectorAll('.order-checkbox'));
                const getSelected = () => Array.from(document.querySelectorAll('.order-checkbox:checked'));
                const getStatus = (cb) => cb.dataset.status || cb.closest('tr.order-row')?.dataset.status || '';

                // Seçim değişikliklerini dinle
                function updateSelection() {
                    const selected = getSelected();
                    const selectedCount = selected.length;

                    if (selectedCountEl) selectedCountEl.textContent = selectedCount + ' Seçili';

                    // Satır görünümü
                    getOrderCheckboxes().forEach(cb => {
                        const row = cb.closest('tr.order-row');
                        if (row) row.classList.toggle('table-primary', cb.checked);
                    });

                    // Buton durumlarını güncelle
                    updateButtonStates(selected);

                    // Tümünü seç checkbox
                    if (selectAllCheckbox) {
                        const all = getOrderCheckboxes();
                        selectAllCheckbox.checked = selectedCount > 0 && selectedCount === all.length;
                        selectAllCheckbox.indeterminate = selectedCount > 0 && selectedCount < all.length;
                    }
                }

                // Buton durumlarını güncelle
                function updateButtonStates(selected) {
                    const nodes = [btnPreparing, btnDelivered, btnPaid, btnCancelled].filter(Boolean);

                    if (selected.length === 0) {
                        nodes.forEach(btn => btn.disabled = true);
                        if (btnCloseTable) btnCloseTable.disabled = true;
                        return;
                    }

                    const statuses = selected.map(getStatus);
                    const hasPending = statuses.includes('pending');
                    const hasPreparing = statuses.includes('preparing');
                    const hasDelivered = statuses.includes('delivered');
                    const hasPaid = statuses.includes('paid');
                    const hasCancelled = statuses.includes('cancelled');

                    if (btnPreparing) {
                        const dis = !hasPending; // sadece pending -> preparing
                        btnPreparing.disabled = dis;
                        btnPreparing.toggleAttribute('disabled', dis);
                    }
                    if (btnDelivered) {
                        const dis = !hasPreparing; // sadece preparing -> delivered
                        btnDelivered.disabled = dis;
                        btnDelivered.toggleAttribute('disabled', dis);
                    }
                    if (btnPaid) {
                        const dis = !hasDelivered; // sadece delivered -> paid
                        btnPaid.disabled = dis;
                        btnPaid.toggleAttribute('disabled', dis);
                    }
                    // İptal: pending veya preparing olan en az bir kalem seçili olmalı
                    if (btnCancelled) {
                        const hasCancellable = hasPending || hasPreparing;
                        const dis = !hasCancellable || hasPaid || hasCancelled; // paid veya zaten cancelled varsa kapat
                        btnCancelled.disabled = dis;
                        btnCancelled.toggleAttribute('disabled', dis);
                    }

                    // Masa kapatma: tüm kalemler (paid **veya** cancelled) ise aktif
                    const all = getOrderCheckboxes();
                    const closable = all.length > 0 && all.every(cb => ['paid', 'cancelled'].includes(getStatus(cb)));
                    if (btnCloseTable) btnCloseTable.disabled = !closable;
                }

                // Event listeners
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function () {
                        getOrderCheckboxes().forEach(cb => cb.checked = this.checked);
                        updateSelection();
                    });
                }

                function rebindCheckboxListeners() {
                    getOrderCheckboxes().forEach(cb => {
                        cb.removeEventListener('change', updateSelection);
                        cb.addEventListener('change', updateSelection);
                    });
                }
                rebindCheckboxListeners();

                // Durum butonları (linear geçişler)
                [
                    { btn: btnPreparing, from: 'pending', to: 'preparing' },
                    { btn: btnDelivered, from: 'preparing', to: 'delivered' },
                    { btn: btnPaid, from: 'delivered', to: 'paid' }
                ].forEach(({ btn, from, to }) => {
                    if (!btn) return;
                    btn.addEventListener('click', function () {
                        const ids = getSelected()
                            .filter(cb => getStatus(cb) === from)
                            .map(cb => cb.value);
                        if (ids.length > 0) {
                            updateOrderStatus(ids, to);
                        }
                    });
                });

                // İPTAL: pending veya preparing -> cancelled
                if (btnCancelled) {
                    btnCancelled.addEventListener('click', function () {
                        const ids = getSelected()
                            .filter(cb => ['pending', 'preparing'].includes(getStatus(cb)))
                            .map(cb => cb.value);

                        if (ids.length === 0) {
                            alert('İptal edilecek uygun sipariş bulunamadı.');
                            return;
                        }
                        if (!confirm('Seçili sipariş(ler) iptal edilecek. Onaylıyor musunuz?')) return;

                        updateOrderStatus(ids, 'cancelled');
                    });
                }

                // Duruma göre seçim fonksiyonları (UI'dan çağrılıyor)
                window.selectByStatus = function (status) {
                    getOrderCheckboxes().forEach(cb => cb.checked = (getStatus(cb) === status));
                    updateSelection();
                };

                // AJAX ile sipariş durumu güncelleme (tekille + allSettled)
                function updateOrderStatus(orderIds, status) {
                    const ids = Array.from(new Set(orderIds.map(String))); // aynı siparişe 2. istek atma

                    // Loading state
                    document.querySelectorAll('.btn-action').forEach(btn => btn.disabled = true);

                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

                    const calls = ids.map(orderId => fetch(`/waiter/orders/${orderId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ status })
                    }));

                    Promise.allSettled(calls)
                        .then(() => {
                            // Başarılı/başarısız ayrımı yapmadan UI'ı senkronla
                            window.location.reload();
                        })
                        .catch(error => {
                            console.error('Toplu güncelleme hatası:', error);
                            // Localhost'ta alert göstermeyelim
                            const isLocal = ['localhost', '127.0.0.1', '::1'].includes(location.hostname);
                            if (!isLocal) {
                                // alert('Sipariş durumu güncellenirken bir hata oluştu.');
                            }
                            updateSelection();
                        });
                }

                // İlk yüklemede durumu güncelle
                updateSelection();
            });
        </script>

    @endpush
@endsection