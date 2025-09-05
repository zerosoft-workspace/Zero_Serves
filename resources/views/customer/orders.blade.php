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

  {{-- Global theme (mevcut temanız) --}}
  <link rel="stylesheet" href="{{ asset('css/public.css') }}">

  {{-- CSRF --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    .font-playfair{font-family:'Playfair Display',serif}
    .font-inter{font-family:'Inter',sans-serif}
    :root{--primary:#ff6b35;--primary-dark:#e55a2b;--bg-dark:#0a0a0a;--bg-card:#111214;--text:#fff;--text-muted:#a0a0a0;--border:rgba(255,255,255,.1)}
    body{background:var(--bg-dark);color:var(--text);overflow-x:hidden}
    .header{position:fixed;top:0;width:100%;background:rgba(0,0,0,.95);backdrop-filter:blur(15px);z-index:1000;padding:1rem 0;border-bottom:1px solid rgba(255,107,53,.2);box-shadow:0 4px 30px rgba(0,0,0,.3)}
    .nav{display:flex;justify-content:space-between;align-items:center;max-width:1200px;margin:0 auto;padding:0 20px}
    .logo{font-size:1.8rem;font-weight:bold;color:#ff6b35;text-shadow:0 2px 4px rgba(0,0,0,.3);transition:.3s;text-decoration:none}
    .logo:hover{transform:scale(1.05);color:#ff8c42}
    .nav-menu{display:flex;list-style:none;gap:2.5rem;align-items:center;margin:0;padding:0}
    .nav-menu a{color:#fff;text-decoration:none;transition:.3s cubic-bezier(.4,0,.2,1);position:relative;font-weight:500;letter-spacing:.5px}
    .nav-menu a::after{content:"";position:absolute;bottom:-5px;left:50%;width:0;height:2px;background:linear-gradient(90deg,#ff6b35,#ff8c42);transition:.3s;transform:translateX(-50%)}
    .nav-menu a:hover{color:#ff6b35;transform:translateY(-2px)}
    .nav-menu a:hover::after,.nav-menu a.active::after{width:100%}
    .nav-menu a.active{color:#ff6b35}
    .mobile-menu-toggle{display:none;background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer;padding:.5rem;border-radius:5px;transition:.3s}
    .mobile-menu-toggle:hover{background:rgba(255,107,53,.2);color:#ff6b35}

    .order-card{background:var(--bg-card);border-radius:16px;border:1px solid var(--border);overflow:hidden;transition:.3s;margin-bottom:1.5rem}
    .order-card:hover{transform:translateY(-4px);border-color:rgba(255,107,53,.3);box-shadow:0 12px 30px rgba(0,0,0,.4)}
    .order-header{background:linear-gradient(135deg,rgba(255,107,53,.1),rgba(255,140,66,.05));padding:1.25rem 1.5rem;border-bottom:1px solid var(--border)}
    .status-badge{padding:.5rem 1rem;border-radius:20px;font-size:.875rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase}
    .status-pending{background:rgba(108,117,125,.2);color:#adb5bd}
    .status-preparing{background:rgba(255,193,7,.18);color:#facc15}
    .status-delivered{background:rgba(13,202,240,.18);color:#38bdf8}
    .status-paid{background:rgba(25,135,84,.2);color:#22c55e}

    .order-item{display:flex;justify-content:space-between;align-items:center;padding:1rem 0;border-bottom:1px solid rgba(255,255,255,.06)}
    .order-item:last-child{border-bottom:none}
    .order-item-image{width:60px;height:60px;object-fit:cover;border-radius:12px;margin-right:1rem;background:rgba(255,255,255,.05)}

    .order-timeline{position:relative;padding-left:2rem}
    .order-timeline::before{content:"";position:absolute;left:.5rem;top:0;bottom:0;width:2px;background:rgba(255,255,255,.1)}
    .timeline-item{position:relative;padding-bottom:1rem}
    .timeline-item::before{content:"";position:absolute;left:-.5rem;top:.25rem;width:12px;height:12px;border-radius:50%;background:rgba(255,255,255,.25);border:2px solid var(--bg-card)}
    .timeline-item.active::before{background:#ff6b35;box-shadow:0 0 0 4px rgba(255,107,53,.2)}
    .timeline-item.completed::before{background:#16a34a}
    .summary-card{background:var(--bg-card);border-radius:16px;border:1px solid var(--border);position:sticky;top:100px}
    .btn-primary{background:linear-gradient(135deg,#ff6b35,#ff8c42);border:none;padding:.75rem 1.5rem;border-radius:25px;color:#fff;font-weight:700;display:inline-flex;align-items:center;gap:.5rem;transition:.3s;letter-spacing:.5px;text-transform:uppercase}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(255,107,53,.4)}
    .btn-secondary{background:transparent;border:2px solid rgba(255,255,255,.3);padding:.75rem 1.5rem;border-radius:25px;color:#fff;font-weight:700;display:inline-flex;align-items:center;gap:.5rem;transition:.3s;backdrop-filter:blur(10px)}
    .btn-secondary:hover{border-color:#ff6b35;color:#ff6b35;transform:translateY(-2px);background:rgba(255,107,53,.08)}
    .btn-dark{background:linear-gradient(135deg,#333,#555);border:none;padding:.75rem 1.5rem;border-radius:25px;color:#fff;font-weight:700;display:inline-flex;align-items:center;gap:.5rem;transition:.3s;width:100%;justify-content:center;letter-spacing:.5px;text-transform:uppercase}
    .btn-dark:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.45)}
    .empty-orders{text-align:center;padding:4rem 1.5rem;color:var(--text-muted)}
    .empty-orders i{font-size:3rem;margin-bottom:1rem;color:#ff6b35;opacity:.8}

    .fade-in{opacity:0;transform:translateY(20px);transition:.6s}
    .fade-in.visible{opacity:1;transform:translateY(0)}
    .pulse{animation:pulse 2s infinite}@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}

    @media (max-width:768px){
      .nav-menu{display:none;position:fixed;top:0;left:0;width:100%;height:100vh;background:rgba(0,0,0,.98);flex-direction:column;justify-content:center;align-items:center;gap:2rem;backdrop-filter:blur(10px);z-index:999}
      .nav-menu.active{display:flex}
      .nav-menu a{font-size:1.5rem;font-weight:300}
      .mobile-menu-toggle{display:block;z-index:1001}
      .summary-card{position:static;margin-top:2rem}
      .order-item{flex-direction:column;align-items:flex-start;gap:.5rem}
      .order-item-image{margin-right:0;margin-bottom:.5rem}
    }
  </style>
</head>

<body class="font-inter">
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
  <main class="container mx-auto px-4 py-6" style="margin-top:100px;">
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
    // Görünür siparişler: cancelled olanları gizle
    $visibleOrders = ($orders ?? collect())->where('status','!=','cancelled');

    // Durum -> badge sınıfı haritası (cancelled emniyet için eklendi)
    $statusClassMap = [
      'pending'   => 'status-badge status-pending',
      'preparing' => 'status-badge status-preparing',
      'delivered' => 'status-badge status-delivered',
      'paid'      => 'status-badge status-paid',
      'cancelled' => 'status-badge status-cancelled',
    ];

    // Zaman çizelgesi adım tamamlama fonksiyonu
    $isCompleted = function($s) {
      return [
        'pending'   => ['received'],
        'preparing' => ['received','preparing'],
        'delivered' => ['received','preparing','delivered'],
        'paid'      => ['received','preparing','delivered','paid'],
        'cancelled' => [], // iptalde ilerleme yok
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

    // Auto-refresh (navbar -> Siparişlerim tıklandığında açılma zaten active)
    setInterval(()=>{ if(document.visibilityState==='visible'){ window.location.reload(); } }, 30000);
  </script>
</body>
</html>
