@extends('layouts.customer')

@section('title', 'Menü')
@section('table_name', $table->name)

@push('styles')
    <style>
        /* Mobile-first responsive design */
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --border-radius: 0.5rem;
            --shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        /* Yapışkan sepet */
        .cart-sticky {
            position: sticky;
            top: 1rem;
        }

        /* Kategori çipleri (yatay kaydırma) */
        .cat-scroller {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.25rem 0 0.5rem;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        .cat-scroller::-webkit-scrollbar {
            height: 4px;
        }

        .cat-scroller::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .cat-scroller::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .cat-chip {
            white-space: nowrap;
            min-width: fit-content;
        }

        /* Metin kısaltma */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Ürün kartları */
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }

        /* Quantity input improvements */
        .qty-input-group {
            max-width: 120px;
        }

        .qty-btn {
            width: 40px;
            height: 38px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            user-select: none;
        }

        .qty-input {
            text-align: center;
            border-left: 0;
            border-right: 0;
            width: 60px;
        }

        /* Mobil alt navigasyon */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-top: 1px solid var(--bs-border-color);
            display: none;
            padding: 0.5rem;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .mobile-nav .container {
            display: flex;
            gap: 0.5rem;
            padding: 0;
            max-width: 100%;
        }

        .mobile-nav-btn {
            flex: 1;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            padding: 0.5rem 0.25rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            border: 1px solid transparent;
            color: #6c757d;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .mobile-nav-btn:hover,
        .mobile-nav-btn.active {
            background: var(--bs-primary-bg-subtle);
            border-color: var(--bs-primary-border-subtle);
            color: var(--primary-color);
        }

        .mobile-nav-btn i {
            font-size: 1.25rem;
        }

        .cart-count-badge {
            position: absolute;
            top: -5px;
            right: 10px;
            font-size: 0.6rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-cart-btn {
            position: fixed;
            right: 1rem;
            bottom: 5rem;
            z-index: 1031;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 50px;
            padding: 0.75rem 1rem;
            background: var(--primary-color);
            color: #fff;
            text-decoration: none;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .floating-cart-btn:hover {
            background: #0b5ed7;
            color: #fff;
            transform: scale(1.05);
        }

        /* Alert improvements */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            margin-bottom: 1rem;
        }

        /* Card improvements */
        .card {
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .card-header {
            background: rgba(13, 110, 253, 0.1);
            border-bottom: 1px solid rgba(13, 110, 253, 0.2);
            font-weight: 600;
        }

        /* Button improvements */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success {
            background: var(--success-color);
            border-color: var(--success-color);
        }

        /* Mobile optimizations */
        @media (max-width: 991.98px) {
            .mobile-nav {
                display: block;
            }

            .cart-sticky {
                position: static;
                top: auto;
                margin-bottom: 6rem; /* Space for mobile nav */
            }

            .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .product-card .card-img-top {
                height: 150px;
            }

            .floating-cart-btn {
                display: none; /* Hide on mobile, use bottom nav instead */
            }

            /* Larger touch targets */
            .btn {
                min-height: 44px;
            }

            .qty-btn {
                width: 44px;
                height: 44px;
            }

            /* Better spacing on mobile */
            .card-body {
                padding: 1rem 0.75rem;
            }

            .input-group {
                flex-wrap: nowrap;
            }

            .input-group .btn {
                white-space: nowrap;
            }
        }

        @media (max-width: 575.98px) {
            .row.g-3 {
                --bs-gutter-x: 0.75rem;
            }

            .product-card .card-body {
                padding: 0.75rem 0.5rem;
            }

            .card-title {
                font-size: 0.9rem;
            }

            .mobile-nav-btn {
                font-size: 0.7rem;
                padding: 0.4rem 0.2rem;
            }

            .mobile-nav-btn i {
                font-size: 1.1rem;
            }
        }

        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Focus states for accessibility */
        .btn:focus,
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Smooth animations */
        * {
            scroll-behavior: smooth;
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

            {{-- Kategori çipleri --}}
            @if($categories->count())
                <div class="cat-scroller">
                    <a href="#menu"
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
        <div class="col-12 col-lg-8 content-section" id="menu">
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
                                                <form method="POST" action="{{ route('customer.cart.add', $table->token) }}" class="add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                                    <div class="input-group mb-2">
                                                        <button class="btn btn-outline-secondary qty-btn" type="button"
                                                            data-step="-1">−</button>
                                                        <input type="number" name="qty" class="form-control text-center qty-input" min="1"
                                                            max="{{ $p->stock }}" value="1" inputmode="numeric" pattern="[0-9]*"
                                                            aria-label="Adet">
                                                        <button class="btn btn-outline-secondary qty-btn" type="button"
                                                            data-step="1">+</button>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary w-100">Sepete Ekle</button>

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
        <div class="col-12 col-lg-4 content-section" id="cart" style="display: none;">
            {{-- Sepet --}}
            <div class="card shadow-sm cart-sticky">
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
                            <button type="submit" class="btn btn-success w-100">Siparişi Ver</button>
                        </form>

                    @endif
                </div>
            </div>

        </div>

        {{-- Mevcut Siparişler --}}
        <div class="col-12 col-lg-4 content-section" id="orders" style="display: none;">
            <div class="card shadow-sm">
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
            <a href="{{ route('customer.table.token', $table->token) }}" class="mobile-nav-btn">
                <i class="bi bi-house"></i><span>Ana Sayfa</span>
            </a>
            <button type="button" class="mobile-nav-btn nav-toggle-btn active" data-target="menu">
                <i class="bi bi-list-ul"></i><span>Menü</span>
            </button>
            <button type="button" class="mobile-nav-btn nav-toggle-btn" data-target="cart">
                <i class="bi bi-cart3"></i><span>Sepet</span>
                @if(!empty(session('cart', [])))
                    <span class="badge text-bg-primary cart-count-badge">{{ count(session('cart', [])) }}</span>
                @endif
            </button>
            <button type="button" class="mobile-nav-btn nav-toggle-btn" data-target="orders">
                <i class="bi bi-clock-history"></i><span>Siparişler</span>
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // CSRF token setup for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Set up CSRF token for all forms and AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.Laravel = {
                    csrfToken: token.getAttribute('content')
                };
                
                // Add CSRF token to all forms that don't have it
                document.querySelectorAll('form').forEach(form => {
                    if (!form.querySelector('input[name="_token"]')) {
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = window.Laravel.csrfToken;
                        form.appendChild(csrfInput);
                    }
                });
            }

            // Refresh CSRF token periodically to prevent 419 errors
            setInterval(function() {
                fetch('/csrf-token', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.csrf_token) {
                        // Update meta tag
                        const metaTag = document.querySelector('meta[name="csrf-token"]');
                        if (metaTag) {
                            metaTag.setAttribute('content', data.csrf_token);
                        }
                        
                        // Update all form tokens
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.csrf_token;
                        });
                        
                        // Update global Laravel object
                        if (window.Laravel) {
                            window.Laravel.csrfToken = data.csrf_token;
                        }
                    }
                })
                .catch(error => {
                    console.log('CSRF token refresh failed:', error);
                });
            }, 300000); // Refresh every 5 minutes
        });

        // + / − butonları
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.qty-btn');
            if (!btn) return;
            
            e.preventDefault();
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

        // Form submission with loading states
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (submitBtn && !submitBtn.disabled) {
                // Add loading state
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Yükleniyor...';
                
                // Re-enable after 3 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 3000);
            }
        });

        // Navigation toggle system
        document.addEventListener('DOMContentLoaded', function () {
            const navButtons = document.querySelectorAll('.nav-toggle-btn');
            const sections = document.querySelectorAll('.content-section');

            // Show menu by default
            showSection('menu');

            navButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    
                    // Remove active class from all buttons
                    navButtons.forEach(b => b.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    // Show target section
                    showSection(target);
                });
            });

            function showSection(target) {
                sections.forEach(section => {
                    if (section.id === target) {
                        section.style.display = 'block';
                        // For desktop, show as col-12 when cart/orders are active
                        if (target === 'cart' || target === 'orders') {
                            section.className = 'col-12 content-section';
                        } else {
                            section.className = 'col-12 col-lg-8 content-section';
                        }
                    } else {
                        section.style.display = 'none';
                    }
                });
            }

            // Auto-hide alerts after 5 seconds
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Handle network errors and show user-friendly messages
        window.addEventListener('unhandledrejection', function(event) {
            if (event.reason && event.reason.message && event.reason.message.includes('419')) {
                alert('Oturum süresi doldu. Sayfayı yenileyin.');
                window.location.reload();
            }
        });
    </script>
@endsection