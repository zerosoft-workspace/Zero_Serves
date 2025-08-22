@extends('layouts.waiter')

@section('title', $table->name ?? 'Masa')

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
        <a href="{{ route(name: 'waiter.dashboard') }}" class="btn btn-outline-secondary btn-sm">← Masalara Dön</a>
    </div>

    @if(!$order)
        <div class="alert alert-info">Bu masada aktif sipariş bulunmuyor.</div>
        @return
    @endif

    <div class="card mb-3">
        <div class="card-header">
            Sipariş #{{ $order->id }} —
            <strong>{{ $statusMap[$order->status] ?? strtoupper($order->status) }}</strong>
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
                            {{ number_format($item->line_total ?? ($item->unit_price * $item->quantity), 2) }}
                        </div>
                    </li>
                @endforeach
            </ul>

            {{-- Toplam --}}
            @isset($order->total)
                <div class="d-flex justify-content-end mb-3">
                    <div class="fs-5">
                        Toplam: <strong>{{ number_format($order->total, 2) }}</strong>
                    </div>
                </div>
            @endisset

            {{-- Tüm durum geçiş butonları --}}
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-warning" onclick="changeStatus({{ $order->id }}, 'preparing')">
                    Hazırlanıyor
                </button>

                <button class="btn btn-success" onclick="changeStatus({{ $order->id }}, 'delivered')">
                    Teslim Edildi
                </button>

                <button class="btn btn-primary" onclick="changeStatus({{ $order->id }}, 'paid')">
                    Ödendi
                </button>
            </div>
        </div>
    </div>
@endsection