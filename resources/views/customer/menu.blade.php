@extends('layouts.customer')

@section('title', 'Menü')
@section('table_name', $table->name)

@push('styles')
    <style>
        /* Yapışkan sepet */
        .cart-sticky {
            position: sticky;
            top: 1rem;
        }

        /* Kategori çipleri (yatay kaydırma) */
        .cat-scroller {
            display: flex;
            gap: .5rem;
            overflow-x: auto;
            padding: .25rem 0 .5rem;
            scrollbar-width: thin;
        }

        .cat-chip {
            white-space: nowrap;
        }

        /* Metin kısaltma */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Mobil alt navigasyon */
        .mobile-nav {
            position: sticky;
            bottom: 0;
            z-index: 1030;
            background: var(--bs-body-bg);
            border-top: 1px solid var(--bs-border-color);
            display: none;
        }

        .mobile-nav .container {
            display: flex;
            gap: .5rem;
            padding: .5rem 0;
        }

        .mobile-nav-btn {
            flex: 1;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .15rem;
            padding: .4rem .2rem;
            border-radius: .5rem;
            text-decoration: none;
            border: 1px solid var(--bs-border-color);
            color: inherit;
        }

        .mobile-nav-btn.active {
            background: var(--bs-primary-bg-subtle);
            border-color: var(--bs-primary-border-subtle);
        }

        .cart-count-badge {
            margin-left: .25rem;
        }

        .floating-cart-btn {
            position: fixed;
            right: 1rem;
            bottom: 4.5rem;
            z-index: 1031;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            border-radius: 999px;
            padding: .6rem .9rem;
            background: var(--bs-primary);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .18);
        }

        @media (max-width: 991.98px) {
            .mobile-nav {
                display: block;
            }

            .cart-sticky {
                position: static;
                top: auto;
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

    {{-- Üst arama + kategori çipleri --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form class="mb-2" method="GET" action="{{ request()->url() }}">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Ürün ara…">
                    @if(request('q'))
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary">Temizle</a>
                    @endif
                </div>
            </form>

            @if($categories->count())
                <div class="cat-scroller">
                    <a href="#products"
                        class="btn btn-sm btn-outline-secondary cat-chip {{ request()->has('category') ? '' : 'active' }}">Tümü</a>
                    @foreach($categories as $idx => $cat)
                        <a href="#cat-{{ $idx }}" class="btn btn-sm btn-outline-secondary cat-chip">{{ $cat->name }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="row gx-3 gy-4">
        {{-- Ürünler --}}
        <div class="col-12 col-lg-8" id="products">
            @foreach($categories as $idx => $cat)
                <div class="mb-3" id="cat-{{ $idx }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">{{ $cat->name }}</h5>
                        <small class="text-muted">{{ $cat->products->count() }} ürün</small>
                    </div>

                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                        @forelse($cat->products as $p)
                            <div class="col">
                                <div class="card h-100 shadow-sm product-card">
                                    @if(!empty($p->image))
                                        <img src="{{ asset($p->image) }}" class="card-img-top" alt="{{ $p->name }}" loading="lazy">
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
            @endforeach
        </div>

        {{-- Sepet & Sipariş --}}
        <div class="col-12 col-lg-4">
            {{-- Sepet --}}
            <div id="cart" class="card shadow-sm cart-sticky">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Sepet</strong>
                    <div class="d-flex align-items-center">
                        <span
                            class="badge bg-primary rounded-pill me-2 cart-items-count">{{ count(session('cart', [])) }}</span>
                        <form method="POST" action="{{ route('customer.cart.clear', $table->token) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Temizle</button>
                        </form>
                    </div>
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
                                    <div class="me-2 flex-grow-1">
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

            {{-- Mevcut Siparişler --}}
            <div class="card shadow-sm mt-4" id="orders">
                <div class="card-header"><strong>Mevcut Siparişleriniz</strong></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($orders as $order)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">#{{ $order->id }}</div>
                                    <small class="text-muted">{{ $order->items->count() }} ürün</small>
                                </div>
                                <span class="badge 
                                            @if($order->status == 'pending') bg-secondary
                                            @elseif($order->status == 'in_kitchen') bg-warning text-dark
                                            @elseif($order->status == 'delivered') bg-info
                                            @elseif($order->status == 'paid') bg-success
                                            @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Henüz siparişiniz yok.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Garson Çağır --}}
            <form method="POST" action="{{ route('customer.call', $table->token) }}" class="mt-3">
                @csrf
                <button class="btn btn-warning w-100">🚨 Garson Çağır</button>
            </form>

            {{-- Hesabı Öde --}}
            <form method="POST" action="{{ route('customer.pay', $table->token) }}" class="mt-2">
                @csrf
                <button class="btn btn-dark w-100">💳 Hesabı Öde</button>
            </form>
        </div>
    </div>

    {{-- Mobil Navigasyon --}}
    <div class="mobile-nav">
        <div class="container">
            <a href="#products" class="mobile-nav-btn">
                <i class="bi bi-list-ul"></i><span>Menü</span>
            </a>
            <a href="#cart" class="mobile-nav-btn">
                <i class="bi bi-cart3"></i><span>Sepet</span>
                @if(!empty(session('cart', [])))
                    <span class="badge text-bg-primary cart-count-badge">{{ count(session('cart', [])) }}</span>
                @endif
            </a>
            <a href="#orders" class="mobile-nav-btn">
                <i class="bi bi-clock-history"></i><span>Siparişler</span>
            </a>
        </div>
    </div>

    {{-- Yüzen Sepet Butonu (mobil) --}}
    <a href="#cart" class="floating-cart-btn d-lg-none">
        <i class="bi bi-cart3 me-1"></i>
        <span>Sepet</span>
        @if(!empty(session('cart', [])))
            <span class="badge text-bg-light cart-count-badge">{{ count(session('cart', [])) }}</span>
        @endif
    </a>
@endsection

@section('scripts')
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

        // Mobil navigasyon aktif durumu + smooth scroll
        document.addEventListener('DOMContentLoaded', function () {
            const navButtons = document.querySelectorAll('.mobile-nav-btn');
            const sections = Array.from(['products', 'cart', 'orders']).map(id => document.getElementById(id)).filter(Boolean);

            highlightActiveSection();
            window.addEventListener('scroll', highlightActiveSection, { passive: true });

            function highlightActiveSection() {
                let current = '';
                const y = window.scrollY + 120; // offset
                sections.forEach(sec => {
                    const top = sec.offsetTop, h = sec.offsetHeight;
                    if (y >= top && y < top + h) current = sec.id;
                });
                navButtons.forEach(btn => {
                    btn.classList.toggle('active', btn.getAttribute('href').slice(1) === current);
                });
            }

            navButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('href').slice(1);
                    const el = document.getElementById(id);
                    if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
                });
            });
        });
    </script>
@endsection