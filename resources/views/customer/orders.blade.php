@extends('layouts.customer')

@section('title', 'Siparişlerim')
@section('table_name', $table->name)

@push('styles')
    <style>
        .order-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .order-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .order-body {
            padding: 1.5rem;
        }

        .order-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.375rem;
            margin-right: 1rem;
        }

        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 1rem;
        }

        .empty-orders {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-orders i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .order-timeline {
            position: relative;
            padding-left: 2rem;
        }

        .order-timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -0.5rem;
            top: 0.25rem;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dee2e6;
        }

        .timeline-item.active::before {
            background: #0d6efd;
        }

        .timeline-item.completed::before {
            background: #198754;
        }
    </style>
@endpush

@section('content')
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Navigation --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clock-history me-2"></i>Siparişlerim</h2>
        <div>
            <a href="{{ route('customer.table.token', $table->token) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-house me-1"></i>Ana Sayfa
            </a>
            <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-primary">
                <i class="bi bi-list-ul me-1"></i>Menü
            </a>
        </div>
    </div>

    @if($orders->count() == 0)
        <div class="empty-orders">
            <i class="bi bi-clock-history"></i>
            <h4>Henüz Siparişiniz Yok</h4>
            <p class="mb-4">Menüden beğendiğiniz ürünleri seçerek sipariş verebilirsiniz.</p>
            <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-primary btn-lg">
                <i class="bi bi-list-ul me-2"></i>Menüyü Görüntüle
            </a>
        </div>
    @else
        <div class="row">
            <div class="col-12 col-lg-8">
                {{-- Orders List --}}
                @foreach($orders as $order)
                    <div class="order-card">
                        <div class="order-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">Sipariş #{{ $order->id }}</h5>
                                    <small class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                                <span class="status-badge 
                                    @if($order->status == 'pending') bg-secondary text-white
                                    @elseif($order->status == 'preparing') bg-warning text-dark
                                    @elseif($order->status == 'delivered') bg-info text-white
                                    @elseif($order->status == 'paid') bg-success text-white
                                    @endif">
                                    @switch($order->status)
                                        @case('pending') Bekliyor @break
                                        @case('preparing') Hazırlanıyor @break
                                        @case('delivered') Teslim Edildi @break
                                        @case('paid') Ödendi @break
                                        @default {{ ucfirst($order->status) }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        
                        <div class="order-body">
                            {{-- Order Items --}}
                            <div class="mb-3">
                                @foreach($order->items as $item)
                                    <div class="order-item">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            @if($item->product && !empty($item->product->image))
                                                <img src="{{ asset($item->product->image) }}" class="order-item-image" alt="{{ $item->product->name }}">
                                            @else
                                                <div class="order-item-image bg-light d-flex align-items-center justify-content-center me-3">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold">{{ $item->product ? $item->product->name : 'Ürün bulunamadı' }}</div>
                                                <small class="text-muted">{{ $item->quantity }} x {{ number_format($item->price, 2) }} ₺</small>
                                            </div>
                                        </div>
                                        <div class="fw-bold">{{ number_format($item->line_total, 2) }} ₺</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Order Total --}}
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="fw-bold">Toplam</span>
                                <span class="fw-bold fs-5 text-primary">{{ number_format($order->total_amount, 2) }} ₺</span>
                            </div>

                            {{-- Order Timeline --}}
                            <div class="mt-3">
                                <h6 class="mb-2">Sipariş Durumu</h6>
                                <div class="order-timeline">
                                    <div class="timeline-item {{ $order->status == 'pending' ? 'active' : ($order->status != 'pending' ? 'completed' : '') }}">
                                        <strong>Sipariş Alındı</strong>
                                        <div class="text-muted small">{{ $order->created_at->format('H:i') }}</div>
                                    </div>
                                    <div class="timeline-item {{ $order->status == 'preparing' ? 'active' : ($order->status == 'delivered' || $order->status == 'paid' ? 'completed' : '') }}">
                                        <strong>Hazırlanıyor</strong>
                                        @if($order->status != 'pending')
                                            <div class="text-muted small">Mutfakta hazırlanıyor</div>
                                        @endif
                                    </div>
                                    <div class="timeline-item {{ $order->status == 'delivered' ? 'active' : ($order->status == 'paid' ? 'completed' : '') }}">
                                        <strong>Teslim Edildi</strong>
                                        @if($order->status == 'delivered' || $order->status == 'paid')
                                            <div class="text-muted small">Masanıza teslim edildi</div>
                                        @endif
                                    </div>
                                    <div class="timeline-item {{ $order->status == 'paid' ? 'completed' : '' }}">
                                        <strong>Ödendi</strong>
                                        @if($order->status == 'paid')
                                            <div class="text-muted small">Ödeme tamamlandı</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12 col-lg-4">
                {{-- Order Summary --}}
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Sipariş Özeti</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Toplam Sipariş:</span>
                            <span class="fw-bold">{{ $orders->count() }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Bekleyen:</span>
                            <span class="badge bg-secondary">{{ $orders->where('status', 'pending')->count() }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Hazırlanan:</span>
                            <span class="badge bg-warning text-dark">{{ $orders->where('status', 'preparing')->count() }}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Teslim Edilen:</span>
                            <span class="badge bg-info">{{ $orders->where('status', 'delivered')->count() }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Toplam Tutar:</span>
                            <span class="fw-bold fs-5 text-primary">{{ number_format($orders->sum('total_amount'), 2) }} ₺</span>
                        </div>

                        @if($orders->whereIn('status', ['delivered'])->count() > 0)
                            <div class="d-grid gap-2">
                                <form method="POST" action="{{ route('customer.pay', $table->token) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-dark w-100">
                                        <i class="bi bi-credit-card me-2"></i>Hesabı Öde
                                    </button>
                                </form>
                            </div>
                        @endif

                        <div class="d-grid gap-2 mt-2">
                            <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-1"></i>Yeni Sipariş Ver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Auto refresh every 30 seconds to update order status
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    window.location.reload();
                }
            }, 30000);
        });
    </script>
@endsection
