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
  {{-- SAĞ BLOK --}}
            <div class="nav-actions">
                <div class="nav-buttons">
                    <button id="backBtn" class="back-btn hidden">
                        <i class="fas fa-arrow-left"></i> Geri
                    </button>
                  {{-- <div class="text-sm text-gray-400 ml-4">
                        <i class="fas fa-qrcode mr-2"></i>
                        {{ $table->name ?? 'Dijital Menü' }}
                    </div> --}}
                </div>

                <button class="btn-secondary btn-call-waiter  sm:inline-flex items-center gap-2"
                    data-url="{{ route('customer.call', ['token' => $table->token]) }}" type="button">
                    <i class="fa-solid fa-bell-concierge"></i>
                    <span>Garson</span>
                </button>

                <button class="mobile-menu-toggle" id="mobileToggle" aria-label="Menüyü Aç/Kapat">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
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
  <a href="{{ route('customer.table.token', ['token' => $table->token, 'view' => 'dashboard']) }}#menu"
     class="btn-primary w-full justify-center">
    <i class="fas fa-plus-circle mr-2"></i>Yeni Sipariş Ver
  </a>
</div>

            </div>
          </div>
        </aside>
      </div>
    @endif
  </main>
<!-- Sepet FAB -->
<div id="cartFab" class="cart-fab">
  <i class="fas fa-shopping-bag"></i>
  <span id="cartBadge" class="cart-badge">0</span>
</div>

<!-- Sepet Modal -->
<div id="cartModal" class="modal">
  <div class="modal-content">
    <div class="flex items-center justify-between mb-6">
      <h3 class="font-playfair text-xl font-bold">Sepetim</h3>
      <button id="closeCart" class="text-gray-400 hover:text-white transition-colors">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <div id="cartItems" class="space-y-4 mb-6"><!-- JS dolduracak --></div>

    <div class="border-t border-gray-700 pt-4">
      <div class="flex items-center justify-between mb-4">
        <span class="font-bold text-lg">Toplam:</span>
        <span id="cartTotal" class="font-bold text-xl text-orange-500">0.00 ₺</span>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <button id="clearCartBtn" class="back-btn justify-center">
          <i class="fa-solid fa-trash-can"></i> Temizle
        </button>
        <button id="orderBtn" class="add-btn justify-center">
          <i class="fas fa-check"></i> Sipariş Ver
        </button>
      </div>
    </div>
  </div>
</div>

<!-- İsim Modali -->
<div id="nameModal" class="modal">
  <div class="modal-content">
    <h3 class="font-playfair text-xl font-bold mb-2">Sipariş için adınızı giriniz</h3>
    <p class="text-gray-400 text-sm mb-3">
      Aynı masada birden fazla kişi sipariş verebilir. Hazırlık ve servis için isminize ihtiyaç duyuyoruz.
    </p>
    <input id="nameInput" type="text"
           class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 mb-3"
           placeholder="Örn: Mehmet" maxlength="100"/>
    <div class="grid grid-cols-2 gap-3">
      <button id="nameCancel" class="back-btn justify-center">İptal</button>
      <button id="nameConfirm" class="add-btn justify-center">Devam Et</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg hidden">
  Siparişiniz başarıyla iletildi.
