@extends('layouts.customer')

@section('title', 'Sepetim')
@section('table_name', $table->name)

@push('styles')
    <style>
        .cart-item {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.375rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quantity-btn:hover {
            background: #f8f9fa;
            border-color: #0d6efd;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }

        .cart-summary {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            position: sticky;
            top: 20px;
        }

        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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
        <h2><i class="bi bi-cart3 me-2"></i>Sepetim</h2>
        <div>
            <a href="{{ route('customer.table.token', $table->token) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-house me-1"></i>Ana Sayfa
            </a>
            <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-primary">
                <i class="bi bi-list-ul me-1"></i>Menüye Dön
            </a>
        </div>
    </div>

    @php 
        $cart = session('cart', []);
        $total = 0;
    @endphp

    @if(empty($cart))
        <div class="empty-cart">
            <i class="bi bi-cart-x"></i>
            <h4>Sepetiniz Boş</h4>
            <p class="mb-4">Henüz sepetinize ürün eklemediniz. Menüden beğendiğiniz ürünleri seçebilirsiniz.</p>
            <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-primary btn-lg">
                <i class="bi bi-list-ul me-2"></i>Menüyü Görüntüle
            </a>
        </div>
    @else
        <div class="row">
            <div class="col-12 col-lg-8">
                {{-- Cart Items --}}
                @foreach($cart as $productId => $item)
                    @php 
                        $lineTotal = $item['price'] * $item['qty'];
                        $total += $lineTotal;
                        $product = App\Models\Product::find($productId);
                    @endphp
                    <div class="cart-item">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                @if($product && !empty($product->image))
                                    <img src="{{ asset($product->image) }}" class="cart-item-image" alt="{{ $item['name'] }}">
                                @else
                                    <div class="cart-item-image bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <h5 class="mb-1">{{ $item['name'] }}</h5>
                                <p class="text-muted mb-2">{{ number_format($item['price'], 2) }} ₺</p>
                                
                                <div class="quantity-controls">
                                    <form method="POST" action="{{ route('customer.cart.add', $table->token) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                        <input type="hidden" name="qty" value="-1">
                                        <button type="submit" class="quantity-btn" {{ $item['qty'] <= 1 ? 'disabled' : '' }}>
                                            <i class="bi bi-dash"></i>
                                        </button>
                                    </form>
                                    
                                    <input type="text" class="quantity-input" value="{{ $item['qty'] }}" readonly>
                                    
                                    <form method="POST" action="{{ route('customer.cart.add', $table->token) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $productId }}">
                                        <input type="hidden" name="qty" value="1">
                                        <button type="submit" class="quantity-btn">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-auto text-end">
                                <div class="fw-bold fs-5 mb-2">{{ number_format($lineTotal, 2) }} ₺</div>
                                <form method="POST" action="{{ route('customer.cart.remove', [$table->token, $productId]) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Clear Cart --}}
                <div class="text-center mt-3">
                    <form method="POST" action="{{ route('customer.cart.clear', $table->token) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Sepeti tamamen boşaltmak istediğinizden emin misiniz?')">
                            <i class="bi bi-trash me-1"></i>Sepeti Boşalt
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                {{-- Cart Summary --}}
                <div class="cart-summary">
                    <h4 class="mb-3"><i class="bi bi-receipt me-2"></i>Sipariş Özeti</h4>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ürün Sayısı:</span>
                        <span>{{ count($cart) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Toplam Adet:</span>
                        <span>{{ array_sum(array_column($cart, 'qty')) }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold fs-5">Toplam:</span>
                        <span class="fw-bold fs-4 text-primary">{{ number_format($total, 2) }} ₺</span>
                    </div>

                    <div class="d-grid gap-2">
                        <form method="POST" action="{{ route('customer.checkout', $table->token) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-check-circle me-2"></i>Siparişi Ver
                            </button>
                        </form>
                        
                        <a href="{{ route('customer.table.token', $table->token) }}?view=menu" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle me-1"></i>Ürün Ekle
                        </a>
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
        });
    </script>
@endsection
