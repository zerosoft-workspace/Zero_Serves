@extends('layouts.customer')

@section('title', 'Dashboard')
@section('table_name', $table->name)

@push('styles')
    <style>
        /* Dashboard specific styles */
        .dashboard-section {
            margin-bottom: 2rem;
        }

        .section-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
            color: white;
            padding: 1rem;
            border-radius: 0.5rem 0.5rem 0 0;
            margin-bottom: 0;
        }

        .section-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .section-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            padding: 1.5rem;
            background: white;
        }

        /* Quick actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: inherit;
            text-decoration: none;
        }

        .quick-action-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .quick-action-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .quick-action-desc {
            font-size: 0.875rem;
            color: #6c757d;
        }

        /* Compact product cards for dashboard */
        .dashboard-product-card {
            transition: transform 0.2s ease;
        }

        .dashboard-product-card:hover {
            transform: translateY(-1px);
        }

        .dashboard-product-card .card-img-top {
            height: 120px;
            object-fit: cover;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-content {
                padding: 1rem;
            }
            
            .dashboard-product-card .card-img-top {
                height: 100px;
            }
        }

        @media (max-width: 576px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }
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

    {{-- Quick Actions --}}
    <div class="quick-actions">
        <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-list-ul"></i>
            </div>
            <div class="quick-action-title">Menüyü Görüntüle</div>
            <div class="quick-action-desc">Ürünleri incele ve sepete ekle</div>
        </a>

        <a href="{{ route('customer.table.token', $table->token) }}?view=cart" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-cart3"></i>
            </div>
            <div class="quick-action-title">Sepetim</div>
            <div class="quick-action-desc">
                @if(!empty(session('cart', [])))
                    {{ count(session('cart', [])) }} ürün
                @else
                    Sepet boş
                @endif
            </div>
        </a>

        <a href="{{ route('customer.table.token', $table->token) }}?view=orders" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="quick-action-title">Siparişlerim</div>
            <div class="quick-action-desc">{{ $orders->count() }} aktif sipariş</div>
        </a>

        <form method="POST" action="{{ route('customer.call', $table->token) }}" class="quick-action-card" style="border: none; background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);">
            @csrf
            <button type="submit" style="background: none; border: none; color: inherit; width: 100%; height: 100%; cursor: pointer;">
                <div class="quick-action-icon" style="color: #212529;">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="quick-action-title">Garson Çağır</div>
                <div class="quick-action-desc" style="color: #495057;">Yardım için çağır</div>
            </button>
        </form>
    </div>

    <div class="row">
        {{-- Sol Kolon: Popüler Ürünler --}}
        <div class="col-12 col-lg-8">
            <div class="dashboard-section">
                <div class="section-header">
                    <h4><i class="bi bi-star me-2"></i>Popüler Ürünler</h4>
                </div>
                <div class="section-content">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                        @php
                            $popularProducts = $categories->flatMap->products->take(8);
                        @endphp
                        @forelse($popularProducts as $p)
                            <div class="col">
                                <div class="card h-100 shadow-sm dashboard-product-card">
                                    @if(!empty($p->image))
                                        <img src="{{ asset($p->image) }}" class="card-img-top" alt="{{ $p->name }}" loading="lazy">
                                    @endif
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1 text-truncate" style="font-size: 0.8rem;" title="{{ $p->name }}">{{ $p->name }}</h6>
                                        <div class="fw-bold text-primary mb-2" style="font-size: 0.9rem;">{{ number_format($p->price, 2) }} ₺</div>
                                        
                                        @if($p->stock <= 0)
                                            <span class="badge bg-secondary w-100" style="font-size: 0.7rem;">Stokta yok</span>
                                        @else
                                            <form method="POST" action="{{ route('customer.cart.add', $table->token) }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $p->id }}">
                                                <input type="hidden" name="qty" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size: 0.75rem;">
                                                    <i class="bi bi-plus-circle me-1"></i>Ekle
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center">Henüz ürün bulunmuyor.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sağ Kolon: Sepet ve Siparişler --}}
        <div class="col-12 col-lg-4">
            {{-- Sepet Özeti --}}
            <div class="dashboard-section">
                <div class="section-header">
                    <h4><i class="bi bi-cart3 me-2"></i>Sepetim</h4>
                </div>
                <div class="section-content">
                    @php 
                        $cart = session('cart', []);
                        $sum = 0; 
                    @endphp
                    @if(empty($cart))
                        <p class="text-muted mb-3">Sepetiniz boş.</p>
                        <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list-ul me-1"></i>Menüye Git
                        </a>
                    @else
                        <div class="mb-3">
                            @foreach($cart as $pid => $row)
                                @php $line = $row['price'] * $row['qty']; $sum += $line; @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="font-size: 0.9rem;">{{ $row['name'] }}</div>
                                        <small class="text-muted">{{ $row['qty'] }} x {{ number_format($row['price'], 2) }} ₺</small>
                                    </div>
                                    <div class="fw-bold">{{ number_format($line, 2) }} ₺</div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                            <span class="fw-bold">Toplam</span>
                            <span class="fw-bold fs-5 text-primary">{{ number_format($sum, 2) }} ₺</span>
                        </div>

                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('customer.checkout', $table->token) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-1"></i>Siparişi Ver
                                </button>
                            </form>
                            <a href="{{ route('customer.table.token', $table->token) }}?view=cart" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil me-1"></i>Sepeti Düzenle
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Siparişler Özeti --}}
            <div class="dashboard-section">
                <div class="section-header">
                    <h4><i class="bi bi-clock-history me-2"></i>Aktif Siparişler</h4>
                </div>
                <div class="section-content">
                    @forelse($orders as $order)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                            <div>
                                <div class="fw-semibold">#{{ $order->id }}</div>
                                <small class="text-muted">{{ $order->items->count() }} ürün - {{ number_format($order->total_amount, 2) }} ₺</small>
                            </div>
                            <span class="badge 
                                @if($order->status == 'pending') bg-secondary
                                @elseif($order->status == 'preparing') bg-warning text-dark
                                @elseif($order->status == 'delivered') bg-info
                                @elseif($order->status == 'paid') bg-success
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
                    @empty
                        <p class="text-muted mb-3">Henüz siparişiniz yok.</p>
                        <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list-ul me-1"></i>Sipariş Ver
                        </a>
                    @endforelse

                    @if($orders->count() > 0)
                        <div class="d-grid gap-2 mt-3">
                            <form method="POST" action="{{ route('customer.pay', $table->token) }}">
                                @csrf
                                <button type="submit" class="btn btn-dark w-100">
                                    <i class="bi bi-credit-card me-1"></i>Hesabı Öde
                                </button>
                            </form>
                            <a href="{{ route('customer.table.token', $table->token) }}?view=orders" class="btn btn-outline-secondary">
                                <i class="bi bi-eye me-1"></i>Detayları Gör
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // CSRF token setup
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.Laravel = {
                    csrfToken: token.getAttribute('content')
                };
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);

            // Form submission loading states
            document.addEventListener('submit', function(e) {
                const form = e.target;
                const submitBtn = form.querySelector('button[type="submit"]');
                
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Yükleniyor...';
                    
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                }
            });
        });
    </script>
@endsection
