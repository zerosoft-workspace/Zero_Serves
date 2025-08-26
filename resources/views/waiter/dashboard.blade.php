{{-- resources/views/waiter/dashboard.blade.php --}}
@extends('layouts.waiter')

@section('title', 'Masalar')

{{-- Üst bar içinde sağ tarafa küçük aksiyon butonu örneği --}}
@section('header_actions')
  <div class="d-flex gap-2">
    @if(isset($activeCalls) && $activeCalls->count() > 0)
      <a href="{{ route('waiter.calls') }}" class="btn btn-sm btn-danger position-relative">
        <i class="bi bi-bell me-1"></i> Çağrılar
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
          {{ $activeCalls->count() }}
        </span>
      </a>
    @else
      <a href="{{ route('waiter.calls') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-bell me-1"></i> Çağrılar
      </a>
    @endif
    <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
      <i class="bi bi-arrow-clockwise me-1"></i> Yenile
    </a>
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
  @endphp

  {{-- Filtre / Arama --}}
  <div class="card shadow-sm mb-3">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('waiter.dashboard') }}" class="row g-2 align-items-center">
        <div class="col-12 col-sm">
          <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Masa adı veya numarası ara...">
        </div>
        <div class="col-6 col-sm-auto">
          <select name="status" class="form-select">
            <option value="">Tüm durumlar</option>
            <option value="pending"   @selected(request('status')==='pending')>Sipariş Bekliyor</option>
            <option value="preparing" @selected(request('status')==='preparing')>Hazırlanıyor</option>
            <option value="delivered" @selected(request('status')==='delivered')>Teslim Edildi</option>
            <option value="paid"      @selected(request('status')==='paid')>Ödendi</option>
          </select>
        </div>
        <div class="col-6 col-sm-auto">
          <button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filtrele</button>
        </div>
      </form>
    </div>
  </div>

  @if(isset($tables) && count($tables))
    {{-- Mobil için 2 sütun, md: 3, lg: 4 --}}
    <div class="row g-3">
      @foreach($tables as $table)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card shadow-sm card-hover h-100">
            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-start justify-content-between mb-2">
                <h5 class="card-title mb-0 text-truncate" title="{{ $table->name }}">{{ $table->name }}</h5>
                <a href="{{ url('/waiter/table/' . $table->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-arrow-right"></i>
                </a>
              </div>

              {{-- Durum rozeti --}}
              @if($table->active_order)
                @php $orderStatus = $table->active_order->status; @endphp
                @switch($orderStatus)
                  @case('pending')   @php $badge='danger'; $txt=$statusMap[$orderStatus]; @endphp @break
                  @case('preparing') @php $badge='warning text-dark'; $txt=$statusMap[$orderStatus]; @endphp @break
                  @case('delivered') @php $badge='success'; $txt=$statusMap[$orderStatus]; @endphp @break
                  @case('paid')      @php $badge='secondary'; $txt=$statusMap[$orderStatus]; @endphp @break
                  @default           @php $badge='dark'; $txt=strtoupper($orderStatus); @endphp
                @endswitch
                <span class="badge bg-{{ $badge }} mb-2">{{ $txt }}</span>

                <div class="small text-white-50">
                  <div><i class="bi bi-receipt-cutoff me-1"></i> #{{ $table->active_order->id }}</div>
                  <div><i class="bi bi-clock me-1"></i> {{ $table->active_order->created_at->diffForHumans() }}</div>
                </div>
              @else
                <span class="badge bg-secondary mb-2">Boş</span>
                <div class="small text-white-50">Aktif sipariş yok</div>
              @endif

              <div class="mt-auto pt-2 d-grid gap-2">
                <a href="{{ url('/waiter/table/' . $table->id) }}" class="btn btn-primary">
                  <i class="bi bi-clipboard2-check me-1"></i> Detaya Git
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div class="alert alert-info">Kayıtlı masa bulunamadı.</div>
  @endif
@endsection
