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

    @if(!$order)
        <div class="alert alert-info">Bu masada aktif sipariş bulunmuyor.</div>
    @else
        {{-- CSRF token'ı JS için gizli inputta tutalım --}}
        <input type="hidden" id="csrfToken" value="{{ csrf_token() }}">

        <div class="card mb-3" data-order-id="{{ $order->id }}">
            <div class="card-header">
                Sipariş #{{ $order->id }} —
                <strong id="order-status-text">
                    {{ $statusMap[$order->status] ?? strtoupper($order->status) }}
                </strong>
            </div>

            <div class="card-body">
                {{-- Ürünler --}}
                <ul class="list-group mb-3">
                    @foreach($order->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $item->product->name ?? 'Ürün' }}</div>
                                <div class="small text-muted">
                                    Adet: x{{ $item->quantity }}
                                    @isset($item->unit_price)
                                        — Fiyat: {{ number_format($item->unit_price, 2) }}
                                    @endisset
                                </div>
                            </div>
                            <div class="fw-bold">
                                {{ number_format($item->line_total ?? (($item->unit_price ?? 0) * ($item->quantity ?? 0)), 2) }}
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Durum geçiş butonları --}}
                <div class="d-flex flex-wrap gap-2">
                    <button id="btn-preparing" class="btn btn-warning" @disabled($order->status !== 'pending')
                        onclick="changeStatus('{{ route('waiter.orders.status', $order->id) }}','preparing')">
                        Hazırlanıyor
                    </button>

                    <button id="btn-delivered" class="btn btn-success" @disabled($order->status !== 'preparing')
                        onclick="changeStatus('{{ route('waiter.orders.status', $order->id) }}','delivered')">
                        Teslim Edildi
                    </button>

                    <button id="btn-paid" class="btn btn-primary" @disabled(!in_array($order->status, ['delivered', 'paid']))
                        onclick="changeStatus('{{ route('waiter.orders.status', $order->id) }}','paid')">
                        Ödendi
                    </button>
                </div>

                <div id="status-flash" class="mt-3" style="display:none;"></div>
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