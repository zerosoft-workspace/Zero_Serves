<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SoftFood - Siparişlerim</title>

  {{-- Tailwind & Icons --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  {{-- Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  {{-- Global theme --}}
  <link rel="stylesheet" href="{{ asset('css/public.css') }}">

  {{-- Sayfaya özgü CSS --}}
  <link rel="stylesheet" href="{{ asset('css/qr_customer.css') }}">

  {{-- CSRF --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="font-inter page-orders">
  {{-- Header --}}
  <header class="header">
    <nav class="nav">
      <a href="{{ route('customer.table.token', ['token' => $table->token]) }}" class="logo">SoftFood</a>

      <ul id="navMenu" class="nav-menu">
        <li><a href="{{ route('customer.table.token', ['token' => $table->token, 'view'=>'dashboard']) }}">Menü</a></li>
        <li><a href="{{ route('customer.cart.view', ['token' => $table->token]) }}">Sepetim</a></li>
        <li><a href="{{ route('customer.table.token', ['token' => $table->token, 'view'=>'orders']) }}" class="active">Siparişlerim</a></li>
      </ul>

      <div class="text-sm text-gray-400 hidden sm:block">
        <i class="fas fa-qrcode mr-2"></i>{{ $table->name ?? 'Masa' }}
      </div>

      <button class="mobile-menu-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
      </button>
    </nav>
  </header>

  {{-- Main --}}
  <main class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
      <div>
        <h1 class="font-playfair text-3xl font-bold mb-2">
          <i class="fas fa-clock-rotate-left mr-3 text-orange-500"></i>Siparişlerim
        </h1>
        <p class="text-gray-400">Siparişlerinizi ve durumlarını gerçek zamanlı takip edin</p>
      </div>
      <div class="flex gap-3">
        <a href="{{ route('customer.table.token', ['token'=>$table->token,'view'=>'dashboard']) }}" class="btn-primary">
          <i class="fas fa-utensils"></i> Menü
        </a>
      </div>
    </div>

    @php
      $visibleOrders = ($orders ?? collect())->where('status','!=','cancelled');

      $statusClassMap = [
        'pending'   => 'status-badge status-pending',
        'preparing' => 'status-badge status-preparing',
        'delivered' => 'status-badge status-delivered',
        'paid'      => 'status-badge status-paid',
        'cancelled' => 'status-badge status-cancelled',
      ];

      $isCompleted = function($s) {
        return [
          'pending'   => ['received'],
          'preparing' => ['received','preparing'],
          'delivered' => ['received','preparing','delivered'],
          'paid'      => ['received','preparing','delivered','paid'],
          'cancelled' => [],
        ][$s] ?? [];
      };
    @endphp

    @if($visibleOrders->count() === 0)
      {{-- Empty state --}}
      <div class="empty-orders">
        <i class="fas fa-clock-rotate-left"></i>
        <h3 class="font-playfair text-2xl font-bold mb-2">Henüz Siparişiniz Yok</h3>
        <p class="mb-6 text-gray-400">Menüden beğendiğiniz ürünleri seçerek sipariş verebilirsiniz.</p>
        <a href="{{ route('customer.table.token', ['token'=>$table->token,'view'=>'dashboard']) }}" class="btn-primary text-base px-6 py-3">
          <i class="fas fa-utensils mr-2"></i>Menüyü Görüntüle
        </a>
      </div>
    @else
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Orders List --}}
        <div class="lg:col-span-2">
          @foreach($visibleOrders as $order)
            @php
              $statusClass = $statusClassMap[$order->status] ?? 'status-badge status-pending';
              $completed = $isCompleted($order->status);
            @endphp

            <div class="order-card fade-in">
              <div class="order-header">
                <div class="flex justify-between items-center">
                  <div>
                    <h3 class="font-playfair text-xl font-bold mb-1">Sipariş #{{ $order->id }}</h3>
                    <p class="text-gray-400 text-sm">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                  </div>
                  <span class="{{ $statusClass }}">
                    @switch($order->status)
                      @case('pending')   <i class="fas fa-hourglass-half mr-1"></i> Bekliyor @break
                      @case('preparing') <i class="fas fa-clock mr-1"></i> Hazırlanıyor @break
                      @case('delivered') <i class="fas fa-check-circle mr-1"></i> Teslim Edildi @break
                      @case('paid')      <i class="fas fa-badge-check mr-1"></i> Ödendi @break
                      @default {{ ucfirst($order->status) }}
                    @endswitch
                  </span>
                </div>
              </div>

              <div class="p-6">
                {{-- Items --}}
                <div class="mb-6">
                  @foreach($order->items as $item)
                    <div class="order-item">
                      <div class="flex items-center justify-between w-full gap-3">
                        <div class="flex items-center gap-3 flex-1">
                          <h4 class="font-semibold text-white">
                            {{ $item->product->name ?? 'Ürün bulunamadı' }}
                          </h4>
                          <span class="text-sm text-gray-400">
                            {{ $item->quantity }} × {{ number_format($item->price,2) }} ₺
                          </span>
                        </div>
                        <div class="text-right whitespace-nowrap">
                          <span class="font-bold text-orange-500">
                            {{ number_format($item->line_total,2) }} ₺
                          </span>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>

                {{-- Total --}}
                <div class="flex justify-between items-center p-4 bg-gray-800 rounded-lg mb-6">
                  <span class="font-bold text-lg">Toplam</span>
                  <span class="font-bold text-xl text-orange-500">{{ number_format($order->total_amount,2) }} ₺</span>
                </div>

                {{-- Timeline --}}
                <div class="mt-2">
                  <h5 class="font-semibold mb-3">Sipariş Durumu</h5>
                  <div class="order-timeline">
                    <div class="timeline-item {{ in_array('received',$completed) ? ($order->status==='pending'?'active':'completed') : '' }}">
                      <div class="font-semibold text-white">Sipariş Alındı</div>
                      <div class="text-sm text-gray-400">{{ $order->created_at->format('H:i') }}</div>
                    </div>
                    <div class="timeline-item {{ in_array('preparing',$completed) ? ($order->status==='preparing'?'active':'completed') : '' }}">
                      <div class="font-semibold text-white">Hazırlanıyor</div>
                      @if($order->status!=='pending')
                        <div class="text-sm {{ $order->status==='preparing'?'text-yellow-400':'text-gray-400' }}">
                          <i class="fas fa-utensils mr-1"></i> Mutfakta hazırlanıyor
                        </div>
                      @endif
                    </div>
                    <div class="timeline-item {{ in_array('delivered',$completed) ? ($order->status==='delivered'?'active':'completed') : '' }}">
                      <div class="font-semibold {{ in_array($order->status,['delivered','paid'])?'text-white':'text-gray-400' }}">Teslim Edildi</div>
                      @if(in_array($order->status,['delivered','paid']))
                        <div class="text-sm text-gray-400">Masanıza teslim edildi</div>
                      @endif
                    </div>
                    <div class="timeline-item {{ in_array('paid',$completed) ? 'completed' : '' }}">
                      <div class="font-semibold {{ $order->status==='paid'?'text-white':'text-gray-400' }}">Ödendi</div>
                      @if($order->status==='paid')
                        <div class="text-sm text-gray-400">Ödeme tamamlandı</div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        {{-- Summary --}}
        <aside class="lg:col-span-1">
          <div class="summary-card">
            <div class="p-6 border-b border-gray-700">
              <h4 class="font-playfair text-xl font-bold flex items-center">
                <i class="fas fa-receipt mr-3 text-orange-500"></i>Sipariş Özeti
              </h4>
            </div>
            <div class="p-6">
              <div class="space-y-4 mb-6">
                <div class="flex justify-between">
                  <span class="text-gray-300">Toplam Sipariş:</span>
                  <span class="font-bold text-white">{{ $visibleOrders->count() }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-300">Bekleyen:</span>
                  <span class="px-2 py-1 bg-gray-600 text-gray-200 rounded-full text-sm">{{ $visibleOrders->where('status','pending')->count() }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-300">Hazırlanan:</span>
                  <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded-full text-sm">{{ $visibleOrders->where('status','preparing')->count() }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-300">Teslim Edilen:</span>
                  <span class="px-2 py-1 bg-sky-500/20 text-sky-400 rounded-full text-sm">{{ $visibleOrders->where('status','delivered')->count() }}</span>
                </div>
              </div>

              <div class="border-t border-gray-700 pt-4 mb-6">
                <div class="flex justify-between items-center">
                  <span class="font-bold text-lg">Toplam Tutar:</span>
                  <span class="font-bold text-2xl text-orange-500">{{ number_format($visibleOrders->sum('total_amount'),2) }} ₺</span>
                </div>
              </div>

              @if($visibleOrders->whereIn('status',['delivered'])->count() > 0)
                <form method="POST" action="{{ route('customer.pay', $table->token) }}" class="space-y-3">
                  @csrf
                  <button type="submit" class="btn-dark">
                    <i class="fas fa-credit-card mr-2"></i>Hesabı Öde
                  </button>
                </form>
              @endif

              <div class="mt-3">
                <a href="{{ route('customer.table.token', ['token'=>$table->token,'view'=>'menu']) }}" class="btn-primary w-full justify-center">
                  <i class="fas fa-plus-circle mr-2"></i>Yeni Sipariş Ver
                </a>
              </div>
            </div>
          </div>
        </aside>
      </div>
    @endif
  </main>

  {{-- Alerts (Laravel flash) --}}
  @if(session('success') || session('error'))
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[1100]">
      <div class="px-4 py-3 rounded-xl border
        @if(session('success')) bg-green-600/20 border-green-500 text-green-200
        @else bg-red-600/20 border-red-500 text-red-200 @endif">
        {{ session('success') ?? session('error') }}
      </div>
    </div>
  @endif

  <script>
    // Mobile menu
    const mobileToggle = document.getElementById('mobileToggle');
    const navMenu = document.getElementById('navMenu');
    mobileToggle.addEventListener('click', () => navMenu.classList.toggle('active'));
    document.querySelectorAll('#navMenu a').forEach(a => a.addEventListener('click', ()=>navMenu.classList.remove('active')));

    // Fade-in on view
    (function observeFadeIns(){
      const io = new IntersectionObserver(es => es.forEach(en => en.isIntersecting && en.target.classList.add('visible')),
        {threshold:0.1, rootMargin:'0px 0px -50px 0px'});
      document.querySelectorAll('.fade-in').forEach(el=>io.observe(el));
    })();

    // Auto-refresh
    setInterval(()=>{ if(document.visibilityState==='visible'){ window.location.reload(); } }, 30000);
  </script>
</body>
</html>