</div>

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
  (function setupGlobalCallWaiter(){
  function _csrfHeader(){
    const el = document.querySelector('meta[name="csrf-token"]');
    return { 'X-CSRF-TOKEN': el ? el.content : '', 'Accept':'application/json','Content-Type':'application/json' };
  }
  function _toast(msg){
    if (typeof showToast === 'function') return showToast(msg);
    // basit fallback
    try {
      let t = document.getElementById('toast');
      if (!t) { alert(msg); return; }
      t.textContent = msg || 'İşlem başarılı';
      t.classList.remove('hidden'); setTimeout(()=>t.classList.add('hidden'), 2000);
    } catch(_) { alert(msg); }
  }
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-call-waiter');
    if (!btn) return;

    if (window.btnMarkBusy) btnMarkBusy(btn, '<i class="fa-solid fa-bell-concierge fa-shake"></i> Çağırılıyor...');
    else btn.disabled = true;

    try {
      const url = btn.dataset.url;
      await fetch(url, { method:'POST', headers:_csrfHeader() });
      _toast('Garson çağrınız iletildi. Lütfen bekleyiniz.');
    } catch(_) {
      _toast('Bir sorun oldu, tekrar dener misiniz?');
    } finally {
      if (window.btnClearBusy) btnClearBusy(btn);
      else btn.disabled = false;
    }
  });
})();
  /* -------- Busy helpers -------- */
  function btnMarkBusy(btn, html) {
    if (!btn) return;
    if (!btn.dataset._oldHtml) btn.dataset._oldHtml = btn.innerHTML;
    btn.classList.add('is-busy');
    btn.setAttribute('aria-disabled', 'true');
    btn.disabled = true;
    if (html) btn.innerHTML = html;
  }
  function btnClearBusy(btn) {
    if (!btn) return;
    btn.disabled = false;
    btn.removeAttribute('aria-disabled');
    btn.classList.remove('is-busy', 'is-success');
    if (btn.dataset._oldHtml) {
      btn.innerHTML = btn.dataset._oldHtml;
      delete btn.dataset._oldHtml;
    }
  }
  // Ekle butonu için: 2 sn kilitle + ✓ göster, sonra geri al
  function flashAddSuccess(btn, duration = 2000) {
    btnMarkBusy(btn, '<i class="fas fa-check"></i> Eklendi');
    btn.classList.add('is-success');
    setTimeout(() => btnClearBusy(btn), duration);
  }

  /* -------- (Mevcut) Mobile menu -------- */
  const mobileToggle = document.getElementById('mobileToggle');
  const navMenu = document.getElementById('navMenu');
  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => navMenu.classList.toggle('active'));
    document.querySelectorAll('#navMenu a').forEach(a => a.addEventListener('click', ()=>navMenu.classList.remove('active')));
  }

  /* -------- (Mevcut) Fade-in -------- */
  (function observeFadeIns(){
    const io = new IntersectionObserver(es => es.forEach(en => en.isIntersecting && en.target.classList.add('visible')),
      {threshold:0.1, rootMargin:'0px 0px -50px 0px'});
    document.querySelectorAll('.fade-in').forEach(el=>io.observe(el));
  })();

  /* -------- (Mevcut) Auto-refresh -------- */
  setInterval(()=>{ if(document.visibilityState==='visible'){ window.location.reload(); } }, 30000);

  /* =======================================================================
     EKLENTİ: Modal Sepet + İsim Sor + Checkout sırasında buton kilitle
     (orders.blade için dashboard/menu ile tutarlı çalışır)
     ======================================================================= */

  // ---- Backend sabitleri ----
  const TOKEN = @json($table->token);
  const ROUTES = {
    items:    @json(route('customer.cart.items', ['token' => $table->token])),
    add:      @json(route('customer.cart.add',    ['token' => $table->token])),
    remove:   @json(route('customer.cart.remove', ['token' => $table->token, 'productId' => '__ID__'])),
    clear:    @json(route('customer.cart.clear',  ['token' => $table->token])),
    checkout: @json(route('customer.checkout',    ['token' => $table->token])),
    call:     @json(route('customer.call',        ['token' => $table->token])),
  };

  function csrfHeader() {
    const t = document.querySelector('meta[name="csrf-token"]')?.content || '';
    return { 'X-CSRF-TOKEN': t, 'Accept': 'application/json', 'Content-Type': 'application/json' };
  }
  function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return; // sayfada toast yoksa sessiz geç
    t.textContent = msg || 'İşlem başarılı';
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 2000);
  }
