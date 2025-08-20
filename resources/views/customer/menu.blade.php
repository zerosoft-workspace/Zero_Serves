@extends('layouts.customer')

@section('title', 'Menü')
@section('table_name', $table->name)

@section('content')
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row gx-3 gy-4">
        {{-- Ürünler --}}
        <div class="col-12 col-lg-8">
            @foreach($categories as $idx => $cat)
                <div class="mb-3">
                    <button class="btn w-100 d-flex justify-content-between align-items-center shadow-sm category-toggle"
                        type="button" data-bs-toggle="collapse" data-bs-target="#cat-{{ $idx }}" aria-expanded="true"
                        aria-controls="cat-{{ $idx }}">
                        <span class="fw-semibold">{{ $cat->name }}</span>
                        <span class="small text-muted">{{ $cat->products->count() }} ürün</span>
                    </button>

                    <div class="collapse show mt-2" id="cat-{{ $idx }}">
                        <div class="row row-cols-1 row-cols-md-2 g-3">
                            @forelse($cat->products as $p)
                                <div class="col">
                                    <div class="card h-100 shadow-sm product-card">
                                        @if(!empty($p->image))
                                            <img src="{{ asset($p->image) }}" class="card-img-top" alt="{{ $p->name }}">
                                        @endif
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title mb-1 text-truncate" title="{{ $p->name }}">{{ $p->name }}</h6>
                                            @if($p->description)
                                                <div class="text-muted small mb-2 line-clamp-2">{{ $p->description }}</div>
                                            @endif
                                            <div class="fw-bold mb-3">{{ number_format($p->price, 2) }} ₺</div>

                                            <div class="mt-auto">
                                                @if($p->stock <= 0)
                                                    <span class="badge bg-secondary">Stokta yok</span>
                                                @else
                                                    <form method="POST" action="{{ route('customer.cart.add', $table->token) }}">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $p->id }}">
                                                        <div class="input-group">
                                                            <button class="btn btn-outline-secondary qty-btn" type="button"
                                                                data-step="-1">−</button>
                                                            <input type="number" name="qty" class="form-control text-center" min="1"
                                                                max="{{ $p->stock }}" value="1" inputmode="numeric" pattern="[0-9]*"
                                                                aria-label="Adet">
                                                            <button class="btn btn-outline-secondary qty-btn" type="button"
                                                                data-step="1">+</button>
                                                            <button class="btn btn-primary">Sepete Ekle</button>
                                                        </div>

                                                        @if(($p->low_stock_threshold ?? 0) > 0 && $p->stock <= $p->low_stock_threshold)
                                                            <div class="small text-warning mt-1">Az kaldı ({{ $p->stock }})</div>
                                                        @endif
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Bu kategoride ürün yok.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Sepet --}}
        <div class="col-12 col-lg-4">
            <div id="cart" class="card shadow-sm cart-sticky">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Sepet</strong>
                    <form method="POST" action="{{ route('customer.cart.clear', $table->token) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">Temizle</button>
                    </form>
                </div>
                <div class="card-body">
                    @php $cart = session('cart', []);
                    $sum = 0; @endphp
                    @if(empty($cart))
                        <p class="text-muted mb-0">Sepetiniz boş.</p>
                    @else
                        <ul class="list-group mb-3">
                            @foreach($cart as $pid => $row)
                                @php $line = $row['price'] * $row['qty'];
                                $sum += $line; @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="me-2">
                                        <div class="fw-semibold text-truncate" title="{{ $row['name'] }}">{{ $row['name'] }}</div>
                                        <small class="text-muted">{{ $row['qty'] }} x {{ number_format($row['price'], 2) }}
                                            ₺</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ number_format($line, 2) }} ₺</div>
                                        <form method="POST" action="{{ route('customer.cart.remove', [$table->token, $pid]) }}"
                                            class="mt-1">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger">Sil</button>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Toplam</span>
                            <strong class="fs-5">{{ number_format($sum, 2) }} ₺</strong>
                        </div>
                        <form method="POST" action="{{ route('customer.checkout', $table->token) }}">
                            @csrf
                            <button class="btn btn-success w-100">Siparişi Ver</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Yüzen Sepet Butonu (mobil) --}}
    <a href="#cart" class="floating-cart-btn d-lg-none">
        <span>Sepet</span>
    </a>

    {{-- Basit stil ve JS --}}
    <style>
        .category-toggle {
            background: #fff;
            border: 1px solid #e9ecef;
        }

        .product-card .card-img-top {
            height: 140px;
            object-fit: cover;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cart-sticky {
            position: sticky;
            top: 1rem;
        }

        @media (max-width: 991.98px) {

            /* < lg */
            .cart-sticky {
                position: static;
                top: auto;
            }
        }

        .floating-cart-btn {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 1030;
            background: #198754;
            color: #fff;
            padding: 10px 14px;
            border-radius: 999px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .18);
            text-decoration: none;
            font-weight: 600;
        }

        .qty-btn {
            min-width: 44px;
        }

        .input-group .form-control[type=number] {
            max-width: 90px;
        }
    </style>

    <script>
        // + / − butonları
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.qty-btn');
            if (!btn) return;
            const group = btn.closest('.input-group');
            const input = group.querySelector('input[name="qty"]');
            const step = parseInt(btn.dataset.step || 0, 10);
            const min = parseInt(input.min || 1, 10);
            const max = parseInt(input.max || 999999, 10);
            let val = parseInt(input.value || 1, 10);
            val = isNaN(val) ? 1 : val;
            val += step;
            if (val < min) val = min;
            if (val > max) val = max;
            input.value = val;
        });
    </script>
@endsection