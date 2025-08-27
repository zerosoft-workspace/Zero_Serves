@extends('layouts.waiter')

@section('title', 'Masa Yönetimi')

@section('header_actions')
  <div class="d-flex gap-2">
    @if(isset($activeCalls) && $activeCalls->count() > 0)
      <a href="{{ route('waiter.calls') }}" class="btn btn-sm btn-danger position-relative">
        <i class="bi bi-bell me-1"></i> 
        <span class="d-none d-sm-inline">Çağrılar</span>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
          {{ $activeCalls->count() }}
        </span>
      </a>
    @else
      <a href="{{ route('waiter.calls') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-bell me-1"></i> 
        <span class="d-none d-sm-inline">Çağrılar</span>
      </a>
    @endif
    <button onclick="location.reload()" class="btn btn-sm btn-ghost">
      <i class="bi bi-arrow-clockwise me-1"></i> 
      <span class="d-none d-sm-inline">Yenile</span>
    </button>
  </div>
@endsection

@section('content')
  @php
    $statusMap = [
      'pending'    => 'Sipariş Bekliyor',
      'preparing'  => 'Hazırlanıyor',
      'delivered'  => 'Teslim Edildi',
      'paid'       => 'Ödendi',
    ];
    
    // Hızlı istatistikler
    $totalTables = $tables ? count($tables) : 0;
    $activeTables = $tables ? collect($tables)->where('active_order')->count() : 0;
    $pendingOrders = $tables ? collect($tables)->where('active_order.status', 'pending')->count() : 0;
    $preparingOrders = $tables ? collect($tables)->where('active_order.status', 'preparing')->count() : 0;
  @endphp

  {{-- Hızlı İstatistikler --}}
  <div class="quick-stats mb-4">
    <div class="stat-card">
      <div class="stat-number text-primary">{{ $totalTables }}</div>
      <div class="stat-label">Toplam Masa</div>
    </div>
    <div class="stat-card">
      <div class="stat-number text-success">{{ $activeTables }}</div>
      <div class="stat-label">Aktif Masa</div>
    </div>
    <div class="stat-card">
      <div class="stat-number text-danger">{{ $pendingOrders }}</div>
      <div class="stat-label">Bekleyen Sipariş</div>
    </div>
    <div class="stat-card">
      <div class="stat-number text-warning">{{ $preparingOrders }}</div>
      <div class="stat-label">Hazırlanan Sipariş</div>
    </div>
  </div>

  {{-- Filtre / Arama --}}
  <div class="filter-bar">
    <form method="GET" action="{{ route('waiter.dashboard') }}" class="row g-3 align-items-end">
      <div class="col-12 col-md-6">
        <label for="search" class="form-label fw-semibold">Masa Ara</label>
        <input type="text" id="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Masa adı veya numarası...">
      </div>
      <div class="col-6 col-md-3">
        <label for="status" class="form-label fw-semibold">Durum Filtresi</label>
        <select id="status" name="status" class="form-select">
          <option value="">Tüm Durumlar</option>
          <option value="pending" @selected(request('status')==='pending')>Sipariş Bekliyor</option>
          <option value="preparing" @selected(request('status')==='preparing')>Hazırlanıyor</option>
          <option value="delivered" @selected(request('status')==='delivered')>Teslim Edildi</option>
          <option value="paid" @selected(request('status')==='paid')>Ödendi</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-search me-1"></i> Filtrele
        </button>
      </div>
    </form>
  </div>

  @if(isset($tables) && count($tables))
    {{-- Masa Kartları Grid --}}
    <div class="table-grid row g-3">
      @foreach($tables as $table)
        @php
          $orderStatus = $table->active_order ? $table->active_order->status : 'empty';
          $statusClass = 'status-' . $orderStatus;
        @endphp
        
        <div class="col-6 col-md-4 col-lg-3">
          <div class="table-card {{ $statusClass }}" onclick="window.location.href='{{ url('/waiter/table/' . $table->id) }}'">
            
            {{-- Header --}}
            <div class="table-card-header">
              <div class="d-flex align-items-center">
                <span class="status-indicator {{ $orderStatus }}"></span>
                <h6 class="mb-0 fw-semibold text-truncate">{{ $table->name }}</h6>
              </div>
              <i class="bi bi-arrow-right text-muted"></i>
            </div>

            {{-- Body --}}
            <div class="table-card-body">
              @if($table->active_order)
                @php $order = $table->active_order; @endphp
                
                {{-- Durum Badge --}}
                @switch($order->status)
                  @case('pending')
                    <span class="badge bg-danger mb-2">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      {{ $statusMap[$order->status] }}
                    </span>
                    @break
                  @case('preparing')
                    <span class="badge bg-warning text-dark mb-2">
                      <i class="bi bi-hourglass-split me-1"></i>
                      {{ $statusMap[$order->status] }}
                    </span>
                    @break
                  @case('delivered')
                    <span class="badge bg-success mb-2">
                      <i class="bi bi-check-circle me-1"></i>
                      {{ $statusMap[$order->status] }}
                    </span>
                    @break
                  @case('paid')
                    <span class="badge bg-secondary mb-2">
                      <i class="bi bi-credit-card me-1"></i>
                      {{ $statusMap[$order->status] }}
                    </span>
                    @break
                @endswitch

                {{-- Sipariş Bilgileri --}}
                <div class="small text-muted mb-2">
                  <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-receipt me-1"></i>
                    <span>Sipariş #{{ $order->id }}</span>
                  </div>
                  <div class="d-flex align-items-center mb-1">
                    <i class="bi bi-clock me-1"></i>
                    <span>{{ $order->created_at->diffForHumans() }}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-currency-exchange me-1"></i>
                    <span class="fw-semibold">{{ number_format($order->total_amount, 2) }} ₺</span>
                  </div>
                </div>
                
              @else
                <span class="badge bg-light text-dark mb-2">
                  <i class="bi bi-circle me-1"></i>
                  Boş Masa
                </span>
                <div class="small text-muted">
                  Aktif sipariş bulunmuyor
                </div>
              @endif
            </div>

            {{-- Footer --}}
            <div class="table-card-footer">
              <div class="d-grid">
                <span class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-arrow-right-circle me-1"></i>
                  Detayları Görüntüle
                </span>
              </div>
            </div>
            
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="text-center py-5">
      <i class="bi bi-inbox display-1 text-muted mb-3"></i>
      <h5 class="text-muted">Kayıtlı masa bulunamadı</h5>
      <p class="text-muted">Henüz hiç masa eklenmemiş veya filtreleme kriterlerinize uygun masa yok.</p>
    </div>
  @endif
@endsection