// Ürünü TAMAMEN kaldır (mevcut /remove ucu 1 azaltıyor; qty kadar çağırıyoruz)
async function removeItemAll(productId){
  const it = cart.find(i => String(i.id) === String(productId));
  if(!it) return;
  for(let k=0; k<it.quantity; k++){
    await fetch(ROUTES.remove.replace('__ID__', productId), {
      method:'POST', headers: csrfHeader()
    }).catch(()=>{});
  }
  await loadCartFromServer();
  updateCartUI();
}

  // ---- İsim modalı ----
  function openNameModal(resolve) {
    const modal = document.getElementById('nameModal');
    const input = document.getElementById('nameInput');
    if (!modal || !input) return resolve(null);

    modal.classList.add('active');
    try { input.value = (localStorage.getItem('customer_name') || '').trim(); } catch(_) {}
    input.focus();

    const confirmBtn = document.getElementById('nameConfirm');
    const cancelBtn  = document.getElementById('nameCancel');

    function cleanup(){
      confirmBtn.removeEventListener('click', onConfirm);
      cancelBtn.removeEventListener('click', onCancel);
      modal.removeEventListener('click', onBackdrop);
    }
    function onConfirm(){
      const name = (input.value || '').trim();
      if (name.length < 2) { input.focus(); input.select(); return; }
      try { localStorage.setItem('customer_name', name); } catch(_){}
      modal.classList.remove('active'); cleanup(); resolve(name);
    }
    function onCancel(){ modal.classList.remove('active'); cleanup(); resolve(null); }
    function onBackdrop(e){ if (e.target === modal) onCancel(); }

    confirmBtn.addEventListener('click', onConfirm);
    cancelBtn.addEventListener('click', onCancel);
    modal.addEventListener('click', onBackdrop);
  }
  function askName(){ return new Promise((resolve)=> openNameModal(resolve)); }

  // ---- Sepet state + DOM ----
  let cart = [];
  const cartFab      = document.getElementById('cartFab');
  const cartModal    = document.getElementById('cartModal');
  const cartItems    = document.getElementById('cartItems');
  const cartBadge    = document.getElementById('cartBadge');
  const cartTotal    = document.getElementById('cartTotal');
  const closeCartBtn = document.getElementById('closeCart');
  const clearCartBtn = document.getElementById('clearCartBtn');
  const orderBtnEl   = document.getElementById('orderBtn');

  async function loadCartFromServer() {
    try {
      const res = await fetch(ROUTES.items);
      const data = await res.json();
      const items = Array.isArray(data.items) ? data.items : [];
      cart = items.map(x => ({ id: x.id, name: x.name, price: x.price, quantity: x.quantity }));
    } catch(_) {}
  }

  function updateCartUI() {
    if (!cartBadge || !cartItems || !cartTotal) return;

    const totalItems = cart.reduce((s,i)=> s + i.quantity, 0);
    const totalPrice = cart.reduce((s,i)=> s + i.quantity * Number(i.price||0), 0);

    cartBadge.textContent = totalItems;
    cartTotal.textContent = totalPrice.toFixed(2) + ' ₺';

    cartItems.innerHTML = '';
    if (totalItems === 0) {
      cartItems.innerHTML = '<p class="text-gray-400 text-center py-8">Sepetiniz boş</p>';
      return;
    }

 cart.forEach(item => {
  const row = document.createElement('div');
  row.className = 'product-card cart-item';
  row.innerHTML = `
    <div class="p-4 flex items-center justify-between gap-4">
      <div>
        <h4 class="font-playfair font-bold text-base md:text-lg mb-1">${item.name}</h4>
        <p class="text-orange-500 font-bold">${Number(item.price).toFixed(2)} ₺</p>
      </div>

      <div class="flex items-center gap-3">
        <button class="item-remove" data-id="${item.id}" aria-label="Ürünü kaldır">
          <i class="fa-solid fa-trash-can"></i>
        </button>
        <div class="quantity-control">
          <button class="quantity-btn" data-action="minus" data-id="${item.id}">−</button>
          <span class="px-3 font-bold">${item.quantity}</span>
          <button class="quantity-btn" data-action="plus" data-id="${item.id}">+</button>
        </div>
      </div>
    </div>
  `;
  row.querySelector('[data-action="minus"]').addEventListener('click', () => removeOne(item.id));
  row.querySelector('[data-action="plus"]').addEventListener('click', () => addToCart(item.id, 1));
  row.querySelector('.item-remove').addEventListener('click', () => removeItemAll(item.id));
  cartItems.appendChild(row);
});

  }

  async function addToCart(productId, qty=1) {
    try {
      await fetch(ROUTES.add, { method:'POST', headers: csrfHeader(), body: JSON.stringify({ product_id: productId, qty }) });
    } catch(_) {}
    await loadCartFromServer(); updateCartUI();
  }
  async function removeOne(productId) {
    try {
      await fetch(ROUTES.remove.replace('__ID__', productId), { method:'POST', headers: csrfHeader() });
    } catch(_) {}
    await loadCartFromServer(); updateCartUI();
  }
  async function clearCart() {
    try {
      await fetch(ROUTES.clear, { method:'POST', headers: csrfHeader() });
    } catch(_) {}
    await loadCartFromServer(); updateCartUI();
    if (cartModal) cartModal.classList.remove('active');
  }

  async function checkout() {
    if (!orderBtnEl) return;
    if (cart.length === 0) return;

    btnMarkBusy(orderBtnEl, '<i class="fas fa-spinner fa-spin"></i> Gönderiliyor...');
    try {
      const name = await askName();
      if (!name) return;

      const payload = {
        customer_name: name,
        items: cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
      };

      const res = await fetch(ROUTES.checkout, { method:'POST', headers: csrfHeader(), body: JSON.stringify(payload) });
      if (!res.ok) {
        showToast('Sipariş gönderilemedi. Lütfen tekrar deneyin.');
        return;
      }

      await loadCartFromServer();
      updateCartUI();
      if (cartModal) cartModal.classList.remove('active');
      showToast('Siparişiniz başarıyla iletildi.');
    } finally {
      btnClearBusy(orderBtnEl);
    }
  }

  // ---- Modal bağlama (eleman varsa bağla) ----
  if (closeCartBtn) closeCartBtn.addEventListener('click', ()=> cartModal.classList.remove('active'));
  if (clearCartBtn) clearCartBtn.addEventListener('click', clearCart);
  if (orderBtnEl)   orderBtnEl.addEventListener('click', checkout);
  if (cartFab)      cartFab.addEventListener('click', ()=> cartModal.classList.add('active'));
  if (cartModal)    cartModal.addEventListener('click', (e)=> { if (e.target === cartModal) cartModal.classList.remove('active'); });

  // ---- İlk yükleme ----
  (async function initOrdersCart(){
    // sayfada modal/fab yoksa sessiz geç
    if (!cartFab || !cartModal) return;
    await loadCartFromServer();
    updateCartUI();
    try { cartFab.classList.remove('hidden'); } catch(_) {}
  })();
</script>

</body>
</html>
