@extends('layouts.waiter')

@section('title', $table->name ?? 'Masa')

@section('header_actions')
    <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
        <i class="bi bi-arrow-left me-1"></i> Masalar
    </a>
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

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="mb-0">{{ $table->name }}</h2>
        </div>
        <a href="{{ route('waiter.dashboard') }}" class="btn btn-outline-secondary btn-sm">← Masalara Dön</a>
    </div>

    {{-- Mevcut Sipariş --}}
    @if(!$currentOrder)
        <div class="alert alert-info">Bu masada aktif sipariş bulunmuyor.</div>
    @else
        {{-- CSRF token'ı JS için gizli inputta tutalım --}}
        <input type="hidden" id="csrfToken" value="{{ csrf_token() }}">

        <div class="card mb-3" data-order-id="{{ $currentOrder->id }}">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-clock me-2"></i>Mevcut Sipariş #{{ $currentOrder->id }}
                    <span class="badge bg-light text-dark ms-2" id="order-status-text">
                        {{ $statusMap[$currentOrder->status] ?? strtoupper($currentOrder->status) }}
                    </span>
                </h5>
            </div>

            <div class="card-body">
                {{-- Ürünler --}}
                <ul class="list-group mb-3">
                    @foreach($currentOrder->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $item->product->name ?? 'Ürün' }}</div>
                                <div class="small text-muted">
                                    Adet: x{{ $item->quantity }}
                                    @isset($item->price)
                                        — Fiyat: {{ number_format($item->price, 2) }} ₺
                                    @endisset
                                </div>
                            </div>
                            <div class="fw-bold">
                                {{ number_format($item->line_total ?? (($item->price ?? 0) * ($item->quantity ?? 0)), 2) }} ₺
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">Toplam:</span>
                    <span class="fw-bold fs-5">{{ number_format($currentOrder->total_amount, 2) }} ₺</span>
                </div>

                {{-- Durum geçiş butonları --}}
                <div class="d-flex flex-wrap gap-2">
                    <button id="btn-preparing" class="btn btn-warning" @disabled($currentOrder->status !== 'pending')
                        onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','preparing')">
                        <i class="bi bi-hourglass-split me-1"></i>Hazırlanıyor
                    </button>

                    <button id="btn-delivered" class="btn btn-success" @disabled($currentOrder->status !== 'preparing')
                        onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','delivered')">
                        <i class="bi bi-check-circle me-1"></i>Teslim Edildi
                    </button>

                    <button id="btn-paid" class="btn btn-primary" @disabled(!in_array($currentOrder->status, ['delivered', 'paid']))
                        onclick="changeStatus('{{ route('waiter.orders.status', $currentOrder->id) }}','paid')">
                        <i class="bi bi-credit-card me-1"></i>Ödendi
                    </button>
                </div>

                <div id="status-flash" class="mt-3" style="display:none;"></div>
            </div>
        </div>
    @endif

    {{-- Geçmiş Siparişler --}}
    @if($pastOrders && $pastOrders->count() > 0)
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-archive me-2"></i>Geçmiş Siparişler ({{ $pastOrders->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="pastOrdersAccordion">
                    @foreach($pastOrders as $index => $pastOrder)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                    <div class="d-flex justify-content-between w-100 me-3">
                                        <span>
                                            <strong>Sipariş #{{ $pastOrder->id }}</strong>
                                            <small class="text-muted ms-2">{{ $pastOrder->created_at->format('d.m.Y H:i') }}</small>
                                        </span>
                                        <span class="badge bg-success">{{ number_format($pastOrder->total_amount, 2) }} ₺</span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" 
                                aria-labelledby="heading{{ $index }}" data-bs-parent="#pastOrdersAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        @foreach($pastOrder->items as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div>
                                                    <div class="fw-semibold">{{ $item->product->name ?? 'Ürün' }}</div>
                                                    <div class="small text-muted">Adet: x{{ $item->quantity }}</div>
                                                </div>
                                                <span class="text-muted">{{ number_format($item->line_total, 2) }} ₺</span>
                                            </li>
                                        @endforeach
                                    </ul>
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
        function setButtonsByStatus(st) {
            const btnPreparing = document.getElementById('btn-preparing');
            const btnDelivered = document.getElementById('btn-delivered');
            const btnPaid = document.getElementById('btn-paid');

            // Akış: pending -> preparing -> delivered -> paid
            if (!btnPreparing || !btnDelivered || !btnPaid) return;

            btnPreparing.disabled = (st !== 'pending');
            btnDelivered.disabled = (st !== 'preparing');
            btnPaid.disabled = !(st === 'delivered' || st === 'paid');
        }

        async function changeStatus(url, toStatus) {
            const token = document.getElementById('csrfToken')?.value;
            const flash = document.getElementById('status-flash');
            const statusText = document.getElementById('order-status-text');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        // İstersen form-data da gönderebilirsin; JSON da çalışır
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ to_status: toStatus })
                });

                const data = await res.json();

                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Durum güncellenemedi.');
                }

                // UI güncelle
                statusText.textContent = ({
                    'pending': 'Sipariş Bekliyor',
                    'preparing': 'Hazırlanıyor',
                    'delivered': 'Teslim Edildi',
                    'paid': 'Ödendi'
                })[data.new_status] ?? data.new_status.toUpperCase();

                setButtonsByStatus(data.new_status);

                if (flash) {
                    flash.className = 'alert alert-success';
                    flash.textContent = 'Durum güncellendi.';
                    flash.style.display = 'block';
                }
            } catch (err) {
                if (flash) {
                    flash.className = 'alert alert-danger';
                    flash.textContent = err.message || 'Bir hata oluştu.';
                    flash.style.display = 'block';
                }
                console.error(err);
            }
        }
    </script>
@endpush